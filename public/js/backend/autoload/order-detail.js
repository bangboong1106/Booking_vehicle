$(function () {
    if ($('#mapChild').length > 0) {
        google.maps.event.addDomListener(window, 'load', initMapChildPage());
    }
});


var map;
var markers = [];
var location, lat = 21.0464404, lng = 105.7936427;
var bounds;

function initMapChildPage() {
    var latlng = new google.maps.LatLng(lat, lng);
    map = new google.maps.Map(document.getElementById('mapChild'), {
        center: latlng,
        zoom: 15
    });
    getLocation();
}

function getLocation() {
    clearMarkers();
    var latlngShow = new google.maps.LatLng(lat, lng);
    var marker = new google.maps.Marker({
        map: map,
        position: latlngShow,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29)
    });
    map.setCenter(marker.getPosition());
}

function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null)
    }
    markers = [];
}