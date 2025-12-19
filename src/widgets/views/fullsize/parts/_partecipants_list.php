<?php

use open20\amos\admin\AmosAdmin;
use open20\amos\community\AmosCommunity;
use open20\amos\admin\widgets\ConnectToUserWidget;
use open20\amos\core\helpers\Html;

/** @var AmosAdmin $adminModule */
$adminModule = AmosAdmin::instance();

$numberExpose = (isset($numberExpose)) ? $numberExpose : 4;
$baseUrlProfile = '/' . AmosAdmin::getModuleName() . '/user-profile/view?id=';
$removeLink = (isset($removeLink)) ? $removeLink : false;
$showTooltip = (isset($showTooltip)) ? $showTooltip : true;
$showAvatarPresence = (isset($showAvatarPresence)) ? $showAvatarPresence : false;

$avatarWrapperSize = (isset($avatarWrapperSize)) ? $avatarWrapperSize : 'sm';
if ($avatarWrapperSize == 'xl') {
  $avatarClassSize = 'size-xl';
} else if ($avatarWrapperSize == 'lg') {
  $avatarClassSize = 'size-lg';
} else if ($avatarWrapperSize == 'sm') {
  $avatarClassSize = 'size-sm';
} else if ($avatarWrapperSize == 'xs') {
  $avatarClassSize = 'size-xs';
} else {
  $avatarClassSize = 'size-md';
}
?>
<a class="partecipants-mobile" href="/community/community/participants?communityId=<?= $community_id ?>">
  <span class="material-icons-two-tone icon-primary">
    people
  </span>
  <?= $usersNumber ?>
</a>


