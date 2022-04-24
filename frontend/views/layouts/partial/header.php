<?php
/* @var $this \yii\web\View */

use yii\helpers\Url;

?>
<div class="navbar-fixed">
    <nav class="nav-extended">
        <div class="nav-wrapper teal">

            <ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">

                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="<?= Url::to(['/'], true) ?>" class="logo">
                        <span itemprop="name">
                            <img src="/img/logo_e.png" height="62px">
                            <span class="hide-on-small-and-down">RePhoto</span>
                        </span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>


                <?php if ($this->h1 != null) : ?>
                    <li>
                        <i class="material-icons">chevron_right</i>
                    </li>

                    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="<?= Yii::$app->request->absoluteUrl ?>">
                            <span itemprop="name"><?= $this->h1 ?></span></a>
                        <meta itemprop="position" content="2" />
                    </li>
                <?php endif; ?>
            </ul>

            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li class="language-select">
                    <ul class="dropdown-content">
                        <li><a href="/cs">Čeština</a></li>
                        <li><a href="/en">English</a></li>
                    </ul>
                    <a class="dropdown-trigger">
                        <?= strtoupper(Yii::$app->language) ?>
                        <i class="material-icons right">arrow_drop_down</i>
                    </a>
                </li>

                <li><a class="waves-effect waves-light" href="<?= Url::to(['/map']) ?>"><?= Yii::t('app/menu', 'Map') ?></a>
                </li>

                <li>
                    <a class="waves-effect waves-light" href="<?= Url::to(['/place/create']) ?>"><?= Yii::t('app/menu', 'Add Photo') ?></a>
                </li>
                <li>
                    <a class="waves-effect waves-light" href="<?= Url::to(['/editor']) ?>">
                        Editor
                        <span id="editor-images" class="new badge" data-badge-caption=""><?= count(Yii::$app->session->get('editor', [])) ?>/2</span>
                    </a>
                </li>

                <?php if (!Yii::$app->user->isGuest) : ?>
                    <li>
                        <a class="login-btn" href="<?= Url::to(['/user/profile']) ?>"><?= Yii::t('app/menu', 'My profile') ?></a>
                    </li>
                <?php else : ?>
                    <li>
                        <a class="waves-effect waves-light login-btn" href="<?= Url::to(['/user/login']) ?>"><?= Yii::t('app/menu', 'Log in') ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>

<?php $this->registerJs(
    <<<JS
    $(".dropdown-trigger").dropdown();
JS
);
