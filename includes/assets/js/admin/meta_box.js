(function($) {
  $.fn.activateRelatedSection = function() {
    return this.each(function() {
      var $container = $(this);
      var onRadioChangeHandler = function() {
        var $radio = $(this);
        var $siblingRadios = $container.find('[name=' + $radio.attr('name') + ']');
        $radio.add($siblingRadios).each(function() {
          var $radio = $(this);
          var relatedSectionSelector = $radio.data('rel-selector');
          if (relatedSectionSelector) {
            $(relatedSectionSelector)[($radio.prop('checked') ? 'remove' : 'add') + 'Class']('hidden');
          }
        });
      };
      $container.on('change', '[type=radio]', onRadioChangeHandler);
      // do this once on page load so the state of the radios is reflected in which sections are visible
      $container.find('[type=radio]').each(onRadioChangeHandler);
    });
  };
}(jQuery));

(function($) {
  $(function() {
    var container = $('.cronblocks-snippet-meta');
    // priority
    $('#priority_field').knob({
      width : 100,
      height : 60,
      angleOffset : -90,
      angleArc : 180,
      fgColor : '#21759b',
      bgColor : '#ddd'
    });
    // timepicker
    container.find('.hours-input').knob({
      min : 0,
      max : 23,
      width : 50,
      height: 50,
      fgColor : '#21759b',
      bgColor : '#ddd'
    });
    container.find('.minutes-input').knob({
      min    : 0,
      max    : 59,
      width  : 50,
      height : 50,
      fgColor : '#21759b',
      bgColor : '#ddd'
    });
    // multiselects
    // country
    $('#usc_cb_country').select2();

    // weekdays
    $('#weekdays_selection').select2();
    $('#weekdays_selection').siblings('.select-all').on('click', function() {
      $('#weekdays_selection option').prop('selected', true);
      $('#weekdays_selection').trigger('change');
    });

    // months
    $('#months_selection').select2();
    $('#months_selection').siblings('.select-all').on('click', function() {
      $('#months_selection option').prop('selected', true);
      $('#months_selection').trigger('change');
    });

    // day of the month
    $('#month_days').select2();
    $('#month_days').siblings('.select-all').on('click', function() {
      $('#month_days option').prop('selected', true);
      $('#month_days').trigger('change');
    });

    // section toggling radios
    container.activateRelatedSection();

    // when everything is initialized, uncover the whole thing
    container.removeClass('hidden');
    $('p.loading').hide();
  });
}(jQuery));