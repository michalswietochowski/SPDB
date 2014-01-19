var SPDB = {
    map: null,
    mapOptions: {
        center: new google.maps.LatLng(38.50, -97.50),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 5
    },
    clustererOptions: {
        minimumClusterSize: 1,
        enableRetinaIcons: true,
        zoomOnClick: false
    },
    clusterer: null,
    markers: {},
    xhr: null,
    init: function () {
        google.visualRefresh = true;
        this.map = new google.maps.Map($('#map-canvas').get(0), this.mapOptions);
        this.clusterer = new MarkerClusterer(this.map, [], this.clustererOptions);
        this.clusterer.setCalculator(SPDB.clustererCalculator);
        this.initEventListeners();
    },
    initEventListeners: function () {
        google.maps.event.addListener(this.map, 'idle', function (event) {
            SPDB.refreshMarkers();
        });
//        google.maps.event.addListener(this.map, 'dragend', function () {
//            console.log('drag', SPDB.map.getZoom(), SPDB.map.getCenter());
//        });
//        google.maps.event.addListener(this.map, 'zoom_changed', function () {
//            var zoom = SPDB.map.getZoom();
//            console.log('zoom changed', SPDB.map.getZoom(), SPDB.map.getCenter());
//        });
//        var viewportBox;

//        google.maps.event.addListener(this.map, 'idle', function (event) {
//            var bounds = SPDB.map.getBounds();
//
//            var ne = bounds.getNorthEast();
//            var sw = bounds.getSouthWest();
//
//            var viewportPoints = [
//                ne, new google.maps.LatLng(ne.lat(), sw.lng()),
//                sw, new google.maps.LatLng(sw.lat(), ne.lng()), ne
//            ];
//
//            if (viewportBox) {
//                viewportBox.setPath(viewportPoints);
//            } else {
//                viewportBox = new google.maps.Polyline({
//                    path: viewportPoints,
//                    strokeColor: '#0000FF',
//                    strokeOpacity: 0.5,
//                    strokeWeight: 2
//                });
//                viewportBox.setMap(SPDB.map);
//            }
//
//            var info = document.getElementById('info');
//            info.innerHTML = 'NorthEast: ' + ne.lat() + '   ' + ne.lng() +
//                '<br />' + 'SouthWest: ' + sw.lat() + '   ' + sw.lng();
//        });
    },
    refreshMarkers: function () {
        var params = this.getParams();
        if (this.isZoomLocalLevel(params.zoom) || this.lastZoom != this.getZoomLevelRange(params.zoom)) {
            this.lastZoom = this.getZoomLevelRange(params.zoom);
            this.clusterer.clearMarkers();
            var xhr = $.ajax({
                url: '/application/index/get-markers',
                data: params,
                timeout: 15000,
                type: 'POST',
                beforeSend: function (jqXHR, settings) {
                    if (SPDB.xhr) {
                        SPDB.xhr.abort();
                    }
                    SPDB.xhr = xhr;
                    SPDB.statusLoading();
                },
                success: function (data, textStatus, jqXHR) {
                    if (data && data.markers) {
                        SPDB.drawMarkers(data.markers);
                        if (SPDB.isZoomLocalLevel(params.zoom)) {
                            SPDB.statusSuccess('Loaded ' + _.size(data.markers) + ' issues!', '', 3000);
                        } else {
                            var size = 0;
                            for (var i in data.markers) {
                                var marker = data.markers[i];
                                size += marker.count;
                            }
                            SPDB.statusSuccess('Loaded ' + size + ' issues in ' + _.size(data.markers) + ' markers!', '', 3000);
                        }
                    } else {
                        SPDB.statusError('Unknown error while loading markers');
                    }
                    SPDB.xhr = null;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    SPDB.statusError('Error while loading markers: ' + errorThrown);
                    SPDB.xhr = null;
                }
            });
        }
    },
    drawMarkers: function (markers) {
        $.extend(SPDB.markers, markers);
        var params = this.getParams();
        if (this.isZoomLocalLevel(params.zoom)) {
            this.clusterer.setMinimumClusterSize(2);
            this.clusterer.setCalculator(MarkerClusterer.CALCULATOR);
        } else {
            this.clusterer.setMinimumClusterSize(1);
            this.clusterer.setCalculator(SPDB.clustererCalculator);
        }
        for (var key in markers) {
            var marker = markers[key];
            var latLng = new google.maps.LatLng(marker.point.latitude, marker.point.longitude);
            var markerObject = new google.maps.Marker({
                position: latLng,
                map: SPDB.map,
                title: key
            });
            this.clusterer.addMarker(markerObject);
        }
    },
    getParams: function () {
        var center = this.map.getCenter();
        var bounds = this.map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var params = {
            zoom: this.map.getZoom(),
            center: {
                latitude: center.d,
                longitude: center.e
            },
            bounds: {
                ne: {
                    latitude: ne.lat(),
                    longitude: ne.lng()
                },
                sw: {
                    latitude: sw.lat(),
                    longitude: sw.lng()
                }
            }
        };
        return params;
    },
    /**
     * ZOOM related functions
     */
    levels: {
        country: 'country',
        state: 'state',
        county: 'county',
        local: 'local'
    },
    zoomLevels: {
        country: 3,
        state: 6,
        county: 12,
        local: 15
    },
    lastZoom: null,
    isZoomCountryLevel: function (zoom) {
        return (zoom <= this.zoomLevels.country);
    },
    isZoomStateLevel: function (zoom) {
        return (zoom <= this.zoomLevels.state && zoom > this.zoomLevels.country);
    },
    isZoomCountyLevel: function (zoom) {
        return (zoom <= this.zoomLevels.county && zoom > this.zoomLevels.state);
    },
    isZoomLocalLevel: function (zoom) {
        return (zoom > this.zoomLevels.county);
    },
    getZoomLevelRange: function (zoom) {
        if (this.isZoomCountryLevel(zoom)) {
            return this.levels.country;
        } else if (this.isZoomStateLevel(zoom)) {
            return this.levels.state;
        } else if (this.isZoomCountyLevel(zoom)) {
            return this.levels.county;
        } else {
            return this.levels.local;
        }
    },
    /**
     * Status related functions
     */
    statusLoading: function () {
        return this.statusNeutral('Loading issues...', '');
    },
    statusNeutral: function (text, icon, timeout) {
        return this.status('label-primary', text, icon, timeout);
    },
    statusSuccess: function (text, icon, timeout) {
        return this.status('label-success', text, icon, timeout);
    },
    statusError: function (text, icon, timeout) {
        return this.status('label-danger', text, icon, timeout);
    },
    status: function (type, text, icon, timeout) {
        var statusSpan = $('#status');
        statusSpan.attr('class', 'label').addClass(type).text(text).show();
        if (timeout && timeout > 0) {
            setTimeout('SPDB.statusClear()', timeout);
        }
    },
    statusClear: function () {
        $('#status').fadeOut();
    },
    /**
     * Clusterer related functions
     */
    clustererCalculator: function (markers, numStyles) {
        var key = markers.shift().title;
        var marker = SPDB.markers[key];
        return {
            text: marker.count,
            index: 5,
            title: marker.name
        };
    }
};

$(document).ready(function () {
    SPDB.init();
});