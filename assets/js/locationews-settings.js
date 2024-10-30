(function ($) {

  $(function() {

    // bootstrapSwitch
    $("[type='checkbox'].locationews").bootstrapSwitch();

    if ($('input[id="locationews_categories_all"]').is(':checked')) {
      $('.locationews-defaultCategories:not(#locationews_categories_all)').bootstrapSwitch('readonly', true, true);
    }

    $('input[id="locationews_categories_all"]').on('switchChange.bootstrapSwitch', function (event, state) {
      if (state === true) {
        $('.locationews-defaultCategories').bootstrapSwitch('state', true, true);
        $('.locationews-defaultCategories:not(#locationews_categories_all)').bootstrapSwitch('readonly', true, true);
      } else {
        $('.locationews-defaultCategories').bootstrapSwitch('readonly', false, true);
        $('.locationews-defaultCategories:not(#locationews_categories_all)').bootstrapSwitch('state', false, true);
      }
    });

  });

})(jQuery);
