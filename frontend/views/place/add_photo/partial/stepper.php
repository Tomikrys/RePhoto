<?php
/* @var $this \frontend\components\FrontendView */
/* @var $active int */

?>


<section class="container stepper">
    <div class="<?= $active == 1 ? 'active' : '' ?>"><span class="number">1</span><span class="text">Upload</span></div>
    <div class="<?= $active == 2 ? 'active' : '' ?>"><span class="number">2</span><span class="text">Align</span></div>
    <div class="<?= $active == 3 ? 'active' : '' ?>"><span class="number">3</span><span class="text">Review</span></div>
    <div class="<?= $active == 4 ? 'active' : '' ?>"><span class="number">4</span><span class="text">Confirm</span></div>
</section>

