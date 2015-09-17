(function ($) {
    $(document).ready(function() {
        $('li.selector-child').on('click', function(e){
            e.stopPropagation();
            //looks like a stopPropagation on the selector-parent forces
            //me to bind the event lower down the DOM, then work back
            //up to the li
            var targetLi = $(e.target).closest('li.selector-child');
            copyTemplate(targetLi);
        });

        filteredPropertyIds.forEach(initProperties);
    });

    function initProperties(propertyId, index, array) {
        var propertyLi = $('li[data-property-id =' + propertyId + ']');
        if (propertyLi.length !== 0) {
            copyTemplate(propertyLi);
        }
    }

    function copyTemplate(targetLi) {
        var id = targetLi.data('property-id');
        var label = targetLi.data('child-search');
        var description = targetLi.find('p.field-comment').html();
        var templateClone = $('.template').clone();
        templateClone.removeClass('template');
        templateClone.find('legend').html(label);
        templateClone.find('div.field-description').html(description);
        templateClone.find('input.property-ids').val(id);
        templateClone.find('input.property-ids').prop('disabled', false);
        $('#properties').append(templateClone);
    }

})(jQuery);
