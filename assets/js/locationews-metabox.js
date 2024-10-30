(function ($) {

  $(function() {
    
    $.fn.invisible = function() {
      return this.each(function() {
        $(this).css('visibility', 'hidden');
      });
    };

    $.fn.visible = function() {
      return this.each(function() {
        $(this).css('visibility', 'visible');
      });
    };

    if ( typeof locationews_metabox_init !== 'undefined' ) {
      var action                 = locationews_metabox_init.action;
      var post_type              = locationews_metabox_init.post_type;
      var display_metabox_always = locationews_metabox_init.display_metabox_always;
      var display_metabox        = locationews_metabox_init.display_metabox;
      var catids                 = locationews_metabox_init.catids;
      var locationewson          = locationews_metabox_init.locationewson;
    } else {
      return;
    }
    // Bootstrap checkbox
    $('[type="checkbox"].locationews').bootstrapSwitch();
    
    $('[type="checkbox"].locationews').on('switchChange.bootstrapSwitch', function(event, state) {
      if ( true === state ) {
        $('#locationews_hidden').val(1);
      } else {
        $('#locationews_hidden').val(0);
      }
    });

    if ( post_type != 'page' && 1 != display_metabox_always ) {
      // Set metabox invisible by default
      $('#locationews').invisible();
      $('input[name="locationews"]').bootstrapSwitch('state', false, true);

      $( document ).on( 'click', '#categorychecklist input[type="checkbox"]', function() {

        $('#locationews').invisible();
        $('input[name="locationews"]').bootstrapSwitch('state', false, true);

        $('#categorychecklist input[type="checkbox"]').each( function( i, e ) {
          var id = $(this).attr('id').match(/-([0-9]*)$/i);
          id = ( id && id[1] ) ? parseInt( id[1] ) : null ;

          if ( $.inArray( id, catids ) > -1 && $(this).is(':checked') ) {
            if ( $('#locationews_hidden').val() == true ) {
              $('input[name="locationews"]').bootstrapSwitch('state', true, true);
            } else {
              $('input[name="locationews"]').bootstrapSwitch('state', false, true);
            }
            $('#locationews').visible();
          }
        });
      });
    }
    if ( action != 'add' ) {
      if ( 1 == display_metabox || 1 == locationewson ) {
        if ( $('#locationews_hidden').val() == true ) {
          $('input[name="locationews"]').bootstrapSwitch('state', true, true);
        } else {
          $('input[name="locationews"]').bootstrapSwitch('state', false, true);
        }
        $('#locationews').visible();
      }
    }
  });

})(jQuery);
