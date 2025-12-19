<?php

/**
 * @var $title
 * @var $textDescription
 */

use open20\amos\community\AmosCommunity;
?>
<?php if(!empty($textDescription)){?>
<div class="alert alert-ms-teams">
    <div class="icon-svg-ms-teams">
      <?= \open20\amos\community\utilities\CommunityUtil::showIconSvgTeams()?>
    </div>
    <div class="h6 title-alert"><?= $title ?></div>
    <div class="text-alert small"><?= $textDescription ?></div>
</div>
<?php } ?>