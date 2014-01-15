var SPDB = {
    map: null,
    mapOptions: {
        center: new google.maps.LatLng(38.50, -97.50),
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        zoom: 5
    },
    init: function () {
        google.maps.visualRefresh = true;
        SPDB.map = new google.maps.Map($('#map-canvas').get(0), SPDB.mapOptions);
    }
};

$(document).ready(function () {
    SPDB.init();
});