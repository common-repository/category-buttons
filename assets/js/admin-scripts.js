jQuery(document).ready(function ($) {
    // Initialize color pickers for existing elements
    $(".cb-color-field").wpColorPicker();

    // Image select
    $('.image-select-wrapper .image-select-item').on('click', function () {
        $('.image-select-wrapper .image-select-item').removeClass('selected');
        $(this).addClass('selected');
        $('input[name="cb_basic_settings[cb_store_display_style_field]"]').val($(this).data('value'));
    });

    // Exclude categories
    var checkboxes = $('input[name="cb_exclude_categories[]"]');
    var hiddenField = $('#cb_exclude_categories');

    checkboxes.on('change', function () {
        var selectedCategories = checkboxes.filter(':checked').map(function () {
            return this.value;
        }).get();

        hiddenField.val(selectedCategories.join(','));
    });

    // Copy shortcode
    function copyToClipboard(text) {
        var tempInput = document.createElement('input');
        tempInput.style.position = 'absolute';
        tempInput.style.left = '-9999px';
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
    }

    $('.shortcode-category-select').on('change', function () {
        var selectedCategory = $(this).val();
        $('.short-copy-item').attr('data-category', selectedCategory);
    });

    $('.shortcode-image-select').on('change', function () {
        var selectedImageDisplay = $(this).val();
        $('.short-copy-item').attr('data-images', selectedImageDisplay);
    });

    $(document).on('click', '.short-copy-wrapper .short-copy-item', function () {
        var style = $(this).data('style');
        var category = $(this).attr('data-category') || 0;
        var images = $(this).attr('data-images') || 1;
        var copyText = '[woocb ' + style + ' category="' + category + '" display-images="' + images + '"]';
        copyToClipboard(copyText);
        alert('Copied: ' + copyText);
    });

    var repeaterFieldWrapper = $('#repeater-field-wrapper');

    $('#add-repeater-field').on('click', function () {
        var index = repeaterFieldWrapper.find('.repeater-field-item').length;
        var newField = repeaterFieldWrapper.find('.repeater-field-item').first().clone();

        // Update the names of the cloned inputs
        newField.find('input').each(function () {
            var name = $(this).attr('name');
            if (name) {
                name = name.replace(/\[\d+\]/, '[' + index + ']');
                $(this).attr('name', name).val('').attr('required', true);
            }
        });

        // Reset the image preview and hide the remove button
        newField.find('img').attr('src', '').hide();
        newField.find('.cb-remove-image').hide(); // Hide the remove button for new fields

        newField.find('.remove-repeater-field').show();

        newField.appendTo(repeaterFieldWrapper);
    });

    repeaterFieldWrapper.on('click', '.remove-repeater-field', function () {
        if (repeaterFieldWrapper.find('.repeater-field-item').length > 1) {
            $(this).closest('.repeater-field-item').remove();
        } else {
            alert('You need to have at least one field.');
        }
    });

    repeaterFieldWrapper.on('click', '.cb-upload-image', function (e) {
        e.preventDefault();
        var button = $(this);
        var field = button.siblings('.cb-image-url');
        var preview = button.siblings('.image-wrapper').find('.cb-image-preview');
        var removeButton = button.siblings('.image-wrapper').find('.cb-remove-image');

        // Ensure wp.media is loaded
        if (typeof wp.media !== 'undefined') {
            var frame = wp.media({
                title: 'Select or Upload Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                field.val(attachment.url);
                preview.attr('src', attachment.url).show();
                removeButton.show(); // Show the remove button when an image is selected
            });

            frame.open();
        } else {
            console.log('wp.media is not loaded.');
        }
    });

    repeaterFieldWrapper.on('click', '.cb-remove-image', function (e) {
        e.preventDefault();
        var button = $(this);
        var field = button.closest('.image-wrapper').siblings('.cb-image-url');
        var preview = button.siblings('.cb-image-preview');

        field.val('');
        preview.attr('src', '').hide();
        button.hide(); // Hide the remove button when the image is removed
    });

    repeaterFieldWrapper.on('change', '.repeater-category-select', function () {
        var selectElement = $(this);
        var selectedValue = selectElement.val();
        var hiddenInput = selectElement.siblings('.repeater-category-input');
        hiddenInput.val(selectedValue);
    });
});
