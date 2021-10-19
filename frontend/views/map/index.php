<?php
/* @var $this yii\web\View */
/* @var $photosJSON object */
/* @var $mapSettings array */
/* @var $photos array */
/* @var $categories array */
/* @var $placeFilter \frontend\models\search\PlaceSearch */

$this->title = 'RePhoto Map';
$this->h1 = Yii::t('app/map', 'Map');
$this->bodyClasses[] = 'map';

\yii\widgets\ActiveFormAsset::register($this);
\frontend\assets\MapAsset::register($this);
\frontend\assets\GoogleMapAsset::register($this);
?>
    <section class="map-content">
        <input id="search" placeholder="<?= Yii::t('app/map', 'Search place') ?>"
               value="<?= Yii::$app->request->get('q') ?>">

        <div id="map"
             data-lat="<?= $mapSettings['lat'] ?>"
             data-lng="<?= $mapSettings['lng'] ?>"
             data-zoom="<?= $mapSettings['zoom'] ?>"
        ></div>

    </section>

    <section class="map-preview <?= $placeFilter->map ? '' : 'full' ?>">
        <div class="row">
            <div class="col-sm-12 action-box">
                <div class="actions">
                    <button id="map-btn" class="btn waves-effect waves-light"><?= Yii::t('app/map', 'Map') ?></button>
                    <button id="filter-btn"
                            class="btn waves-effect waves-light"><?= Yii::t('app/map', 'Filter') ?></button>
                </div>

                <div id="filter-items">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'id' => 'filter-form',
                    ]); ?>

                    <div class="form-group slider-group">
                        <label><?= Yii::t('app/map', 'Captured at') ?></label>
                        <div class="slider-wrapper">
                            <div id="year-slider"></div>
                        </div>
                    </div>

                    <?= $form->field($placeFilter, 'ymin')->hiddenInput()->label(false) ?>
                    <?= $form->field($placeFilter, 'ymax')->hiddenInput()->label(false) ?>

                    <div class="form-group">
                        <label><?= Yii::t('app/map', 'Category') ?></label>
                    </div>

                    <input id="placesearch-bu"
                           name="<?= $placeFilter->formName() . '[bu]' ?>"
                        <?= $placeFilter->bu ? 'checked="checked"' : '' ?>
                           type="checkbox">
                    <label for="placesearch-bu"><?= $placeFilter->getAttributeLabel('bu') ?></label>

                    <input id="placesearch-na"
                           name="<?= $placeFilter->formName() . '[na]' ?>"
                        <?= $placeFilter->na ? 'checked="checked"' : '' ?>
                           type="checkbox">
                    <label for="placesearch-na"><?= $placeFilter->getAttributeLabel('na') ?></label>

                    <input id="placesearch-un"
                           name="<?= $placeFilter->formName() . '[un]' ?>"
                        <?= $placeFilter->un ? 'checked="checked"' : '' ?>
                           type="checkbox">
                    <label for="placesearch-un"><?= $placeFilter->getAttributeLabel('un') ?></label>

                    <input id="placesearch-map"
                           name="<?= $placeFilter->formName() . '[map]' ?>"
                           value="<?= $placeFilter->map ?>"
                           type="hidden">

                    <?php $form::end(); ?>
                </div>
            </div>
        </div>

        <div id="places-preview" class="row">
            <div class="places-loading">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p><?= Yii::t('app/map', 'Looking for places, please wait.') ?></p>
            </div>

            <? //= $this->render('partial/places', ['places' => $places]) ?>
        </div>

        <a href="<?= \yii\helpers\Url::to(['/place/create']) ?>" id="add-place-btn" class="btn-floating btn-large waves-effect waves-light red"><i class="material-icons">add</i></a>

    </section>

<?php
$sliderStart = $placeFilter->ymin;
$sliderEnd = $placeFilter->ymax;

$sliderMin = \common\models\Photo::find()->min('captured_at');
$sliderMin = $sliderMin ? (new DateTime($sliderMin))->format('Y') : date('Y');
$sliderMax = date('Y');

$this->registerJs(<<<JS
    $(".map-preview").on('mouseenter', 'img',  function(){
        $(this).removeClass('partial');
        $(this).siblings('img').addClass('hidden-img');
    });

    $(".map-preview").on('mouseleave', 'img', function(){
        $(this).siblings('img').removeClass('hidden-img');
        $(this).addClass('partial');
    });
    
    $('#filter-btn').on('click', function(){
        $('#filter-items').slideToggle(); 
    });
    
    
    var slider = document.getElementById('year-slider');
        noUiSlider.create(slider, {
        start: [{$sliderStart}, {$sliderEnd}],
        connect: true,
        step: 1,
        orientation: 'horizontal', // 'horizontal' or 'vertical'
        tooltips: true,
        range: {    
            'min': {$sliderMin},
            'max': {$sliderMax}
        },
        format: wNumb({
         decimals: 0
       })
    });
        
    slider.noUiSlider.on('change', function() {
        var values = slider.noUiSlider.get();
        $("#placesearch-ymin").val(values[0]);
        $("#placesearch-ymax").val(values[1]);
        processFilter();
    });
    
    $("#filter-form").on('change', ':input', function(){
        processFilter();
    });
    
    function processFilter() {
        refreshMapAndSideNav();
    }
    
    $(document).on('click', '#map-btn', function(){
        var mapElement = $('.map-preview');
        var oldState = mapElement.hasClass('full');
        
        mapElement.toggleClass('full');
        setTimeout(function(){
            $('#placesearch-map').val(oldState ? 1 : 0).trigger('change');    
        }, 500);
        
    });
JS
);