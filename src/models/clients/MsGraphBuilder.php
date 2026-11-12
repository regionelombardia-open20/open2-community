<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\community\models\clients;

use open20\amos\comments\models\CommentMsTeams;
use open20\amos\community\exceptions\msgraph\MsGraphException;
use open20\amos\community\models\clients\msmodels\MsCalendarEvent;
use open20\amos\community\models\clients\msmodels\MsChannel;
use open20\amos\community\models\clients\msmodels\MsChannelMessage;
use open20\amos\community\models\clients\msmodels\MsDriveItem;
use open20\amos\community\models\clients\msmodels\MsFilesFolder;
use open20\amos\community\models\clients\msmodels\MsLicense;
use open20\amos\community\models\clients\msmodels\MsOrganization;
use open20\amos\community\models\clients\msmodels\MsPlannerPlan;
use open20\amos\community\models\clients\msmodels\MsPlannerPlanDetail;
use open20\amos\community\models\clients\msmodels\MsPlannerTask;
use open20\amos\community\models\clients\msmodels\MsSubscription;
use open20\amos\community\models\clients\msmodels\MsTeam;
use open20\amos\community\models\clients\msmodels\MsTeamMember;
use open20\amos\community\models\clients\msmodels\MsUser;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\user\User;
use open20\amos\cwh\models\CwhConfig;
use open20\amos\cwh\models\CwhConfigContents;
use open20\amos\cwh\models\CwhPubblicazioni;
use open20\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use open20\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use open20\amos\documenti\models\Documenti;
use open20\amos\documenti\models\DocumentiCategorie;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class MsGraphBuilder extends Model
{

    public $useOnlyApplicationToken = false;

    /** @var $client MsTeamsClient */
    public $client;

    public function init()
    {
        parent::init();
        $client = new MsTeamsClient();
        $client->useOnlyApplicationToken = $this->useOnlyApplicationToken;
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getTenantId()
    {
        return $this->client->tenantId;
    }

    public function listAllTeams()
    {
        try {
            return $this->client->listAllTeams();
        } catch (\Exception $e) {
            echo 'Errore: ' . $e->getMessage();
            return null;
        }
    }

    public function createTeam($teamName, $description)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $msUserId = $this->getMsGraphUserId();
            $res = $this->client->createTeam($teamName, $description, $msUserId);
            if ($this->client->error) {
            }
            return $res;

        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getMsGraphUserId($user_id = null)
    {
        try {
            if (empty($user_id)) {
                $user_id = \Yii::$app->user->id;
            }
            $token = CommunityMsgraphTokens::find()->andWhere(['user_id' => $user_id])->one();
            if ($token) {
                return $token->msgraph_user_id;
            }
            return null;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $communityUserMm CommunityUserMm
     * @param $teamId
     * @param $user_id (User.id)
     * @param $roles
     * @return MsTeamMember|null
     */
    public function addMemberToTeam($communityUserMm, $teamId, $user_id = null, $roles = [])
    {
        if (empty($user_id)) {
            $user_id = \Yii::$app->user->id;
        }
        $msUserId = $this->getMsGraphUserId($user_id);
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->addMemberToTeam($teamId, $msUserId, $roles);
            $teamMember = new MsTeamMember($resp);
            if (!empty($teamMember->id)) {
                $communityUserMm->msgraph_membership_id = $teamMember->id;
                $communityUserMm->save(false);
            }
            return $teamMember;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $user_id
     * @param $type
     * @return mixed|null
     */
    public function addMemberToChannel($communityUserMm, $teamId, $channelId, $user_id, $type = MsTeamMember::ROLE_MEMBER)
    {
        $msUserId = $this->getMsGraphUserId($user_id);
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->addMemberToChannel($teamId, $channelId, $msUserId, $type);
            if ($this->client->getMsError()) {
                return null;
            }
            $teamMember = new MsTeamMember($resp);
            if (!empty($teamMember->id)) {
                $communityUserMm->msgraph_membership_id = $teamMember->id;
                $communityUserMm->save(false);
            }
            return $teamMember;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $membershipId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeMemberFromChannel($teamId, $channelId, $membershipId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->removeMemberFromChannel($teamId, $channelId, $membershipId);
            if ($this->client->getMsError()) {
                return null;
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $membershipId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeMemberFromTeam($teamId, $membershipId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->removeMemberFromToTeam($teamId, $membershipId);
            if ($this->client->getMsError()) {
                return null;
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getTeam($teamId)
    {
        try {
            $resp = $this->client->getTeam($teamId);
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function deleteTeam($teamId)
    {
        try {

            $this->client->useOnlyApplicationToken = true;
            $this->client->deleteTeam($teamId);
            if ($this->client->getMsError()) {
                return null;
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }


    public function getTeamMember($teamId, $membershipId)
    {
        try {
            return $this->client->getTeamMember($teamId, $membershipId);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }


    public function getGeneralChannel($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $channels = $this->client->getTeamChannels($teamId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return null;
            }
            if (!empty($channels['value']))
                foreach ($channels['value'] as $channelAttributes) {
                    $channel = new MsChannel($channelAttributes);
                    if ($channel->displayName === 'General') {
                        return $channel;
                    }
                }
            return null;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getTeamChannels($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            return $this->client->getTeamChannels($teamId);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getTeamAllChannels($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            return $this->client->getTeamAllChannels($teamId);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return MsFilesFolder|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelFolderInfo($teamId, $channelId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getChannelFolderInfo($teamId, $channelId);
            $folder = new MsFilesFolder($resp);
            return $folder;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getDriveItem($driveId, $itemId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getDriveItem($driveId, $itemId);
            $driveItem = new MsDriveItem($resp);

            return $driveItem;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }


    /**
     * @param $driveId
     * @param $folderId
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFolderChildren($driveId, $folderId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getFolderChildren($driveId, $folderId);
            $items = [];
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $elem) {
                    $driveItem = new MsDriveItem($elem);
                    $items[] = $driveItem;
                }
            }
            return $items;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    /**
     * @param $driveId
     * @param $folderId
     * @param $community
     * @param $cron
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \yii\base\InvalidConfigException
     */
    public function recursiveFolderChildren($driveId, $folderId, $community, $cron = false)
    {
        $allItems = []; // Accumuleremo qui tutti gli elementi (file e cartelle)

        // Otteniamo tutti i file e le cartelle dal folder corrente
        $items = $this->getFolderChildren($driveId, $folderId);

        foreach ($items as $elemItem) {
            // Aggiungiamo l'elemento corrente all'array degli elementi
            $allItems[] = $elemItem;
            $this->saveDocument($elemItem, $community, $folderId, $cron);
            // Se l'elemento è una cartella, chiamiamo la funzione ricorsivamente
            if (!empty($elemItem->folder)) {
                $childItems = $this->recursiveFolderChildren($driveId, $elemItem->id, $community);
                // Uniamo i figli ricorsivi trovati agli elementi totali
                $allItems = array_merge($allItems, $childItems);
            }
        }

        return $allItems; // Restituiamo tutti gli elementi trovati
    }

    /**
     * @param $item MsDriveItem
     * @param $community
     * @param $parentItem
     * @param $cron
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function saveDocument($item, $community, $parentItem = null, $cron = false)
    {
//        pr($item);
        $myfile = fopen("changedrivefolder.txt", "a") or die("Unable to open file!");
        fwrite($myfile, '---START DOC---' . $item->name . ' - ' . $item->id . PHP_EOL);
        $db = \Yii::$app->db;

        // Inizia una transazione
        $transaction = $db->beginTransaction();
        try {

            $creatorUser = $item->getCreatorUser();
            \Yii::$app->user->login($creatorUser);
            /** @var  $document \open20\amos\documenti\models\Documenti */
            $document = \open20\amos\documenti\models\Documenti::find()->andWhere(['msgraph_drive_item_id' => $item->id])->one();

            if (empty($document)) {
                //crea nuovo
                $document = new \open20\amos\documenti\models\Documenti();
                $categoria = DocumentiCategorie::find()->one();
                $document->titolo = $item->name;
                $document->descrizione_breve = "File su Microsoft Teams ";
                $document->msgraph_channel_id = $community->msgraph_channel_id;
                $document->msgraph_drive_item_id = $item->id;
                $document->documenti_categorie_id = $categoria->id;
                $document->type = \open20\amos\documenti\models\Documenti::MAIN_DOCUMENT_TYPE_LINK;
                $document->created_by = $creatorUser ? $creatorUser->id : null;
                $document->created_at = $item->getCreatedAt()->format('Y-m-d H:i');
                $document->data_pubblicazione = $item->getCreatedAt()->format('Y-m-d H:i');
                $document->updated_at = $item->getCreatedAt()->format('Y-m-d H:i');

                if (!empty($parentItem)) {
                    $documentParent = \open20\amos\documenti\models\Documenti::find()->andWhere(['msgraph_drive_item_id' => $parentItem])->one();
                    if ($documentParent) {
                        $document->parent_id = $documentParent->id;
                    }
                }
                if (!empty($item->folder)) {
                    $document->is_folder = true;
                } else {
                    $document->link_document = \Yii::$app->params['platform']['frontendUrl'] . '/community/ms-teams/download-drive-item?communityId=' . $community->id . '&itemId=' . $item->id;
                }

                $document->detachBehaviors();
                $document->status = \open20\amos\documenti\models\Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO;
                $document->save(false);
                $this->saveCwhDocumenti($document, $community);
//                fwrite($myfile, VarDumper::dumpAsString( $document->getErrors()). PHP_EOL);


                $isOk = $this->createDocumentMsTeams($document, $item->id);
                if (!$isOk) {
                    throw new \Exception('Impossibile salvare il Documento Teams. (tabella accessoria)');
                }

                //salvo il path
                $result = \open20\amos\documenti\models\DocumentiCartellePath::generatePath($document, 1, []);
                \open20\amos\documenti\models\DocumentiCartellePath::savePath($result, $document->id);


            } else {
                //aggiornamento
                $document->titolo = $item->name;
                $document->descrizione_breve = "File su Microsoft Teams ";
                $documentParent = \open20\amos\documenti\models\Documenti::find()->andWhere(['msgraph_drive_item_id' => $parentItem])->one();
                $document->msgraph_channel_id = $community->msgraph_channel_id;
                $document->msgraph_drive_item_id = $item->id;

                if ($documentParent) {
                    $document->parent_id = $documentParent->id;
                }
                if (!empty($item->folder)) {
                    $document->is_folder = true;
                } else {
                    $document->link_document = \Yii::$app->params['platform']['frontendUrl'] . '/community/ms-teams/download-drive-item?communityId=' . $community->id . '&itemId=' . $item->id;
                }
                $document->save(false);
                $this->saveCwhDocumenti($document, $community);

                $isOk = $this->createDocumentMsTeams($document, $item->id);
                if (!$isOk) {
                    throw new \Exception('Impossibile salvare il Documento Teams. (tabella accessoria)');
                }
                //salvo il path
                $result = \open20\amos\documenti\models\DocumentiCartellePath::generatePath($document, 1, []);
                \open20\amos\documenti\models\DocumentiCartellePath::savePath($result, $document->id);

            }
            //pr($document->attributes,$item->id);

        } catch (\Exception $e) {
            // Annulla la transazione in caso di errore
            $transaction->rollBack();
            fwrite($myfile, '*** ERROR *** ' . $e->getMessage() . PHP_EOL);
            \Yii::error("Errore nella creazione del documento: " . $e->getMessage());
            return null;
        }
        $transaction->commit();
        fclose($myfile);
    }

    /**
     * @param $document
     * @param $community
     * @return true
     * @throws \Exception
     */
    public function saveCwhDocumenti($document, $community)
    {
        $myfile = fopen("cwh_documenti.txt", "a") or die("Unable to open file!");
        fwrite($myfile, '---START CWH---' . $document->titolo . ' - ' . $document->id . PHP_EOL);
        try {
            $configContent = CwhConfigContents::findOne(['tablename' => 'documenti'])->id;
            $cwhPubblicazioni = CwhPubblicazioni::findOne([
                'cwh_config_contents_id' => $configContent,
                'content_id' => $document->id,
                'cwh_regole_pubblicazione_id' => 3
            ]);
            if (is_null($cwhPubblicazioni)) {
                $cwhPubblicazioni = new CwhPubblicazioni();
                $cwhPubblicazioni->cwh_config_contents_id = $configContent;
                $cwhPubblicazioni->content_id = $document->id;
                $cwhPubblicazioni->cwh_regole_pubblicazione_id = 3;
            }
            $ok = $cwhPubblicazioni->save(false);

            if ($ok) {
                $cwhConfig = CwhConfig::find()->andWhere(['tablename' => 'community'])->one();
                if ($cwhConfig) {
                    $pubblicazioniEditori = CwhPubblicazioniCwhNodiEditoriMm::find()
                        ->andWhere(['cwh_config_id' => $cwhConfig->id])
                        ->andWhere(['cwh_nodi_id' => $community->id . '-community'])
                        ->andWhere(['cwh_network_id' => $community->id])
                        ->andWhere(['cwh_pubblicazioni_id' => $cwhPubblicazioni->id])
                        ->one();
                    if (is_null($pubblicazioniEditori)) {
                        $pubblicazioniEditori = new CwhPubblicazioniCwhNodiEditoriMm();
                        $pubblicazioniEditori->cwh_config_id = $cwhConfig->id;
                        $pubblicazioniEditori->cwh_nodi_id = $community->id . '-community';
                        $pubblicazioniEditori->cwh_network_id = $community->id;
                        $pubblicazioniEditori->cwh_pubblicazioni_id = $cwhPubblicazioni->id;
                        $pubblicazioniEditori->save(false);
                    }
                    $pubblicazioniValidatori = CwhPubblicazioniCwhNodiValidatoriMm::find()
                        ->andWhere(['cwh_config_id' => $cwhConfig->id])
                        ->andWhere(['cwh_nodi_id' => $community->id . '-community'])
                        ->andWhere(['cwh_network_id' => $community->id])
                        ->andWhere(['cwh_pubblicazioni_id' => $cwhPubblicazioni->id])
                        ->one();
                    if (is_null($pubblicazioniValidatori)) {
                        $pubblicazioniValidatori = new CwhPubblicazioniCwhNodiValidatoriMm();
                        $pubblicazioniValidatori->cwh_config_id = $cwhConfig->id;
                        $pubblicazioniValidatori->cwh_nodi_id = $community->id . '-community';
                        $pubblicazioniValidatori->cwh_network_id = $community->id;
                        $pubblicazioniValidatori->cwh_pubblicazioni_id = $cwhPubblicazioni->id;
                        $pubblicazioniValidatori->save(false);
                    }
                }
            }
        } catch (\Exception $e) {
            fwrite($myfile, '*** ERROR *** ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getFile() . ' ' . $e->getTraceAsString() . PHP_EOL);

            throw new \Exception("Errore nella pubblicazione del documento: " . $e->getMessage());
        }
        fclose($myfile);
        return true;
    }

    public function createDocumentMsTeams($document, $driveFileId)
    {
        if (class_exists('open20\amos\documenti\models\DocumentiMsTeams')) {
            try {
                $documentTeams = new \open20\amos\documenti\models\DocumentiMsTeams;
                $documentTeams->documenti_id = $document->id;
                $documentTeams->msgraph_drive_item_id = $driveFileId;
                if (!$documentTeams->save()) {
                    $err = VarDumper::dumpAsString($documentTeams->getErrors(), 4);
//                pr($commentTeams->getErrors());
                    throw new \Exception("#######" . $err);
                }
            } catch (Exception $e) {
//                pr($e->getMessage().' '.$e->getFile().' '.$e->getFile().' '.$e->getTraceAsString());
                return false;
            }
        }
        return true;
    }


    public function subscribeToWebHookMessages()
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $currentDate = new \DateTime();
            $date = $currentDate->add(new \DateInterval('P1D'));
            //$date = $currentDate->modify('+1 day');
            $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-channel-message";
            $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
            $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
            $resp = $this->client->SubscribeToWebHook("/teams/getAllMessages", $notificationUrl, $lifecycleNotificationUrl, $formattedDate);
            if (!empty($this->client->error)) {
                if ($this->client->error->error->code == "ExtensionError") {
                    \Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /teams/getAllMessages");
                    return false;
//                throw new MsGraphException("Sei già iscritto al webHook /teams/getAllMessages");
                }
            }
            return new MsSubscription($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }


    public function subscribeToWebHookChannelMember($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $currentDate = new \DateTime();
            $date = $currentDate->add(new \DateInterval('P1D'));
            //$date = $currentDate->modify('+1 day');
            $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-channel-members";
            $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
            $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
            $resp = $this->client->SubscribeToWebHook("/teams/$teamId/channels/getAllMembers", $notificationUrl, $lifecycleNotificationUrl, $formattedDate, 'created,updated,deleted');
            if (!empty($this->client->error)) {
//                pr($this->client->getMsError());
                if ($this->client->error->error->code == "ExtensionError") {
                    //\Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /teams/{teamId}/channels/getAllMembers");
                    return false;
                }
            }
            return new MsSubscription($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function subscribeToWebHookDriveFolder($msgraph_team_id, $msgraph_channel_id)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $folder = $this->getChannelFolderInfo($msgraph_team_id, $msgraph_channel_id);
            $driveId = $folder->getDriveId();
            $currentDate = new \DateTime();
            $date = $currentDate->add(new \DateInterval('P1D'));
            $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-drive-folder";
            $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
            $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
            $resp = $this->client->SubscribeToWebHook("/drives/{$driveId}/root", $notificationUrl, $lifecycleNotificationUrl, $formattedDate, 'updated');
            if (!empty($this->client->error)) {
                //pr($this->client->getMsError());
                if ($this->client->error->error->code == "ExtensionError") {
                    \Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /drives/{driveid}/root");
                    return false;
//                throw new MsGraphException("Sei già iscritto al webHook /teams/getAllMessages");
                }
            }
            return new MsSubscription($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $subscribtionId
     * @return mixed|null
     */
    public function unsubscribeWebHook($subscriptionId)
    {
        $this->client->useOnlyApplicationToken = true;
        $resp = $this->client->unsubscribeWebHook($subscriptionId);
        if ($this->client->getMsError()) {
            pr($this->client->getMsError(), 'SUB ID -' . $subscriptionId);

        }
        return $resp;
    }

    public function subscribeToWebHookTeamMember($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $currentDate = new \DateTime();
            $date = $currentDate->add(new \DateInterval('P1D'));
            //$date = $currentDate->modify('+1 day');
            $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-team-members";
            $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
            $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
            $resp = $this->client->SubscribeToWebHook("/teams/$teamId/members", $notificationUrl, $lifecycleNotificationUrl, $formattedDate, 'created,updated,deleted');
            if (!empty($this->client->error)) {
                pr($this->client->getMsError());
                if ($this->client->error->error->code == "ExtensionError") {
                    \Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /teams/{teamId}/members");
                    return false;
//                throw new MsGraphException("Sei già iscritto al webHook /teams/getAllMessages");
                }
            }
            return new MsSubscription($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }


    public function subscribeToWebHookTeams()
    {

        $this->client->useOnlyApplicationToken = true;
        $currentDate = new \DateTime();
        $date = $currentDate->add(new \DateInterval('P1D'));
        //$date = $currentDate->modify('+1 day');
        $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-teams";
        $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
        $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
        $resp = $this->client->SubscribeToWebHook("/teams", $notificationUrl, $lifecycleNotificationUrl, $formattedDate, 'created,updated,deleted');
        if (!empty($this->client->error)) {
            if ($this->client->error->error->code == "ExtensionError") {
                \Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /teams");
                return false;
//                throw new MsGraphException("Sei già iscritto al webHook /teams/getAllMessages");
            }
        }
        return new MsSubscription($resp);
    }

    public function subscribeToWebHookChannels()
    {
        $this->client->useOnlyApplicationToken = true;
        $currentDate = new \DateTime();
        $date = $currentDate->add(new \DateInterval('P1D'));
        //$date = $currentDate->modify('+1 day');
        $notificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/change-channels";
        $lifecycleNotificationUrl = \Yii::$app->params['platform']['frontendUrl'] . "/community/ms-teams/lifecycle-notification";
        $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
        $resp = $this->client->SubscribeToWebHook("/teams/getAllChannels", $notificationUrl, $lifecycleNotificationUrl, $formattedDate, 'created,updated,deleted');
        if (!empty($this->client->error)) {
            if ($this->client->error->error->code == "ExtensionError") {
                \Yii::$app->session->addFlash('warning', "Microsoft Teams - Sei già iscritto al webHook /teams/getAllChannels");
                return false;
//                throw new MsGraphException("Sei già iscritto al webHook /teams/getAllMessages");
            }
        }
        return new MsSubscription($resp);
    }

    public function subscriptionList()
    {
        try {
            $subscriptions = [];
            $url = null;
            $this->client->useOnlyApplicationToken = true;
            $i = 0;
//            $resp = $this->client->subscriptionList();
//            pr($resp); die;
            do {
                // Effettua la chiamata per recuperare le sottoscrizioni
                $resp = $this->client->subscriptionList($url);

                if (!empty($resp['value'])) {
                    foreach ($resp['value'] as $attributes) {
                        $subscriptions [] = new MsSubscription($attributes);
                    }
                }

                // Verifica se esiste un link alla pagina successiva
                $url = isset($resp['@odata.nextLink']) ? $resp['@odata.nextLink'] : null;
//$i++;
            } while ($url); // Continua fino a quando non ci sono altre pagine

//            if (!empty($resp['value'])) {
//                foreach ($resp['value'] as $attributes) {
//                    $subscriptions [] = new MsSubscription($attributes);
//                }
//            }
            // pr($i);
            return $subscriptions;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function reauthorizeWebHook($idSubscription)
    {
        $this->client->useOnlyApplicationToken = true;
        $resp = $this->client->reauthorizeWebHook($idSubscription);
        return $resp;
    }

    public function renewSubscriptionWebHook($idSubscription)
    {
        $currentDate = new \DateTime();
        $date = $currentDate->add(new \DateInterval('P1D'));
        $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
        $this->client->useOnlyApplicationToken = true;
        $resp = $this->client->renewSubscriptionWebHook($idSubscription, $formattedDate);
        return $resp;
    }

    public
    function postMessageToChannel($teamId, $channelId, $message)
    {
        try {
            $resp = $this->client->postMessageToChannel($teamId, $channelId, $message);
            return new MsChannelMessage($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function getChannelMessages($teamId, $channelId)
    {
        $messages = [];
        try {
            $resp = $this->client->getChannelMessages($teamId, $channelId);
            foreach ($resp['value'] as $messageAttr) {
                $messages [] = new MsChannelMessage($messageAttr);
            }
            return $messages;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $community Community|null
     * @param $teamId
     * @param $channelId
     * @param $skipLink
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelMessagesDelta($community, $teamId, $channelId, $skipLink = null)
    {
        $messages = [];
//        $myfile = fopen("messagedelta.txt", "a") or die("Unable to open file!");
//        fwrite($myfile,  "MESSAGE DELTA -----\n");

        try {
//            fwrite($myfile, "PROVA ".$community->name. '- '.$community->msgraph_channel_message_nextlink. "-----\n");

            if ($community && !empty($community->msgraph_channel_message_nextlink)) {
                $skipLink = $community->msgraph_channel_message_nextlink;
            }

            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getChannelMessagesDelta($teamId, $channelId, $skipLink);
            if ($this->client->getMsError()) {
            }


            if (!empty($resp['@odata.deltaLink']) || !empty($resp['@odata.nextLink'])) {
//                fwrite($myfile, "LINK - >".$resp['@odata.deltaLink'] . "-----\n");
                $deltaLink = !empty($resp['@odata.deltaLink']) ? $resp['@odata.deltaLink'] : $resp['@odata.nextLink'];
                if ($community) {
                    $community->msgraph_channel_message_nextlink = $deltaLink;
                    $community->save(false);
                }
            }
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $messageAttr) {
                    $message = new MsChannelMessage($messageAttr);
                    if ($message->messageType == 'message') {
                        $messages[] = $message;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
        return $messages;
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $messageId
     * @return array
     */
    public function getChannelMessageReplies($teamId, $channelId, $messageId)
    {
        $messages = [];
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getChannelMessageReplies($teamId, $channelId, $messageId);
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $messageAttr) {
                    $messages [] = new MsChannelMessage($messageAttr);
                }
            }
            return $messages;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    /**
     * @param $community Community
     * @param $teamId
     * @param $channelName
     * @param $description
     * @param $user_id
     * @param $type
     * @return MsChannel|null
     */
    public
    function createChannel($community, $teamId, $channelName, $description, $user_id = null, $type = 'private')
    {
        try {

            if (empty($user_id)) {
                $user_id = \Yii::$app->user->id;
            }
            $msUserId = $this->getMsGraphUserId($user_id);
            if ($msUserId) {
                $this->client->useOnlyApplicationToken = true;
                $resp = $this->client->createChannel($teamId, $channelName, $description, $msUserId, $type);
                if (!empty($this->client->error->error->innerError->code)) {
//                pr($this->client->error->error->innerError->code);
                    $resp = $this->client->createChannel($teamId, $channelName . ' ' . $community->id, $description, $msUserId, $type);
                }
                $channel = new MsChannel($resp);
                if ($community) {
                    $community->msgraph_channel_id = $channel->id;
                    $community->save(false);
//                $moduleCommunity = \Yii::$app->getModule('community');
//                $moduleCommunity->createCommunityUser($community->id, CommunityUserMm::STATUS_ACTIVE, CommunityUserMm::ROLE_COMMUNITY_MANAGER, $user_id);
                }
                return $channel;
            }
            return null;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function updateChannel($teamId, $channelId, $displayName, $description)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->updateChannel($teamId, $channelId, $displayName, $description);
            if ($this->client->getMsError()) {
//            pr($this->client->getMsError());
                return null;
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function deleteChannel($teamId, $channelId)
    {
        try {

            $this->client->useOnlyApplicationToken = true;
            $this->client->deleteChannel($teamId, $channelId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelMembers($teamId, $channelId)
    {
        try {
            $members = [];
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getChannelMembers($teamId, $channelId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }
            foreach ($resp['value'] as $attributes) {
                $member = new MsTeamMember($attributes);
                $members[] = $member;
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return $members;
        }
        return $members;
    }


    public function isUserChannelMember($user_id, $teamId, $channelId)
    {
        $msUserId = $this->getMsGraphUserId($user_id);
        $members = $this->getChannelMembers($teamId, $channelId);
        /** @var  $member MsTeamMember */
        foreach ($members as $member) {
            if ($member->userId == $msUserId) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param $teamId
     * @param $channelId
     * @param $user_id
     * @param $role
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateChannelMemberRole($teamId, $channelId, $userMm, $role = MsTeamMember::ROLE_MEMBER)
    {
        try {
            $member = null;
            $msMemberId = $userMm->msgraph_membership_id;
            if ($msMemberId) {
                $this->client->useOnlyApplicationToken = true;
                $member = $this->client->updateChannelMemberRole($teamId, $channelId, $msMemberId, $role);
                if ($this->client->getMsError()) {
                    pr($this->client->getMsError());
                    return null;
                }
            }
            return $member;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }

    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $user_id
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserChannelMembership($teamId, $channelId, $user_id)
    {
        try {
            $msUserId = $this->getMsGraphUserId($user_id);
            $members = $this->getChannelMembers($teamId, $channelId);
            foreach ($members as $member) {
                if ($member->userId == $msUserId) {
                    return $member;
                }
            }
            return null;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function listAllUsers()
    {
        try {
            return $this->client->listAllUsers();
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function inviteUser($email, $invitedUserDisplayName, $redirectUrl, $invitedUserType = 'Guest')
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            return $this->client->inviteUser($email, $invitedUserDisplayName, $redirectUrl, $invitedUserType);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function getUser($email)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getUser($email);
            if ($this->client->getMsError()) {
                if ($this->client->getMsError()->error->code == 'Request_ResourceNotFound') {
                    return null;
                }
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function getUserById($user_id)
    {
        try {
            $msUserId = $this->getMsGraphUserId($user_id);
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getUserById($msUserId);
            if ($this->client->getMsError()) {
                if ($this->client->getMsError()->error->code == 'Request_ResourceNotFound') {
                    return null;
                }
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public
    function createUser($email, $nameSurname, $password = null)
    {
        try {
            $resp = $this->client->createUser($email, $nameSurname, $password);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }
            $user = new MsUser($resp);
            return $user;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $teamId
     * @param $displayName
     * @param $description
     * @return MsTeam|null
     */
    public function updateTeam($teamId, $displayName, $description)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->updateTeam($teamId, $displayName, $description);
            if ($this->client->getMsError()) {
                return null;
            }
            $team = new MsTeam($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
        return $team;
    }


    public
    function getOrganization()
    {
        try {
            return $this->client->getOrganization();
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getOrganizationDomains()
    {
        $respOrg = $this->getOrganization();
        foreach ($respOrg['value'] as $orgAttr) {
            $organization = new MsOrganization($orgAttr);
            $tenantDomains = $organization->getDomains();
        }
        return $tenantDomains;
    }

    public function getOrganizationsLicenses()
    {
        try {
            $licenses = [];
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getOrganizationsLicenses();
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $licenseattr) {
                    $license = new MsLicense($licenseattr);
                    $licenses[] = $license;
                }
            }
            return $licenses;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function assignLicensetouser($user_id)
    {
        $license = $this->getLicense();
        if ($license->skuId) {
            $this->updateUsageLocation($user_id);
            $resp = $this->assignLicense($user_id, $license->skuId);
            return $resp;
        }
        return null;
    }

    public function assignLicense($user_id, $skuId)
    {
        try {
            $msUserId = $this->getMsGraphUserId($user_id);
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->assignLicense($msUserId, $skuId);
            if ($this->client->error) {
                pr($this->client->error);
                \Yii::$app->session->addFlash('warning', $this->client->error);
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    /**
     * @param $licensetype
     * @return MsLicense|null
     */
    public function getLicense($licensetype = null)
    {
        try {
            if (empty($licensetype)) {
                $licensetype = $this->client->licensetype;
            }
            $licenses = $this->getOrganizationsLicenses();
            foreach ($licenses as $license) {
                if ($license->skuPartNumber == $licensetype) {
                    return $license;
                }
            }
            return null;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function updateUser($user_id, $data)
    {
        try {
            $msUserId = $this->getMsGraphUserId($user_id);
            $this->client->useOnlyApplicationToken = true;
            return $this->client->updateUser($msUserId, $data);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function updateUsageLocation($user_id)
    {
        try {
            $data = [
                'usageLocation' => 'it'
            ];
            return $this->updateUser($user_id, $data);
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function getChannel($teamId, $channelId)
    {
        try {
            $channel = null;
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getChannel($teamId, $channelId);
            if ($this->client->getMsError()) {
                return [];
//                pr($this->client->getMsError());
            }
            $channel = new MsChannel($resp);
            return $channel;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }

    public function listPlans($teamId)
    {
        try {
            $plans = [];
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listPlans($teamId);
            if ($this->client->getMsError()) {
                return [];
            }
//            pr($resp); die;
            foreach ($resp['value'] as $elem) {
                $plans[] = new MsPlannerPlan($elem);
            }
            return $plans;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }

    }

    public function getPlanDetail($planId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getPlanDetail($planId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return [];
            }
            pr($resp);
            die;

            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return null;
        }
    }


    public function listPlanTasks($planId)
    {
        $tasks = [];
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listPlanTasks($planId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return [];
            }
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $item) {
                    $tasks [] = new MsPlannerTask($item);
                }
            }
            return $tasks;
            pr($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function updatePlanTasks($taskId, $etag, $percentComplete = 100)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $data = ['percentComplete' => $percentComplete];
            $resp = $this->client->updatePlanTasks($taskId, $data, $etag);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return [];
            }
            //pr($resp);
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function getPlanTask($taskId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getPlanTask($taskId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return [];
            }
            $task = new MsPlannerTask($resp);
            return $task;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function getPlanTaskEtag($taskId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $etag = $this->client->getPlanTask($taskId, true);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return [];
            }
            return $etag;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    /**
     * @param $planId
     * @return array|mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlannerPlanDetail($planId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getPlannerPlanDetails($planId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }

            return new MsPlannerPlanDetail($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function listCalendarEvents($teamId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listCalendarEvents($teamId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return null;
            }
            pr($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function listMyCalendarEvents($user_id)
    {
        $msUserId = $this->getMsGraphUserId($user_id);
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listMyCalendarEvents($msUserId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return null;
            }
            pr($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function listMyCalendars($user_id)
    {
        $msUserId = $this->getMsGraphUserId($user_id);
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listMyCalendars($msUserId);
            if ($this->client->getMsError()) {
                pr($this->client->getMsError());
                return null;
            }
            pr($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    public function listMyCalendarEventsView($user_id, $startDateTime, $endDateTime)
    {
        $msUserId = $this->getMsGraphUserId($user_id);
        $calendarMeets = [];
        try {
//            pr('ddddd');
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->listMyCalendarEventsView($msUserId, $startDateTime, $endDateTime);
//            pr($resp);
            if (!empty($resp['value'])) {
                foreach ($resp['value'] as $elem) {
                    $calendarMeets [] = new MsCalendarEvent($elem);
                }
            }
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError(),'ddddd');
                return [];
            }
            return $calendarMeets;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }


    public function getPlannerTaskDetails($taskId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getPlannerTaskDetails($taskId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }
            return new MsPlannerTask($resp);
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }

    /**
     * @param $teamId
     * @param $conversationId
     * @return array|void|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlannerTaskConversation($teamId, $conversationId)
    {
        try {
            $this->client->useOnlyApplicationToken = true;
            $resp = $this->client->getPlannerTaskConversationThreads($teamId, $conversationId);
            if ($this->client->getMsError()) {
//                pr($this->client->getMsError());
                return null;
            }
            return $resp;
        } catch (\Exception $e) {
            $this->logError($e);
            return [];
        }
    }


    /**
     * @param $community
     * @return string|null
     */
    public function getLinkChannelFiles($community)
    {
        $this->client->useOnlyApplicationToken = true;
        try {
            $parentCommunity = Community::getCommunityRoot($community);
            $tenantId = $this->getTenantId();
            $linkTeamsFile = "https://teams.microsoft.com/l/channel/{$community->msgraph_channel_id}?groupId={$parentCommunity->msgraph_team_id}&tenantId={$tenantId}&context={\"subEntityId\":\"files\"}";
//            $a = $this->getChannelFolderInfo($parentCommunity->msgraph_team_id, $community->msgraph_channel_id);
//            $linkTeamsFile = $a->webUrl;
//            pr($a); die;
        } catch (\Exception $e) {
            return null;
        }
        return $linkTeamsFile;
    }


    /**
     * @param $ex \Exception
     * @return void
     */
    public function logError($ex)
    {
        \Yii::getLogger()->log($ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine() . "\n" . $ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
    }

}