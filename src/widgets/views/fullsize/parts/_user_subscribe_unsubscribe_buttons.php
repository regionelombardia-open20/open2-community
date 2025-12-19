<?php

/**
 * @var $dynamicModel \yii\base\DynamicModel
 * @var $form \open20\amos\core\forms\ActiveForm
 * @var $modelFieldImages
 */
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\user\User;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\community\AmosCommunity;
use yii\helpers\Url;

/**
 * @var $from_menu
 */
$moduleCommunity = Yii::$app->getModule('community');
?>

<?php
if (isset($community)) {
    if (!$from_menu || ($from_menu && !empty($community->parent_id))) {

        $isOpenCommunity = false;
        $isClosedCommunity = false;
        $isPrivateCommunity = false;
        $isWaitingToSigned = false;
        $isInvited = false;
        $loggedUserId = $user->id;
        $testo = AmosCommunity::t('amoscommunity', 'alla community');
        if (empty($user->id) && !empty($user)) {
            $loggedUserId = $user;
        }

        if (!empty($loggedUserId)) {
            $userProfile = User::findOne($loggedUserId)->getProfile();
            $userCommunity = CommunityUtil::getMemberCommunityLogged($community->id);
        }
        if (!empty($userProfile) && !is_null($userCommunity)) {
            if (in_array($userCommunity->status, [CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER])) {
                $isWaitingToSigned = true;
            } else if (in_array($userCommunity->status, [CommunityUserMm::STATUS_WAITING_OK_USER])) {
                $isInvited = true;
            } else {
                $isSigned = true;
            }
        } else {
            $isSigned = false;
        }
        if ($community->parent_id) {
            $testo = AmosCommunity::t('amoscommunity', 'al canale');
            $parent = open20\amos\community\models\Community::findOne($community->parent_id);
            if ($parent->parent_id) {
                $testo = AmosCommunity::t('amoscommunity', 'al sottocanale');
            }
        }


        $array_info = CommunityUtil::getCommunityTypeConfigs($community);

        $isCreatorCommunity = ($community->created_by == \Yii::$app->user->id) && !\Yii::$app->user->can("ADMIN");
        ?>

        <?php if ($isSigned) : ?>
            <?php if (!$isCreatorCommunity) { ?>
                <span>
                    <?=
                    AmosCommunity::t('amoscommunity', 'Sei iscritto {testo} come', ['testo' => $testo]) . ' ' . AmosCommunity::t(
                            'amoscommunity',
                            "{$community->getRoleByUser()}"
                    )
                    ?>
                    <br>
                    <a class="text-danger bold" href="<?=
                    Url::to([
                        '/community/community/elimina-m2m', 'id' => $community->id, 'targetId' => \Yii::$app->user->id,
                        'redirectAction' => \Yii::$app->request->url
                    ])
                    ?>" title="<?= AmosCommunity::t('amoscommunity', 'Disiscriviti {testo}', ['testo' => $testo]) . $community->title ?>">
                           <?=
                           AmosCommunity::t('amoscommunity', 'Disiscriviti')
                           ?>
                    </a>
                </span>
            <?php } ?>
            <?php // ---------- IS NOT PARTICIPANT  --------
            ?>
        <?php else : ?>
            <?php if ($userProfile && $userProfile->validato_almeno_una_volta) { ?>
                <?php if ($array_info['classType'] != 'open') : ?>
                    <?php if ($isInvited) : ?>
                        <small class="text-invited">
                            <span>
                                <?=
                                AmosCommunity::t('amoscommunity', 'Sei stato invitato {testo} come', ['testo' => $testo]) . ' ' . AmosCommunity::t(
                                        'amoscommunity',
                                        "{$community->getRoleByUser()}"
                                ) . ':'
                                ?>
                            </span>
                            <a class="btn btn-xs btn-success" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '1',
                                'redirectAction' => '/community/join/open-join?id=' . $community->id
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Accetta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Accetta')
                                   ?>
                            </a>
                            <a class="btn btn-xs btn-danger" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '0',
                                'redirectAction' => Yii::$app->request->url
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Rifiuta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Rifiuta')
                                   ?>
                            </a>
                        </small>
                    <?php elseif ($isWaitingToSigned) : ?>
                        <div class="button-container">
                            <p class="text-right">
                                <?=
                                AmosCommunity::t('amoscommunity', 'Richiesta iscrizione inviata')
                                ?>
                                <a href="javascript::void(0)" class="bi-form-field-tooltip-info m-l-5" data-toggle="tooltip" data-html="true" data-original-title="<?=
                                AmosCommunity::t(
                                        'amoscommunity',
                                        'Sei in attesa che un community manager convalidi la richiesta per poter accedere {testo}',
                                        ['testo' => $testo]
                                )
                                ?>">
                                    <span class="am am-info-outline"></span>
                                    <span class="sr-only"><?=
                                        AmosCommunity::t(
                                                'amoscommunity',
                                                'Sei in attesa che un community manager convalidi la richiesta per poter accedere {testo}',
                                                ['testo' => $testo]
                                        )
                                        ?></span>
                                </a>
                            </p>
                        </div>
                    <?php else : ?>
                        <a class="btn btn-primary btn-xs my-3 align-self-start" href="<?= Url::to(['/community/community/join-community', 'communityId' => $community->id, 'redirectAction' => Yii::$app->request->url]) ?>" title="<?= AmosCommunity::t('amoscommunity', 'Iscriviti {testo}', ['testo' => $testo]) ?> <?= $community->title ?>">
                            <?=
                            AmosCommunity::t('amoscommunity', 'Iscriviti {testo}', ['testo' => ' <span class="label-type m-l-5">' . $testo . '</span>'])
                            ?>
                        </a>
                    <?php endif; ?>
                <?php elseif ($array_info['classType'] != 'private') : ?>
                    <?php if ($isWaitingToSigned) : ?>
                        <div class="button-container w-100 d-flex justify-content-center border-top">
                            <p class="d-flex align-items-end text-muted mt-4">
                                <?=
                                AmosCommunity::t('amoscommunity', 'Richiesta iscrizione inviata')
                                ?>
                                <a href="javascript::void(0)" class="bi-form-field-tooltip-info m-l-5" data-toggle="tooltip" data-html="true" data-original-title="<?=
                                AmosCommunity::t(
                                        'amoscommunity',
                                        'Sei in attesa che un community manager convalidi la richiesta per poter accedere {testo}',
                                        ['testo' => $testo]
                                )
                                ?>">
                                    <span class="am am-info-outline"></span>
                                    <span class="sr-only"><?=
                                        AmosCommunity::t(
                                                'amoscommunity',
                                                'Sei in attesa che un community manager convalidi la richiesta per poter accedere {testo}',
                                                ['testo' => $testo]
                                        )
                                        ?></span>
                                </a>
                            </p>
                        </div>
                    <?php elseif ($isInvited) : ?>
                        <small class="text-invited">
                            <span>
                                <?=
                                AmosCommunity::t('amoscommunity', 'Sei stato invitato {testo} come', ['testo' => $testo]) . ' ' . AmosCommunity::t(
                                        'amoscommunity',
                                        "{$community->getRoleByUser()}"
                                ) . ':'
                                ?>
                            </span>
                            <a class="btn btn-xs btn-success" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '1',
                                'redirectAction' => '/community/join/open-join?id=' . $community->id
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Accetta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Accetta')
                                   ?>
                            </a>
                            <a class="btn btn-xs btn-danger" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '0',
                                'redirectAction' => Yii::$app->request->url
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Rifiuta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Rifiuta')
                                   ?>
                            </a>
                        </small>
                    <?php else : ?>
                        <a class="btn btn-primary btn-xs my-3 align-self-start" href="<?= Url::to(['/community/community/join-community', 'communityId' => $community->id, 'redirectAction' => Yii::$app->request->url]) ?>" title="<?= AmosCommunity::t('amoscommunity', 'Iscriviti {testo}', ['testo' => $testo]) ?> <?= $community->title ?>">

                            <?=
                            AmosCommunity::t('amoscommunity', 'Iscriviti {testo}', ['testo' => '<span class="label-type m-l-5">' . $testo . '</span>'])
                            ?>
                        </a>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if ($isWaitingToSigned) : ?>
                        <small class="text-invited">
                            <span>
                                <?=
                                AmosCommunity::t('amoscommunity', 'Sei stato invitato {testo} come', ['testo' => $testo]) . ' ' . AmosCommunity::t(
                                        'amoscommunity',
                                        "{$community->getRoleByUser()}"
                                ) . ':'
                                ?>
                            </span>
                            <a class="btn btn-xs btn-success" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '1',
                                'redirectAction' => '/community/join/open-join?id=' . $community->id
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Accetta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Accetta')
                                   ?>
                            </a>
                            <a class="btn btn-xs btn-danger" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '0',
                                'redirectAction' => Yii::$app->request->url
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Rifiuta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Rifiuta')
                                   ?>
                            </a>
                        </small>
                    <?php elseif ($isInvited) : ?>
                        <small class="text-invited">
                            <span>
                                <?=
                                AmosCommunity::t('amoscommunity', 'Sei stato invitato {testo} come', ['testo' => $testo]) . ' ' . AmosCommunity::t(
                                        'amoscommunity',
                                        "{$community->getRoleByUser()}"
                                ) . ':'
                                ?>
                            </span>
                            <a class="btn btn-xs btn-success" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '1',
                                'redirectAction' => '/community/join/open-join?id=' . $community->id
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Accetta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Accetta')
                                   ?>
                            </a>
                            <a class="btn btn-xs btn-danger" href="<?=
                            Url::to([
                                '/community/community/join-community', 'communityId' => $community->id,
                                'accept' => '0',
                                'redirectAction' => Yii::$app->request->url
                            ])
                            ?>" title="<?= AmosCommunity::t('amoscommunity', 'Rifiuta invito di iscrizione {testo}', ['testo' => $testo]) . ' ' . $community->title ?>">
                                   <?=
                                   AmosCommunity::t('amoscommunity', 'Rifiuta')
                                   ?>
                            </a>
                        </small>
                    <?php else : ?>
                        <!-- -->
                    <?php endif ?>
                <?php endif; ?>
            <?php } else { ?>
                <a class="btn btn-primary btn-xs my-3 align-self-start" disabled="true" href="<?= 'javascript:void(0)' ?>" title="<?=
                   AmosCommunity::t(
                           'amoscommunity',
                           "Devi essere validato per poter effettuare l'iscrizione {testo}",
                           ['testo' => $testo]
                   )
                   ?> <?= $community->title ?>">
                       <?=
                       AmosCommunity::t('amoscommunity', 'Iscriviti {testo}', ['testo' => '<span class="label-type m-l-5">' . $testo . '</span>'])
                       ?>
                </a>
            <?php } ?>

        <?php endif; ?>

        <?php
    }
}
?>