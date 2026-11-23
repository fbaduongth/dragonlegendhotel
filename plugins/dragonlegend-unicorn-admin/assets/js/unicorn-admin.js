/**
 * Dragon Legend - Unicorn Admin UI JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // 1. Dynamic Greeting based on current local time
        updateGreetingMessage();

        // 2. Format Post Statuses in WP List Tables into Vibrant Badges
        formatTableBadges();

        // 3. Smooth handling for Admin Notices
        handleAdminNotices();
    });

    /**
     * Update greeting text based on hour of day
     */
    function updateGreetingMessage() {
        var greetingEl = document.getElementById('dl-greeting-time');
        if (!greetingEl) return;

        var hour = new Date().getHours();
        var greeting = 'Chào ngày mới năng động! ⚡';

        if (hour >= 5 && hour < 12) {
            greeting = 'Chào buổi sáng năng động! 🌅';
        } else if (hour >= 12 && hour < 18) {
            greeting = 'Chào buổi chiều tràn đầy năng lượng! ⚡';
        } else {
            greeting = 'Chào buổi tối thảnh thơi! 🌙';
        }

        greetingEl.textContent = greeting;
    }

    /**
     * Enhance status text into modern pill badges in list tables
     */
    function formatTableBadges() {
        $('.wp-list-table .post-state').each(function() {
            var $state = $(this);
            var text = $state.text().trim();

            if (text.indexOf('Bản nháp') !== -1 || text.indexOf('Draft') !== -1) {
                $state.addClass('dl-badge dl-badge-draft');
            } else if (text.indexOf('Riêng tư') !== -1 || text.indexOf('Private') !== -1) {
                $state.addClass('dl-badge dl-badge-private');
            } else if (text.indexOf('Chờ duyệt') !== -1 || text.indexOf('Pending') !== -1) {
                $state.addClass('dl-badge dl-badge-pending');
            } else {
                $state.addClass('dl-badge dl-badge-default');
            }
        });
    }

    /**
     * Enhance admin notices with clean close animation
     */
    function handleAdminNotices() {
        $('.notice.is-dismissible').on('click', '.notice-dismiss', function() {
            var $notice = $(this).closest('.notice');
            $notice.fadeOut(250, function() {
                $(this).remove();
            });
        });
    }

})(jQuery);
