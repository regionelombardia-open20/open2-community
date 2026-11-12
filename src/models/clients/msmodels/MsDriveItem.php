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

namespace open20\amos\community\models\clients\msmodels;

use open20\amos\community\models\CommunityMsgraphTokens;

/**
 * Class DriveItem
 *
 * Modello che rappresenta un singolo drive item di Microsoft Graph.
 *
 * @var string $microsoftGraphDownloadUrl URL per il download diretto del file da Microsoft Graph
 *
 * @var array $createdBy Informazioni sull'autore della creazione
 * @var array $createdBy['application'] Informazioni sull'applicazione che ha creato l'elemento
 * @var string $createdBy['application']['id'] ID dell'applicazione
 * @var string $createdBy['application']['displayName'] Nome dell'applicazione
 * @var array $createdBy['user'] Informazioni sull'utente che ha creato l'elemento
 * @var string $createdBy['user']['email'] Email dell'autore
 * @var string $createdBy['user']['id'] ID dell'autore
 * @var string $createdBy['user']['displayName'] Nome visualizzato dell'autore
 *
 * @var string $createdDateTime Data e ora di creazione dell'elemento
 * @var string $eTag Tag di versione dell'elemento
 * @var string $id ID dell'elemento
 *
 * @var array $lastModifiedBy Informazioni sull'autore dell'ultima modifica
 * @var array $lastModifiedBy['application'] Informazioni sull'applicazione che ha modificato l'elemento
 * @var string $lastModifiedBy['application']['id'] ID dell'applicazione che ha modificato l'elemento
 * @var string $lastModifiedBy['application']['displayName'] Nome dell'applicazione che ha modificato l'elemento
 * @var array $lastModifiedBy['user'] Informazioni sull'utente che ha modificato l'elemento
 * @var string $lastModifiedBy['user']['email'] Email di chi ha modificato
 * @var string $lastModifiedBy['user']['id'] ID di chi ha modificato
 * @var string $lastModifiedBy['user']['displayName'] Nome visualizzato di chi ha modificato
 *
 * @var string $lastModifiedDateTime Data e ora dell'ultima modifica dell'elemento
 * @var string $name Nome dell'elemento
 *
 * @var array $parentReference Informazioni di riferimento sul genitore
 * @var string $parentReference['driveType'] Tipo di drive (es. documentLibrary)
 * @var string $parentReference['driveId'] ID del drive
 * @var string $parentReference['id'] ID del genitore
 * @var string $parentReference['name'] Nome del genitore
 * @var string $parentReference['path'] Percorso dell'elemento nel drive
 * @var string $parentReference['siteId'] ID del sito SharePoint
 *
 * @var string $webUrl URL web dell'elemento
 * @var string $cTag Tag di coerenza dell'elemento
 *
 * @var array $file Informazioni sul file (se l'elemento è un file)
 * @var array $file['hashes'] Hash del file
 * @var string $file['hashes']['quickXorHash'] Hash QuickXOR del file
 * @var string $file['mimeType'] Tipo MIME del file
 *
 * @var array $fileSystemInfo Informazioni sul file system dell'elemento
 * @var string $fileSystemInfo['createdDateTime'] Data di creazione del file
 * @var string $fileSystemInfo['lastModifiedDateTime'] Data dell'ultima modifica del file
 *
 * @var array $folder Informazioni sulla cartella (se l'elemento è una cartella)
 * @var int $folder['childCount'] Numero di elementi figli nella cartella
 *
 * @var array $shared Informazioni sulla condivisione
 * @var string $shared['scope'] Scopo della condivisione (es. users)
 *
 * @var int $size Dimensione dell'elemento in byte
 */
class MsDriveItem extends AbstractMsTeams
{
    public $createdBy;
    public $createdDateTime;
    public $eTag;
    public $id;
    public $lastModifiedBy;
    public $lastModifiedDateTime;
    public $name;
    public $parentReference;
    public $webUrl;
    public $cTag;
    public $file;
    public $fileSystemInfo;
    public $folder;
    public $shared;
    public $size;


    public function getCreatorMsUserId(){
        if(!empty($this->createdBy['user']['id'])){
            return $this->createdBy['user']['id'];
        }
        return null;
    }

    public function getCreatorUser()
    {
        $msUserId = $this->getCreatorMsUserId();
        $token = CommunityMsgraphTokens::find()->andWhere(['msgraph_user_id' => $msUserId])->one();
        if($token){
            return $token->user;
        }
        return null;
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public function getCreatedAt(){
        return new \DateTime($this->createdDateTime);
    }

}