<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photos array */

/* @var $photosEdited array */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'update place';
$this->bodyClasses[] = 'place';

\frontend\assets\GoogleMapAsset::register($this);
?>

<section class="container white-box">
    <h1><?= $this->title ?></h1>


    <?php $form = ActiveForm::begin([
        'id' => 'form-upload',
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>

    <?= $form->field($place, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($place, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($place, 'id_category')->dropDownList(\common\models\Place::getCategories()) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($place, 'latitude')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($place, 'longitude')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div id="map" style="height: 300px; margin-bottom: 24px">
        <input id="map-search" type="text">
        <div id="google-map" style="height: 100%"></div>
    </div>

    <hr>

    <div class="box-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>


<script>
    var map;
    var actualMarker = null;

    function initMap() {
        map = new google.maps.Map(document.getElementById('google-map'), {
            center: {
                lat: <?= $place->latitude ?? 47 ?>,
                lng: <?= $place->longitude ?? 17 ?>},
            zoom: 10,
            streetViewControl: false
        });


        // add marker on click
        google.maps.event.addListener(map, 'click', function (event) {
            if (actualMarker != null) {
                actualMarker.setMap(null);
            }

            actualMarker = new google.maps.Marker({position: event.latLng, map: map});

            $("#place-latitude").val(event.latLng.lat());
            $("#place-longitude").val(event.latLng.lng());

        });

        var geocoder = new google.maps.Geocoder;

        $("#place-latitude, #place-longitude").on('change', function () {
            geocodeLatLng(geocoder, map);
            map.setCenter(
                {
                    lat: parseFloat($("#place-latitude").val()),
                    lng: parseFloat($("#place-longitude").val()),
                });
        });


        // Create the search box and link it to the UI element.
        var input = document.getElementById('map-search');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // disable enter submit
        google.maps.event.addDomListener(input, 'keydown', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function (place) {
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
        var lat = $("#place-latitude").val();
        var long = $("#place-longitude").val();

        if (lat == "" || long == "") {
            return;
        }

        var latlng = {lat: parseFloat(lat), lng: parseFloat(long)};
        geocoder.geocode({'location': latlng}, function (results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    if (actualMarker != null) {
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