<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photoNew \common\models\Photo */
/* @var $photoOld \common\models\Photo */

$this->title = 'Align new rephoto';
$this->h1 = $this->title;
$this->bodyClasses[] = 'place-add-photo';

\frontend\assets\GoogleMapAsset::register($this);
?>

<?= $this->render('partial/stepper', ['active' => 4]) ?>


<section class="container">
    <div class="white-box">
        <h2><?= Yii::t('app/place', 'Confirm photo') ?></h2>
        <p><?= Yii::t('app/place', 'Fill additional photo info and after submitting <a href="#form">form</a>, photo will be visible on place
            page.') ?></p>
    </div>
</section>

<section class="container confirm">
    <?php $form = \yii\widgets\ActiveForm::begin([
        'id' => 'confirm-form',
    ]) ?>

    <div class="row">
        <div class="col-sm-6 photo">
            <img src="<?= $photo->aligned_photo ?>">
        </div>
        <div class="col-sm-6 white-box">
            <a id="form" class="anchor"></a>


            <?= $form->field($photo, 'name') ?>
            <?= $form->field($photo, 'description')->textarea() ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($photo, 'latitude') ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($photo, 'longitude') ?>
                </div>
            </div>


            <div id="map" style="height: 200px; margin-bottom: 24px">
                <input id="map-search" type="text">
                <div id="google-map" style="height: 100%"></div>
            </div>

            <?= $form->field($photo, 'captured_at') ?>
            <?= $form->field($photo, 'aligned_photo')->hiddenInput()->label(false) ?>

        </div>
    </div>

    <div class="actions">
        <a href="<?= \yii\helpers\Url::to(['/place/align-photo', 'id_place' => $place->id, 'id_photo' => $photo->id]) ?>"
           class="btn btn-back"><?= Yii::t('app/place', 'Change alignment') ?></a>
        <button class="btn waves-effect waves-light"><?= Yii::t('app/place', 'Submit') ?></button>
    </div>

    <?php $form::end(); ?>

</section>


<script>
    var map;
    var actualMarker = null;

    function initMap() {
        map = new google.maps.Map(document.getElementById('google-map'), {
            center: {
                lat: <?= $photo->latitude ?? $place->latitude ?>,
                lng: <?= $photo->longitude ?? $place->longitude?>},
            zoom: 10,
            streetViewControl: false
        });


        // add marker on click
        google.maps.event.addListener(map, 'click', function(event) {
            if (actualMarker != null){
                actualMarker.setMap(null);
            }

            actualMarker = new google.maps.Marker({position: event.latLng, map: map});

            $("#photo-latitude").val(event.latLng.lat());
            $("#photo-longitude").val(event.latLng.lng());

        });

        var geocoder = new google.maps.Geocoder;

        $("#photo-latitude, #photo-longitude").on('change', function(){
            geocodeLatLng(geocoder, map);
            map.setCenter(
                {
                    lat: parseFloat($("#photo-latitude").val()),
                    lng: parseFloat($("#photo-longitude").val()),
                });
        });


        // Create the search box and link it to the UI element.
        var input = document.getElementById('map-search');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // disable enter submit
        google.maps.event.addDomListener(input, 'keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });

        // initial marer
        geocodeLatLng(geocoder, map);
    }


    function geocodeLatLng(geocoder, map) {
        var lat = $("#photo-latitude").val();
        var long = $("#photo-longitude").val();

        if (lat == "" || long == ""){
            return;
        }

        var latlng = {lat: parseFloat(lat), lng: parseFloat(long)};
        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    if (actualMarker != null){
                        actualMarker.setMap(null);
                    }

                    actualMarker = new google.maps.Marker({
                        position: latlng,
                        map: map
                    });
                }
            }
        });
    }


</script>