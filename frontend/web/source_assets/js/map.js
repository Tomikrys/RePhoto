var map, infoWindow, actualMarker, activeMarker;
var actualBounds;

function initMap() {
    var mapElement = document.getElementById('map');

    var geocoder = new google.maps.Geocoder();
    var address = document.getElementById('search').value;

    if (address !== '') {
        geocoder.geocode({'address': address}, function (results, status) {
            if (status === 'OK') {
                var center = results[0].geometry.location;
                mapElement.dataset.zoom = 12;
                initMap2(mapElement, center);
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
                var center = {lat: parseFloat(mapElement.dataset.lat), lng: parseFloat(mapElement.dataset.lng)};
                initMap2(mapElement, center);
            }
        });
    } else {
        var center = {lat: parseFloat(mapElement.dataset.lat), lng: parseFloat(mapElement.dataset.lng)};
        initMap2(mapElement, center);
    }
}

function initMap2(mapElement, center) {
    // init and display map
    map = new google.maps.Map(mapElement, {
        zoom: parseInt(mapElement.dataset.zoom),
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        scaleControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        mapTypeControlOptions: {
            mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
                'styled_map']
        },
        center: center
    });

    infoWindow = new google.maps.InfoWindow();

    // add on map click listener
    map.addListener('click', function () {
        var search = $("#search");
        if (search.is(":focus")) {
            $("#search").blur();
        }
    });

    // Create the search box and link it to the UI element.
    var input = document.getElementById('search');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    actualBounds = map.getBounds();

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function () {
        var tmp = actualBounds;

        actualBounds = map.getBounds();
        searchBox.setBounds(actualBounds);

        // refresh only on first call
        if (tmp == null) {
            refreshMapAndSideNav();
        }
    });

    map.addListener('dragend', function () {
        actualBounds = map.getBounds();
        window.setTimeout(function () {
            refreshMapAndSideNav();
        }, 500);
    });

    map.addListener('zoom_changed', function () {
        actualBounds = map.getBounds();
        window.setTimeout(function () {
            refreshMapAndSideNav();
        }, 500);
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

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });

    // map click event listener
    google.maps.event.addListener(map, 'click', function (event) {
        processPlaceEdit(map, event);
    });
}

var markers = [];


function geocodeAddress(geocoder, resultsMap) {
    var address = document.getElementById('search').value;
    geocoder.geocode({'address': address}, function (results, status) {
        if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: resultsMap,
                position: results[0].geometry.location
            });
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}

function refreshMapAndSideNav(page) {
    var progress = $('#main-progress');

    progress.show();

    var NE = actualBounds.getNorthEast();
    var SW = actualBounds.getSouthWest();
    var bounds = {nw: {lat: NE.lat(), lon: SW.lng()}, se: {lat: SW.lat(), lon: NE.lng()}};

    var postData = {
        bounds: bounds,
        zoom: map.zoom,
        ymin: document.getElementById('placesearch-ymin').value,
        ymax: document.getElementById('placesearch-ymax').value,
        bu: document.getElementById('placesearch-bu').checked ? 1 : 0,
        na: document.getElementById('placesearch-na').checked ? 1 : 0,
        un: document.getElementById('placesearch-un').checked ? 1 : 0,
        map: document.getElementById('placesearch-map').value,
        page: typeof page === 'undefined' ? 1 : page
    };
    //checkbox-1
    $.post(url('/map/places'), postData, function (response) {
        if (response.page == 1) {
            refreshMapPlaces(response.markers);
            refreshPlacesPreview(response.placesPreview);

        } else {
            appendPlacesPreview(response.placesPreview);
        }

        pushMapUrl(postData);

        progress.hide();

    }).fail(function () {
        progress.hide();
        Materialize.toast('Chyba při získávání fotografií.', 6000);
    });
}

function pushMapUrl(postData) {
    var center = map.getCenter();
    var zoom = map.getZoom();

    var url = "?lat="
        + center.lat()
        + "&lng=" + center.lng()
        + "&zoom=" + zoom
        + "&ymin=" + postData['ymin']
        + "&ymax=" + postData['ymax']
        + "&bu=" + postData['bu']
        + "&na=" + postData['na']
        + "&un=" + postData['un']
        + "&map=" + postData['map']
    ;

    history.pushState({}, "", url);
}

function refreshMapPlaces(places) {
    markers.forEach(function (marker) {
        marker.setMap(null);
    });
    markers = [];

    places.forEach(function (place) {
        var marker = new google.maps.Marker({
            position: {lat: place.location.lat, lng: place.location.lon},
            map: map,
            db_lat: place.location.lat,
            db_lng: place.location.lon
        });

        if (place.count > 1) {
            marker.setLabel('' + place.count);

            if (place.count >= 1000) {
                marker.setIcon("/img/map/m5.png");
            } else if (place.count >= 500) {
                marker.setIcon("/img/map/m4.png");
            } else if (place.count >= 200) {
                marker.setIcon("/img/map/m3.png");
            } else if (place.count >= 50) {
                marker.setIcon("/img/map/m2.png");
            } else {
                marker.setIcon("/img/map/m1.png");
            }

            marker.addListener('click', function () {
                map.setZoom(map.zoom + 2);
                map.setCenter(marker.getPosition());
            });

        } else {

            marker.addListener('click', function () {
                $.post(url('/place/info-window'), {lat: this.db_lat, lng: this.db_lng}, function (response) {
                    infoWindow.setContent(response);
                    infoWindow.open(map, marker);
                });
            });
        }


        markers.push(marker);
    });
}

function appendPlacesPreview(content) {
    var sideNav = $("#places-preview");
    sideNav.find('.actions').remove();
    sideNav.append(content);
}


function refreshPlacesPreview(content) {
    var sideNav = $("#places-preview");
    sideNav.html(content);
}

function processPlaceEdit(map, event) {
    var modal = $('.modal');
    if (modal.length > 0) {
        if (actualMarker != null) {
            actualMarker.setMap(null);
        }

        actualMarker = new google.maps.Marker({position: event.latLng, map: map});

        $("#photo-latitude").val(event.latLng.lat());
        $("#photo-longitude").val(event.latLng.lng());
    }
}

function showPhotoDetail(id) {
    var sidenav = $("#slide-out-dynamic");
    var progress = $('#main-progress');

    progress.show();

    $.get('/photo/detail', {id: id}, function (result) {
        if (result === false) {
            Materialize.toast('Chyba při získávání informací o fotce.', 6000);
            progress.hide()
        } else {
            sidenav.html(result);
            progress.hide()
            pushMapUrl(id);
        }
    });
}

$(document).on('click', '.modal .modal-close-btn', function () {
    $(this).parents('.modal').modal('hide');
});

function showActiveMarker(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);

    if (typeof activeMarker === 'undefined') {
        activeMarker = new google.maps.Marker({});
    }

    activeMarker.setAnimation(google.maps.Animation.BOUNCE);
    activeMarker.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);
    activeMarker.setPosition(latlng);
    activeMarker.setMap(map);
}


function hideActiveMarker() {
    if (typeof activeMarker !== 'undefined') {
        activeMarker.setMap(null);
    }
}