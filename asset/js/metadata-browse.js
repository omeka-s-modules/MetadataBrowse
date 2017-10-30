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

        
        // Remove property
        $('a.remove-property').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var propertyToRemove = $(this).parents('.field');
            propertyToRemove.find('input').prop('disabled', true);
            propertyToRemove.addClass('delete');
            propertyToRemove.find('.restore-property').show();
            $(this).hide();
        });

        // Restore a removed property
        $('a.restore-property').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var propertyToRemove = $(this).parents('.field');
            propertyToRemove.find('.remove-property').show();
            propertyToRemove.find('span.restore-property').hide();
            propertyToRemove.find('input').prop('disabled', false);
            propertyToRemove.removeClass('delete');
            $(this).hide();
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
        //check if it has already been added
        var skip = false;
        $('.property-ids').each(function() {
            if ($(this).val() == id) {
                skip = true;
            }
        });
        if (skip) {
            return;
        }

        var label = targetLi.data('child-search');
        var description = targetLi.find('p.field-comment').html();
        var term = targetLi.data('property-term');
        var templateClone = $('.template').clone(true);
        templateClone.removeClass('template');
        templateClone.find('div.field-label').html(label);
        templateClone.find('div.field-description').html(description);
        templateClone.find('div.field-term').html(term);
        templateClone.find('input.property-ids').val(id);
        templateClone.find('input.property-ids').prop('disabled', false);
        $('#metadata-browse-properties').append(templateClone);
    }

})(jQuery);
