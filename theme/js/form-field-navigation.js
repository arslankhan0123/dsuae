(function ($) {
  'use strict';

  $(function () {
    $('#items-form, #sales-form').each(function () {
      var form = this;
      var pendingSelect = null;

      function fields() {
        return $(form).find('input, select, textarea, .box-footer button').filter(function () {
          var field = $(this);
          if (this.disabled || this.type === 'hidden') return false;
          if (this.readOnly && !field.hasClass('datepicker')) return false;
          if (field.is('select.select2-hidden-accessible')) {
            return field.next('.select2-container').is(':visible');
          }
          return field.attr('tabindex') !== '-1' && field.is(':visible');
        });
      }

      function move(current, backwards) {
        var controls = fields();
        var index = controls.index(current);
        if (index < 0) return;
        var nextIndex = index + (backwards ? -1 : 1);
        if (nextIndex < 0 || nextIndex >= controls.length) return;
        var next = controls.eq(nextIndex);
        if (next.is('select.select2-hidden-accessible')) {
          next.next('.select2-container').find('.select2-selection').focus();
        } else {
          next.focus();
        }
      }

      document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' || event.isComposing || event.ctrlKey || event.altKey) return;
        var target = $(event.target);
        var openSelect = $(form).find('select.select2-hidden-accessible').filter(function () {
          return $(this).next('.select2-container').hasClass('select2-container--open');
        }).first();
        if (openSelect.length && (target.hasClass('select2-search__field') || $.contains(form, event.target))) {
          pendingSelect = openSelect[0];
          return; // Select2 handles the choice before advancing.
        }
        if (!$.contains(form, event.target) || !target.is('input, select, textarea')) return;
        if (target.attr('id') === 'item_search') {
          var autocomplete = target.data('ui-autocomplete');
          if (autocomplete && autocomplete.menu.element.is(':visible')) return;
        }
        if (target.is(':button, :submit, :reset, :file')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        move(event.target, event.shiftKey);
      }, true);

      $(form).on('select2:select', 'select', function () {
        if (pendingSelect !== this) return;
        var selected = this;
        pendingSelect = null;
        setTimeout(function () { move(selected, false); }, 0);
      });
      $(form).on('select2:close', 'select', function () {
        setTimeout(function () { pendingSelect = null; }, 0);
      });
    });
  });
})(jQuery);
