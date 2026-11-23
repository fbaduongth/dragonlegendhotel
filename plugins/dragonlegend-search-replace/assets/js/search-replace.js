jQuery(document).ready(function($) {
    var $dryRunCheckbox = $('#dl-dry-run');
    var $submitBtn = $('#dl-submit-btn');
    var $btnText = $('#dl-btn-text');

    function updateButtonState() {
        if ($dryRunCheckbox.is(':checked')) {
            $btnText.text('Bắt đầu Chạy Thử (Dry Run)');
            $submitBtn.removeClass('dl-live-mode');
        } else {
            $btnText.text('⚠️ Bắt đầu Thay Thế Thật Vào CSDL');
            $submitBtn.addClass('dl-live-mode');
        }
    }

    $dryRunCheckbox.on('change', updateButtonState);
    updateButtonState();

    // Select all / None
    $('#dl-select-all').on('click', function(e) {
        e.preventDefault();
        $('.dl-table-checkbox').prop('checked', true);
    });

    $('#dl-select-none').on('click', function(e) {
        e.preventDefault();
        $('.dl-table-checkbox').prop('checked', false);
    });

    // Preset button
    $('.dl-preset-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        $('#search_string').val(url).focus();
    });

    // Form submit confirmation
    $('#dl-sr-form').on('submit', function(e) {
        var isDryRun = $dryRunCheckbox.is(':checked');
        var selectedCount = $('.dl-table-checkbox:checked').length;
        var searchVal = $('#search_string').val().trim();

        if (searchVal === '') {
            alert('Vui lòng nhập chuỗi tìm kiếm!');
            $('#search_string').focus();
            return false;
        }

        if (selectedCount === 0) {
            alert('Vui lòng chọn ít nhất một bảng trong cơ sở dữ liệu!');
            return false;
        }

        if (!isDryRun) {
            var confirmed = confirm('CẢNH BÁO QUAN TRỌNG:\n\nBạn đang chuẩn bị thay đổi TRỰC TIẾP vào Cơ sở dữ liệu.\nThao tác này sẽ ghi đè dữ liệu thật và không thể hoàn tác nếu chưa có bản sao lưu.\n\nBạn có chắc chắn muốn tiếp tục không?');
            if (!confirmed) {
                return false;
            }
        }

        $submitBtn.prop('disabled', true).css('opacity', '0.7');
        $btnText.text('Đang xử lý dữ liệu... Vui lòng đợi');
        return true;
    });
});
