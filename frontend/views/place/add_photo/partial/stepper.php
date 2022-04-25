<?php
/* @var $this \frontend\components\FrontendView */
/* @var $active int */

?>


<section class="container stepper">
    <div class="<?= $active == 1 ? 'active' : '' ?>"><span class="number">1</span><span class="text"><?= Yii::t('app/place', 'Upload') ?></span></div>
    <div class="<?= $active == 2 ? 'active' : '' ?>"><span class="number">2</span><span class="text"><?= Yii::t('app/place', 'Align') ?></span></div>
    <div class="<?= $active == 3 ? 'active' : '' ?>"><span class="number">3</span><span class="text"><?= Yii::t('app/place', 'Review') ?></span></div>
    <div class="<?= $active == 4 ? 'active' : '' ?>"><span class="number">4</span><span class="text"><?= Yii::t('app/place', 'Confirm') ?></span></div>
</section>

