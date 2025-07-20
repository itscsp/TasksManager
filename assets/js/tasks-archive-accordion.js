jQuery(document).ready(function($) {
    // Only target the Archives accordion with unique classes
    $('.archives-accordion-header').on('click keypress', function(e) {
        if (e.type === 'click' || (e.type === 'keypress' && (e.which === 13 || e.which === 32))) {
            var $item = $(this).closest('.archives-accordion-item');
            var $icon = $(this).find('.accordion-icon');
            if ($item.hasClass('open')) {
                $item.removeClass('open');
                $item.find('> .accordion-content').slideUp(250);
                $icon.removeClass('open');
                // Always close all child accordions and reset their icons
                $item.find('.accordion-item').removeClass('open');
                $item.find('.accordion-item .accordion-icon').removeClass('open');
                $item.find('.accordion-item > .accordion-content').hide();
            } else {
                // Only close other open archives accordions, not all accordions
                $('.archives-accordion-item.open').removeClass('open').find('> .accordion-content').slideUp(250);
                $('.archives-accordion-header .accordion-icon').removeClass('open');
                $item.addClass('open');
                $item.find('> .accordion-content').slideDown(350, function() {
                    // After opening, ensure all child accordions are closed and icons reset
                    $item.find('.accordion-item').removeClass('open');
                    $item.find('.accordion-item .accordion-icon').removeClass('open');
                    $item.find('.accordion-item > .accordion-content').hide();
                });
                $icon.addClass('open');
            }
        }
    });
    // Optionally, open the archives by default:
    // $('.archives-accordion-item').first().addClass('open').find('.accordion-content').show();
});