<ul class="avatar-group-stacked <?= $additionalClass ?>">
  <?php
  $i = 0;
  for ($i; $i < min([$numberExpose, $usersNumber]); $i++) {
  ?>
    <?php
    if ($userProfiles[$i] && $userProfiles[$i]->user_id != \Yii::$app->user->id) {
      $nameSurname = $userProfiles[$i]->nomeCognome;
      $tooltipText = '<strong>' . $nameSurname . '</strong>' . (isset($tooltipAdditionalInfoAvatarsGroup) ? '<br/><em>' . $tooltipAdditionalInfoAvatarsGroup . '</em>' : '');
      $imageAvatar = $userProfiles[$i]->getAvatarWebUrl('card_users');
      $url = $baseUrlProfile . $userProfiles[$i]->id;
      $arr = explode(' ', trim($nameSurname));
      $name = $arr[0];
      $surname = $arr[1];
      $initials = (strpos($avatarWrapperSize, 'sm,xs')) ? substr($name, 0, 1) : substr($name, 0, 1) . substr($surname, 0, 1);
      $avatarPresence = ($userProfiles[$i]->attivo) ? 'active' : 'hidden';
      if ($avatarPresence == 'hidden') {
        $avatarPresenceTitle = AmosCommunity::t('amosdesign', 'Assente');
      } else {
        $avatarPresenceTitle = AmosCommunity::t('amosdesign', 'Attivo');
      }
	  
    
    ?>
    <li class="avatar-wrapper">
      <?php if (!$removeLink || $showTooltip) : ?>
        <div class="dropdown wrapper-dropdown-partecipants">
          <a class="avatar <?= $avatarClassSize ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href="javascript:void(0)" title="<?= AmosCommunity::t('amosdesign', 'Collegati e scrivi a') . ' ' . $nameSurname ?> ">
            <?php if (!(isset($imageAvatar))) : ?>
              <p aria-hidden="true"><?= $initials ?></p>
              <span class="sr-only"><?= $nameSurname ?></span>
            <?php else : ?>
              <?php if ($userProfiles[$i]) : ?>
                <img src="<?= $userProfiles[$i]->getAvatarUrl('card_users') ?>" alt="<?= $nameSurname ?>">
              <?php endif ?>

            <?php endif ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-partecipants" aria-labelledby="dropdownPartecipants">
            <div class="dropdown-body">
              <div class="avatar-wrapper avatar-extra-text m-b-0">
                <div class="avatar-box-img">
                  <div class="avatar size-xl">
                    <?php if (!(isset($imageAvatar))) : ?>
                      <p aria-hidden="true"><?= $initials ?></p>
                      <span class="sr-only"><?= $nameSurname ?></span>
                    <?php else : ?>
                      <?php if ($userProfiles[$i]) : ?>
                        <img src="<?= $userProfiles[$i]->getAvatarUrl('card_users') ?>" alt="<?= $nameSurname ?>">
                      <?php endif ?>
                    <?php endif ?>
                  </div>
                </div>
                <div class="ml-2 avatar-body">
                  <div class="name-manage">
                    <p class="avatar-name font-weight-bold mb-0">
                      <?=
                      Html::a(
                        $userProfiles[$i]->getNomeCognome(),
                        $url,
                        ['title' => AmosAdmin::t('amosadmin', '#icon_name_title_link') . ' ' . $userProfiles[$i]->getNomeCognome(), 'data-gui' => 'icon-view-profiles']
                      );
                      ?>
                    </p>
                  </div>


                    <?php if ($adminModule->enableUserContacts && !$adminModule->enableSendMessage) : ?>
                      <?= ConnectToUserWidget::widget(['model' => $userProfiles[$i], 'divClassBtnContainer' => '']) ?>
                    <?php endif; ?>
                    <?php if (!$adminModule->enableUserContacts && $adminModule->enableSendMessage) : ?>
                      <?= SendMessageToUserWidget::widget(['model' => $userProfiles[$i], 'divClassBtnContainer' => '']) ?>
                    <?php endif; ?>
                    <?php if ($adminModule->enableInviteUserToEvent) : ?>
                      <?php
                      /** @var \open20\amos\events\AmosEvents $eventsModule */
                      $eventsModule = Yii::$app->getModule('events');
                      ?>
                      <?php if (!is_null($eventsModule) && $eventsModule->hasMethod('getInviteUserToEventWidget')) : ?>
                        <?= $eventsModule->getInviteUserToEventWidget($model) ?>
                      <?php endif; ?>
                    <?php endif; ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <?php if ($showAvatarPresence) : ?>
          <div class="avatar-presence <?= $avatarPresence ?>" data-toggle="tooltip" title="<?= AmosCommunity::t('amosdesign', 'Presenza') . ': ' . $avatarPresenceTitle ?>">

            <span class="sr-only"><?= AmosCommunity::t('amosdesign', 'Presenza') . ': ' . $avatarPresenceTitle ?> </span>
          </div>
        <?php endif ?>
      <?php else : ?>
        <div class="avatar <?= $avatarClassSize ?>">
          <?php if (!(isset($imageAvatar))) : ?>
            <p aria-hidden="true"><?= $initials ?></p>
            <span class="sr-only"><?= $nameSurname ?></span>
          <?php else : ?>
            <img src="<?= $imageAvatar ?>" alt="<?= $nameSurname ?>">
          <?php endif ?>
        </div>
      <?php endif; ?>
    </li>
  <?php
  }
  }
  $userDropdown = '';
  if ($i < $usersNumber) {
    $userDropdown = $usersNumber - $numberExpose;
    $message = AmosCommunity::t('amosdesign', 'Visualizza altri {numeroUtenti} partecipanti', ['numeroUtenti' => $userDropdown]);
    $iconUsersNumber = '+';
  } else {
    $message = AmosCommunity::t('amosdesign', 'Gestisci/aggiungi partecipanti');
    $iconUsersNumber = '<span class="mdi mdi-account-plus mdi-18px"></span>';
  }
  ?>

  <!--dropdown-->
  <li>
    <div class="avatar avatar-dropdown <?= $avatarClassSize ?>">
      <div class="dropdown">
        <a class="btn btn-dropdown dropdown-toggle" href="/community/community/participants?communityId=<?= $community_id ?>" data-toggle="tooltip" title="<?= $message ?>" role="button" id="dropdownMenuAvatarGroupStacked">
          <span class="sr-only"><?= $message ?></span>
          <span aria-hidden="true"><?= $iconUsersNumber . $userDropdown ?></span>
        </a>
      </div>
    </div>
  </li>
</ul>


