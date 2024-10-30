(function ($) {

  $(function() {

    if (typeof google === 'object' && typeof google.maps === 'object') {
    } else {
      alert('Google Maps API not loaded.');
      return;
    }
    if ( typeof locationews_map_init === 'undefined' ) {
      return;
    }

    var locationews_marker  = '';
    var coordinates         = '';
    var locationews_meta    = locationews_map_init.locationews_meta;
    var locationews_options = locationews_map_init.locationews_options;
    var locationews_user    = locationews_map_init.locationews_user;

    if (locationews_meta.latlng && 0 !== locationews_meta.latlng.length) {
      coordinates = locationews_meta.latlng.split(',');
    } else {
      if (locationews_user.location && 0 !== locationews_user.location.length) {
        coordinates = locationews_user.location.split(',');
      } else {
        coordinates = locationews_options.location.split(',');
      }
    }

    var locationews_latitude    = coordinates[0];
    var locationews_longitude   = coordinates[1];

    var locationews_location    = new google.maps.LatLng(locationews_latitude, locationews_longitude);
    var locationews_map_options = {
      zoom                   : ( 0 !== locationews_map_init.zoom.length ? parseInt(locationews_map_init.zoom) : 9 ),
      center                 : locationews_location,
      disableDoubleClickZoom : true,
      mapTypeId              : google.maps.MapTypeId.ROADMAP,
      disableDefaultUI       : true,
      zoomControl            : true,
      gestureHandling        : 'greedy',
      streetViewControl      : false,
      styles: [
        { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
        { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
        { "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }] },
        { "elementType": "labels.text.stroke", "stylers": [{ "color": "#f5f5f5 " }] },
        { "featureType": "administrative.land_parcel", "elementType": "labels.text.fill", "stylers": [{ "color": "#bdbdbd" }] },
        { "featureType": "poi", "elementType": "geometry", "stylers": [{ "color": "#eeeeee" }] },
        { "featureType": "poi", "elementType": "labels.text.fill", "stylers": [{ "color": "#757575" }] },
        { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#e5e5e5" }] },
        { "featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }] },
        { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#e05a5a" }] },
        { "featureType": "road.arterial", "elementType": "labels.text.fill", "stylers": [{ "color": "#757575" }] },
        { "featureType": "road.highway", "elementType": "geometry", "stylers": [{ "color": "#e05a5a" }, { "saturation": -40 }, { "lightness": 30 }] },
        { "featureType": "road.highway", "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }]},
        { "featureType": "road.local", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }]},
        { "featureType": "transit.line", "elementType": "geometry", "stylers": [{ "color": "#e5e5e5" }]},
        { "featureType": "transit.station", "elementType": "geometry", "stylers": [{ "color": "#eeeeee" }]},
        { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#c9d9d9" }]},
        { "featureType": "water", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }]}
      ]
    };

    var compare_latlng = ( locationews_latitude + ',' + locationews_longitude );

    if (locationews_meta.latlng !== compare_latlng) {
      $('#locationews-location').val(locationews_latitude + ',' + locationews_longitude);
    }
  
    var locationews_map = new google.maps.Map(
      document.getElementById('locationews-google-map'),
      locationews_map_options
    );

    var geocoder = new google.maps.Geocoder;

    placeLocationewsMarker(locationews_location, '', locationews_map, geocoder);

    locationews_map.addListener('dblclick', function (event) {
      placeLocationewsMarker(event.latLng, '', locationews_map, geocoder);
    });

    if (typeof google.maps.places != 'object') {
      $('#locationews-pac-input').hide();
    } else {
      // Google map places is loaded

      // Create the search box and link it to the UI element.
      var input = document.getElementById('locationews-pac-input');
      var searchBox = new google.maps.places.SearchBox(input);

      locationews_map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

      // Bias the SearchBox results towards current map's viewport.
      locationews_map.addListener('bounds_changed', function () {
        searchBox.setBounds(locationews_map.getBounds());
      });

      var markers = [];

      // Listen for the event fired when the user selects a prediction and retrieve more details for that place.
      searchBox.addListener('places_changed', function () {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
          return;
        }

        // Clear out the old markers.
        markers.forEach(function (marker) {
          marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.every(function (place, index) {
          if (!place.geometry) {
            return;
          }

          placeLocationewsMarker(place.geometry.location, '', locationews_map, geocoder)

          if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
          } else {
            bounds.extend(place.geometry.location);
          }
          // Show only one place (the first one)
          return false;
        });

        locationews_map.fitBounds(bounds);
      });
    } // google.map.places loaded

    function placeLocationewsMarker( location, title, map, geocoder ) {
      if ( locationews_marker ) {
        locationews_marker.setPosition( location );
        $('#locationews-location').val(location.lat() + ',' + location.lng());
      } else {
        locationews_marker = new google.maps.Marker({
          position:   location,
          map:        map,
          title:      '',
          draggable:  true,
          icon:       ( 0 !== locationews_map_init.icon.length ? locationews_map_init.icon : locationews_map_init.plugin_url + 'img/icon-small.png')
        });
      }

      locationews_marker.addListener('drag', function( event ) {
        $('#locationews-location').val( event.latLng.lat() + ',' + event.latLng.lng() );
      });

      locationews_marker.addListener('dragend', function( event ) {
        $('#locationews-location').val( event.latLng.lat() + ',' + event.latLng.lng() );
        geocodeLatLng( geocoder, map, event.latLng.lat(), event.latLng.lng() );
      });

      geocodeLatLng( geocoder, map, location.lat(), location.lng() );
    } // placeLocationewsMarker

    function geocodeLatLng( geocoder, map, lat, lng ) {
      if ( typeof( lat ) != 'undefined' && typeof( lng ) != 'undefined' ) {
        var latlng = {
          lat: parseFloat( lat ),
          lng: parseFloat( lng )
        };
      } else {
        var latlngStr = $('#locationews-location').val().split(',', 2);
        var latlng = {
          lat: parseFloat( latlngStr[0] ),
          lng: parseFloat( latlngStr[1] )
        };
      }

      geocoder.geocode( {'location': latlng}, function( results, status ) {
        if ( status === 'OK' ) {
          if ( results[0] ) {
            $('#locationews-pac-input').val( results[0].formatted_address );
            $('#locationews-pac-input').removeAttr('placeholder');
          }  else {
            $('#locationews-pac-input').val('');
          }
        } else {
          $('#locationews-pac-input').val('');
        }
      });
    } // geocodeLatLng

    if( 0 !== $('#locationews-pac-input').val().length ) {
      $('#locationews-pac-input').removeAttr('placeholder');
    }

    $('#locationews-pac-input').focus(function() {
      $(this).data('oldvalue', $(this).val() );
      $(this).val('');
      $(this).attr('placeholder', locationews_map_init.map_search_placeholder );
    }).blur(function() {
      if( $(this).val() == '') {
        $(this).val( $(this).data('oldvalue') );
      }
    });

    $(window).keydown( function( event ) {
      if ( event.keyCode == 13 ) {
        event.preventDefault();
        return false;
      }
    });

    $('#locationews-location').keyup(function() {
      var $this = $(this);
      $this.val( $this.val().replace(/[^\d.,]/g, '') );
    });

  });

})(jQuery);
