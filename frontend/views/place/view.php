<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photos array */
/* @var $photosEdited array */

$this->title = $place->name;
//$this->h1 = $this->title;
$this->bodyClasses[] = 'place-detail';
\frontend\assets\GoogleMapAsset::register($this);
?>

    <section class="container main-info">
        <h1><?= $this->title ?></h1>

        <div class="row">
            <div class="col-md-6 active-image-box">
                <img id="active-photo" class="" src="<?= $photos[0]['original_url'] ?>">
            </div>
            <div class="col-md-6 white-box ">
                <div class="row">
                    <div class="col s12">
                        <ul class="tabs">
                            <li class="tab col s4"><a href="#place">Místo</a></li>
                            <li class="tab col s4"><a href="#photo">Fotografie</a></li>
                            <li class="tab col s4"><a href="#map">Mapa</a></li>
                        </ul>
                    </div>

                    <div id="place" class="col s12">
                        <table class="table info-table">
                            <tbody>
                            <tr>
                                <th>Popis</th>
                                <td>
                                    <?= $place->description ?>
                                </td>
                            </tr>
                            <tr>
                                <th>GPS</th>
                                <td><?= $place->latitude ?> / <?= $place->longitude ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="photo" class="col s12">
                        <table class="table info-table">
                            <tbody>
                            <tr>
                                <th>Autor</th>
                                <td>
                                    <a href="<?= \yii\helpers\Url::to(['/user/public-profile', 'id' => $photos[0]['user']['id']]) ?>"><?= $photos[0]['user']['name'] ?></a>
                                </td>
                            </tr>
                            <tr>
                                <th>Zachyceno</th>
                                <td><?= Yii::$app->formatter->asDate($photos[0]['captured_at'], 'Y') ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="map" class="col s12">
                        <div id="google-map"></div>
                    </div>
                </div>

                <div class="actions">
                    <a class="btn waves-effect waves-light btn-flat" data-pjax="0"
                       href="<?= \yii\helpers\Url::to(['/place/update', 'id_place' => $place->id]) ?>">
                        Edit place
                    </a>

                    <a class="btn waves-effect waves-light"
                       href="<?= \yii\helpers\Url::to(['/place/upload-photo', 'id_place' => $place->id]) ?>">
                        Add new rephoto
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 additional-images-box">
                <div class="row">
                    <?php if (!empty($photos)): ?>
                        <div class=" col-md-12 valign-wrapper photos">
                            <i class="material-icons scroll-left-btn">keyboard_arrow_left</i>
                            <?= $this->render('partial/photos', ['photos' => $photos]) ?>
                            <i class="material-icons scroll-right-btn">keyboard_arrow_right</i>
                        </div>
                    <?php endif; ?>
                    <php
                    <?php if (!empty($photosEdited)): ?>
                        <div class=" col-md-12 valign-wrapper edited">
                            <i class="material-icons scroll-left-btn">keyboard_arrow_left</i>
                            <?= $this->render('partial/photos_edited', ['photos' => $photosEdited]) ?>
                            <i class="material-icons scroll-right-btn">keyboard_arrow_right</i>
                        </div>
                    <?php endif; ?>
                    <div></div>
                </div>
            </div>
        </div>

    </section>

    <script>
        var map;

        function initMap() {
            map = new google.maps.Map(document.getElementById('google-map'), {
                center: {lat: <?= $place->latitude ?>, lng: <?= $place->longitude ?>},
                zoom: 10
            });

            var marker = new google.maps.Marker({
                position: {lat: <?= $place->latitude ?>, lng: <?= $place->longitude ?>},
                map: map
            });
        }
    </script>

<?php $this->registerJs(<<<JS
    $(document).on('click', '.card img', function() {
        $("#active-photo").attr('src', $(this).data('url-original'));
    });

    $('.photos .toggle-editor-btn').on('click', function(e){
        e.preventDefault();
        var photo = $(this).parents('.photo');
        console.log(photo);
        editorListToggle(photo);
    });
    
    
    $(".scroll-right-btn").on('click', function(){
       var box = $(this).siblings('.horizontal-scroll-box');
       box.animate({scrollLeft: box[0].scrollLeft + 200}, 400);
    });

    $(".scroll-left-btn").on('click', function(){
       var box = $(this).siblings('.horizontal-scroll-box');
       box.animate({scrollLeft: box[0].scrollLeft - 200}, 400);
    });
JS
);