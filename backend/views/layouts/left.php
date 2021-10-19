<aside class="main-sidebar">

    <section class="sidebar">
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Menu', 'options' => ['class' => 'header']],
                    ['label' => Yii::t('app/menu', 'Home'), 'url' => ['/site/index'],  'icon' => 'dashboard'],
                    ['label' => Yii::t('app/menu', 'Users'), 'url' => ['/user/index'],  'icon' => 'user'],
                    ['label' => Yii::t('app/menu', 'Places'), 'url' => ['/place/index'],  'icon' => 'thumb-tack'],
                ],
            ]
        ) ?>

    </section>

</aside>
