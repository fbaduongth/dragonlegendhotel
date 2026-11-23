/**
 * Dragon Legend Hotel Core - Admin JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Room Gallery Media Uploader
        $('#dl-add-gallery-btn').on('click', function(e) {
            e.preventDefault();

            var custom_uploader = wp.media({
                title: 'Chọn ảnh cho Thư viện phòng',
                button: {
                    text: 'Thêm vào bộ sưu tập'
                },
                multiple: true
            }).on('select', function() {
                var selection = custom_uploader.state().get('selection');
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    var html = '<div class="dl-gallery-item" style="position:relative; width:100px; height:100px; border-radius:6px; overflow:hidden; border:1px solid #ccc;">' +
                        '<img src="' + attachment.url + '" style="width:100%; height:100%; object-fit:cover;">' +
                        '<input type="hidden" name="_dl_room_gallery[]" value="' + attachment.url + '">' +
                        '<button type="button" class="dl-remove-img" style="position:absolute; top:2px; right:2px; background:rgba(200,0,0,0.8); color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; line-height:18px;">&times;</button>' +
                        '</div>';
                    $('#dl-gallery-preview').append(html);
                });
            }).open();
        });

        // Xóa ảnh khỏi gallery
        $(document).on('click', '.dl-remove-img', function(e) {
            e.preventDefault();
            $(this).closest('.dl-gallery-item').remove();
        });
    });
})(jQuery);
