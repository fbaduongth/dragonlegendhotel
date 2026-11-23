/**
 * Dragon Legend Content Manager - Admin Media & Tabs Handler
 */

jQuery(document).ready(function ($) {
    // 1. Chuyển Tab
    $('.dl-tab-btn').on('click', function (e) {
        e.preventDefault();
        var targetTab = $(this).data('tab');

        $('.dl-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.dl-tab-pane').removeClass('active');
        $('#' + targetTab).addClass('active');

        // Lưu tab vừa chọn vào localStorage
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('dl_active_tab', targetTab);
        }
    });

    // Khôi phục tab đã mở gần nhất
    if (typeof localStorage !== 'undefined') {
        var savedTab = localStorage.getItem('dl_active_tab');
        if (savedTab && $('#' + savedTab).length) {
            $('.dl-tab-btn[data-tab="' + savedTab + '"]').trigger('click');
        }
    }

    // Chuyển nhanh tab từ nút liên kết trong trang
    $(document).on('click', '.dl-switch-tab-btn', function (e) {
        e.preventDefault();
        var targetTab = $(this).data('target');
        if (targetTab && $('.dl-tab-btn[data-tab="' + targetTab + '"]').length) {
            $('.dl-tab-btn[data-tab="' + targetTab + '"]').trigger('click');
            $('html, body').animate({ scrollTop: $('.dl-tabs-nav').offset().top - 40 }, 300);
        }
    });

    // 2. Tích hợp WordPress Media Uploader
    $('.dl-upload-btn').on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        var inputField = button.siblings('input[type="text"], input[type="url"]');
        var previewContainer = button.closest('.dl-form-group').find('.dl-image-preview');

        var customUploader = wp.media({
            title: 'Chọn hoặc tải ảnh lên cho website Dragon Legend',
            button: {
                text: 'Sử dụng ảnh này'
            },
            multiple: false
        }).on('select', function () {
            var attachment = customUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url).trigger('change');

            if (previewContainer.length) {
                previewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
            }
        }).open();
    });

    // Cập nhật preview khi người dùng tự gõ URL
    $('input[data-preview]').on('input change', function () {
        var val = $(this).val();
        var previewSelector = $(this).data('preview');
        var preview = $(previewSelector);

        if (preview.length) {
            if (val) {
                preview.html('<img src="' + val + '" alt="Preview">');
            } else {
                preview.html('<span class="placeholder">Chưa có ảnh</span>');
            }
        }
    });
});
