  <!-- ========================================== -->
  <!-- 11. LUXURY FOOTER                          -->
  <!-- ========================================== -->
  <footer class="bg-navy-deep text-slate-300 pt-16 pb-10 border-t border-white/10" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
      
      <!-- Col 1: Brand Info -->
      <div class="lg:col-span-4 space-y-4">
        <div class="flex items-center gap-3">
          <?php
          $footer_logo_src = function_exists('dl_get_content') ? dl_get_content('footer_logo_url', '') : '';
          if (empty($footer_logo_src)) {
              $footer_logo_src = function_exists('dl_get_content') ? dl_get_content('header_logo_url', 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png') : 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';
          }
          $footer_title_val = function_exists('dl_get_content') ? dl_get_content('footer_brand_title', 'Dragon Legend Hotel Hạ Long') : 'Dragon Legend Hotel Hạ Long';
          $footer_sub_val   = function_exists('dl_get_content') ? dl_get_content('footer_brand_subtitle', 'HẠ LONG ★★★★') : 'HẠ LONG ★★★★';
          ?>
          <img src="<?php echo esc_url($footer_logo_src); ?>" alt="<?php echo esc_attr($footer_title_val); ?>" class="h-12 w-auto">
          <div>
            <span class="font-display font-bold text-lg text-white block leading-tight"><?php echo esc_html($footer_title_val); ?></span>
            <span class="text-[10px] text-gold uppercase tracking-widest block font-medium mt-0.5"><?php echo esc_html($footer_sub_val); ?></span>
          </div>
        </div>
        <p class="text-xs text-slate-400 leading-relaxed">
          <?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('footer_desc', 'Được ví như một "Viên ngọc rực rỡ" giữa lòng kỳ quan thiên nhiên thế giới Vịnh Hạ Long. Tận hưởng chuẩn mực nghỉ dưỡng tân cổ điển đỉnh cao với 138 phòng tiện nghi, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo.') : 'Được ví như một "Viên ngọc rực rỡ" giữa lòng kỳ quan thiên nhiên thế giới Vịnh Hạ Long. Tận hưởng chuẩn mực nghỉ dưỡng tân cổ điển đỉnh cao với 138 phòng tiện nghi, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo.'); ?>
        </p>
        <div class="flex items-center gap-3 pt-2">
          <a href="<?php echo esc_url(dragonlegend_get_info('facebook')); ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-slate-300 hover:text-gold hover:bg-white/20 transition-all" aria-label="Facebook">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="<?php echo esc_url(dragonlegend_get_info('tiktok')); ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-slate-300 hover:text-gold hover:bg-white/20 transition-all" aria-label="TikTok">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
          </a>
          <a href="<?php echo esc_url(dragonlegend_get_info('youtube')); ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-slate-300 hover:text-gold hover:bg-white/20 transition-all" aria-label="YouTube">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
        </div>
      </div>

      <!-- Col 2: Navigation Links (Thông Tin) -->
      <div class="lg:col-span-2 space-y-3">
        <h4 class="font-bold text-white text-sm uppercase tracking-wider">
          <?php
          $info_menu_title = 'Thông tin';
          $locations = get_nav_menu_locations();
          if (!empty($locations['footer_info'])) {
              $menu_obj = wp_get_nav_menu_object($locations['footer_info']);
              if ($menu_obj && !empty($menu_obj->name)) {
                  $info_menu_title = $menu_obj->name;
              }
          }
          echo esc_html($info_menu_title);
          ?>
        </h4>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer_info',
            'container'      => false,
            'items_wrap'     => '<ul class="space-y-2 text-xs text-slate-400">%3$s</ul>',
            'depth'          => 1,
            'walker'         => new DragonLegend_Footer_Nav_Walker(),
            'fallback_cb'    => 'dragonlegend_default_footer_info_nav',
        ));
        ?>
      </div>

      <!-- Col 3: Policies (Quy định & Chính sách) -->
      <div class="lg:col-span-3 space-y-3">
        <h4 class="font-bold text-white text-sm uppercase tracking-wider">
          <?php
          $policy_menu_title = 'Quy định & Chính sách';
          if (!empty($locations['footer_policy'])) {
              $menu_obj = wp_get_nav_menu_object($locations['footer_policy']);
              if ($menu_obj && !empty($menu_obj->name)) {
                  $policy_menu_title = $menu_obj->name;
              }
          }
          echo esc_html($policy_menu_title);
          ?>
        </h4>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer_policy',
            'container'      => false,
            'items_wrap'     => '<ul class="space-y-2 text-xs text-slate-400">%3$s</ul>',
            'depth'          => 1,
            'walker'         => new DragonLegend_Footer_Nav_Walker(),
            'fallback_cb'    => 'dragonlegend_default_footer_policy_nav',
        ));
        ?>
      </div>

      <!-- Col 4: Newsletter -->
      <div class="lg:col-span-3 space-y-3">
        <h4 class="font-bold text-white text-sm uppercase tracking-wider"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_newsletter_title', 'Đăng ký nhận ưu đãi') : 'Đăng ký nhận ưu đãi'); ?></h4>
        <p class="text-xs text-slate-400"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_newsletter_desc', 'Nhận ngay mã giảm 10% cho lần đặt phòng kế tiếp cùng các gói combo độc quyền.') : 'Nhận ngay mã giảm 10% cho lần đặt phòng kế tiếp cùng các gói combo độc quyền.'); ?></p>
        <form id="footer-newsletter-form" class="space-y-2.5" novalidate>
          <div class="relative">
            <input type="email" id="footer-newsletter-email" name="subscriber_email" required 
                   placeholder="Nhập địa chỉ email của bạn" 
                   class="w-full px-3.5 py-2.5 rounded-lg bg-white/10 border border-white/20 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-gold transition-colors">
          </div>
          <button type="submit" id="footer-newsletter-btn" class="w-full py-2.5 bg-gold hover:bg-gold-500 text-navy-deep font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-md hover:shadow-gold/20">
            <span id="footer-newsletter-btn-text"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_newsletter_btn', 'Đăng ký ngay') : 'Đăng ký ngay'); ?></span>
            <svg id="footer-newsletter-btn-icon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </button>
          <div id="footer-newsletter-msg" class="hidden text-xs py-2 px-3 rounded-lg leading-relaxed transition-all"></div>
        </form>
      </div>

    </div>

    <!-- Copyright -->
    <div class="max-w-7xl mx-auto px-4 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
      <p><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_copyright', '© 2026 Dragon Legend Hotel Ha Long. All rights reserved.') : '© ' . date('Y') . ' Dragon Legend Hotel. All rights reserved.'); ?></p>
      <div class="flex items-center gap-6">
        <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_subtext_1', 'Tiêu chuẩn 4 Sao Quốc Tế') : 'Tiêu chuẩn 4 Sao Quốc Tế'); ?></span>
        <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('footer_subtext_2', 'Bãi Cháy, Quảng Ninh') : 'Bãi Cháy, Quảng Ninh'); ?></span>
      </div>
    </div>
  </footer>
  </div><!-- /#site-wrapper -->

  <!-- ========================================== -->
  <!-- 12. INTERACTIVE RESERVATION MODAL          -->
  <!-- ========================================== -->
  <div id="booking-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gold/40 flex flex-col">
      <!-- Modal Header -->
      <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-md z-10">
        <div>
          <span class="text-xs font-bold text-gold uppercase tracking-wider block">Đặt phòng trực tuyến</span>
          <h3 class="font-display text-xl font-bold text-navy-deep">Hoàn Tất Yêu Cầu Đặt Phòng</h3>
        </div>
        <button id="close-booking-modal" class="p-2 text-slate-400 hover:text-slate-700 rounded-full hover:bg-slate-100" aria-label="Đóng popup">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-6">
        <!-- Room Summary Box -->
        <div class="flex gap-4 p-4 rounded-2xl bg-amber-50/60 border border-amber-200/60 items-center">
          <img id="summary-room-img" src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg" alt="Phòng" class="w-24 h-20 rounded-xl object-cover">
          <div>
            <span class="text-[10px] font-bold text-gold uppercase tracking-widest block">Hạng phòng đã chọn</span>
            <h4 id="summary-room-name" class="font-display font-bold text-base text-navy-deep">Phòng Superior</h4>
            <p id="summary-stay-info" class="text-xs text-slate-600 mb-1">Ngày nhận phòng — Ngày trả phòng</p>
            <span id="summary-room-price" class="text-sm font-bold text-amber-700">1.050.000 VNĐ / đêm</span>
          </div>
        </div>

        <!-- Booking Form -->
        <form id="reservation-form" class="space-y-4">
          <!-- Room selector in modal -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Thay đổi hạng phòng</label>
            <select id="modal-room-select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-gold"></select>
          </div>

          <!-- Dates & Guests in modal -->
          <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ngày nhận phòng</label>
              <input type="date" id="modal-checkin" class="w-full px-2 py-2 rounded-lg border border-slate-200 text-xs">
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ngày trả phòng</label>
              <input type="date" id="modal-checkout" class="w-full px-2 py-2 rounded-lg border border-slate-200 text-xs">
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Số phòng</label>
              <input type="number" id="modal-rooms" min="1" max="10" value="1" class="w-full px-2 py-2 rounded-lg border border-slate-200 text-xs">
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Người lớn</label>
              <input type="number" id="modal-adults" min="1" max="30" value="2" class="w-full px-2 py-2 rounded-lg border border-slate-200 text-xs">
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Trẻ em (&lt;12t)</label>
              <input type="number" id="modal-children" min="0" max="15" value="0" class="w-full px-2 py-2 rounded-lg border border-slate-200 text-xs">
            </div>
          </div>

          <!-- Customer details -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên quý khách <span class="text-rose-500">*</span></label>
              <input type="text" id="guest-name" required placeholder="Nguyễn Văn A" 
                     oninvalid="this.setCustomValidity('Vui lòng nhập họ và tên của quý khách')"
                     oninput="this.setCustomValidity('')"
                     class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-gold">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
              <input type="tel" id="guest-phone" required placeholder="0987 654 321" 
                     oninvalid="this.setCustomValidity('Vui lòng nhập số điện thoại liên hệ')"
                     oninput="this.setCustomValidity('')"
                     class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-gold">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ Email (Nhận mã xác nhận đặt phòng)</label>
            <input type="email" id="guest-email" placeholder="nguyenvana@gmail.com" 
                   oninvalid="this.setCustomValidity('Vui lòng nhập đúng định dạng email (ví dụ: nguyenvana@gmail.com)')"
                   oninput="this.setCustomValidity('')"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-gold">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú đặc biệt (Tùy chọn)</label>
            <textarea id="guest-note" rows="2" placeholder="Ví dụ: Giường đôi lớn, tầng cao hướng biển, nhận phòng sớm..." class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-gold resize-none"></textarea>
          </div>

          <!-- Promo Code -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Mã giảm giá / Mã ưu đãi</label>
            <div class="flex gap-2">
              <input type="text" id="booking-promo-code" placeholder="Nhập mã ưu đãi (ví dụ: DRAGON2026)" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 text-sm uppercase focus:outline-none focus:border-gold">
              <button type="button" id="apply-promo-btn" class="px-5 py-2 bg-navy-deep text-white text-xs font-bold rounded-xl hover:bg-navy transition-colors">
                Áp dụng
              </button>
            </div>
            <div id="promo-message" class="text-xs mt-1"></div>
          </div>

          <!-- Price Calculation Summary -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Tiền phòng tạm tính:</span>
              <span id="summary-total-base" class="font-semibold text-slate-800">1.050.000 VNĐ</span>
            </div>
            <div id="summary-discount-row" class="flex justify-between text-emerald-600 hidden">
              <span>Chiết khấu mã giảm giá:</span>
              <span id="summary-discount-val" class="font-semibold">- 0 VNĐ</span>
            </div>
            <div class="flex justify-between text-slate-600">
              <span>Thuế VAT & Phí dịch vụ (8%):</span>
              <span id="summary-tax-val" class="font-semibold text-slate-800">76.000 VNĐ</span>
            </div>
            <div class="border-t border-slate-200 pt-2 flex justify-between items-center text-sm font-bold text-navy-deep">
              <span>Tổng thanh toán dự kiến:</span>
              <span id="summary-total-val" class="text-lg text-rose-600 font-extrabold">1.026.000 VNĐ</span>
            </div>
            <p class="text-[10px] text-slate-400 italic text-right">* Thanh toán linh hoạt khi nhận phòng hoặc chuyển khoản đặt cọc.</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3 pt-4">
            <button type="button" id="cancel-booking-modal" class="w-1/3 py-3 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-colors">
              Hủy bỏ
            </button>
            <button type="submit" id="submit-booking-btn" class="w-2/3 py-3 rounded-xl bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow-lg hover:brightness-110 transition-all gold-shimmer-btn flex items-center justify-center gap-2">
              <span id="submit-btn-text">Xác nhận đặt phòng</span>
              <span id="submit-btn-spinner" class="hidden animate-spin">&#9696;</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 13. BOOKING SUCCESS CONFIRMATION MODAL     -->
  <!-- ========================================== -->
  <div id="booking-success-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-gold/40 space-y-4" data-aos="zoom-in">
      <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      </div>
      <div>
        <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Thành công</span>
        <h3 class="font-display text-2xl font-bold text-navy-deep">Đặt Phòng Thành Công!</h3>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Yêu cầu của bạn đã được ghi nhận vào hệ thống khách sạn. Bộ phận Lễ tân & Đặt phòng sẽ liên hệ qua điện thoại trong ít phút để hoàn tất xác nhận.
      </p>

      <div class="p-4 rounded-2xl bg-amber-50/70 border border-gold/30 text-left text-xs space-y-2">
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Mã đặt phòng:</span>
          <span id="success-booking-code" class="font-mono font-bold text-sm text-gold bg-navy-deep px-2.5 py-0.5 rounded">DLH-000000</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Khách hàng:</span>
          <span id="success-guest-name" class="font-semibold text-slate-800">—</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Phòng đã chọn:</span>
          <span id="success-room-name" class="font-semibold text-slate-800">—</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Thời gian lưu trú:</span>
          <span id="success-dates" class="font-semibold text-slate-800">—</span>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
          Gọi Hotline hỗ trợ
        </a>
        <button onclick="document.getElementById('booking-success-modal').classList.add('hidden')" class="flex-1 py-3 bg-navy-deep hover:bg-navy text-white font-bold text-xs rounded-xl transition-colors">
          Đóng
        </button>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 14. INTERACTIVE ROOM DETAIL MODAL          -->
  <!-- ========================================== -->
  <div id="room-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop hidden">
    <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gold/40 flex flex-col">
      <!-- Image Header -->
      <div class="relative h-64 sm:h-80 bg-slate-900">
        <img id="detail-room-img" src="" alt="Chi tiết phòng" class="w-full h-full object-cover">
        <button id="close-room-modal" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 text-white hover:bg-black flex items-center justify-center backdrop-blur-md" aria-label="Đóng popup">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between text-white">
          <div>
            <span id="detail-room-badge" class="px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider inline-block mb-2"></span>
            <h3 id="detail-room-name" class="font-display text-2xl sm:text-3xl font-bold">Phòng nghỉ</h3>
            <span id="detail-room-view" class="text-xs bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded">View vịnh</span>
          </div>
          <div class="text-right">
            <span id="detail-room-orig-price" class="text-xs line-through text-slate-300 block"></span>
            <span id="detail-room-price" class="text-xl font-bold text-amber-300"></span>
            <span class="text-[10px] text-slate-200 block">/ đêm</span>
          </div>
        </div>
      </div>

      <!-- Specs & Details -->
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl text-center">
          <div>
            <span class="text-[11px] text-slate-500 block">Diện tích</span>
            <span id="detail-room-size" class="font-bold text-navy-deep text-sm">32 m²</span>
          </div>
          <div>
            <span class="text-[11px] text-slate-500 block">Loại giường</span>
            <span id="detail-room-bed" class="font-bold text-navy-deep text-sm">1 King</span>
          </div>
          <div>
            <span class="text-[11px] text-slate-500 block">Sức chứa</span>
            <span id="detail-room-occ" class="font-bold text-navy-deep text-sm">2 Người</span>
          </div>
        </div>

        <div>
          <h4 class="font-bold text-sm text-navy-deep mb-2 uppercase tracking-wider">Mô tả chi tiết</h4>
          <p id="detail-room-desc" class="text-sm text-slate-600 leading-relaxed"></p>
        </div>

        <div>
          <h4 class="font-bold text-sm text-navy-deep mb-3 uppercase tracking-wider">Tiện nghi có sẵn</h4>
          <div id="detail-room-amenities" class="grid grid-cols-2 sm:grid-cols-4 gap-2"></div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
          <a id="detail-view-page-btn" href="#" class="py-3.5 px-4 sm:px-6 border border-slate-300 text-slate-700 hover:border-gold hover:text-gold hover:bg-amber-50/40 font-bold text-xs uppercase tracking-wider rounded-xl transition-all text-center whitespace-nowrap">
            Xem trang đầy đủ ↗
          </a>
          <button id="detail-book-btn" class="flex-1 py-3.5 bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider rounded-xl hover:brightness-105 shadow-md text-center">
            Đặt phòng này ngay
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 15. VR 360 INTERACTIVE MODAL               -->
  <!-- ========================================== -->
  <div id="vr-modal" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 md:p-6 modal-backdrop hidden" data-vr-url="<?php echo esc_url(dragonlegend_get_info('vr_url')); ?>" role="dialog" aria-modal="true" aria-label="Khám phá Tour VR 360">
    <div class="bg-navy-deep rounded-2xl sm:rounded-3xl max-w-6xl w-full h-[92vh] sm:h-[85vh] shadow-2xl border border-gold/40 flex flex-col overflow-hidden relative">
      <!-- Modal Header -->
      <div class="p-3 sm:p-4 border-b border-gold/30 flex items-center justify-between text-white bg-navy-surface flex-shrink-0">
        <div class="flex items-center gap-2.5">
          <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping flex-shrink-0"></span>
          <h3 class="font-display font-bold text-sm sm:text-base text-amber-200 truncate">Khám Phá Thực Tế Ảo VR 360° - Dragon Legend Hotel</h3>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
          <a href="<?php echo esc_url(dragonlegend_get_info('vr_url')); ?>" target="_blank" rel="noopener" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-xs text-gold border border-gold/40 font-semibold transition-colors">
            <span>Mở toàn màn hình</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
          </a>
          <button id="close-vr-btn" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white hover:text-gold flex items-center justify-center transition-colors" aria-label="Đóng VR">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>

      <!-- Iframe Container with Loader -->
      <div class="flex-1 bg-black relative w-full h-full min-h-0">
        <div id="vr-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-navy-deep text-gold z-10 transition-opacity">
          <svg class="w-10 h-10 animate-spin text-gold mb-3" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-xs uppercase tracking-widest font-semibold text-amber-200">Đang tải không gian VR 360°...</span>
        </div>
        <iframe id="vr-iframe" src="about:blank" class="w-full h-full border-0 absolute inset-0" allow="accelerometer; gyroscope; magnetometer; vr; fullscreen; xr-spatial-tracking" loading="lazy" title="VR 360 Tour"></iframe>
      </div>

      <!-- Mobile Hint Bar -->
      <div class="sm:hidden p-2.5 bg-navy-surface border-t border-gold/20 flex items-center justify-between text-xs flex-shrink-0">
        <span class="text-slate-300 text-[11px]">Chạm & xoay để xem toàn cảnh 360°</span>
        <a href="<?php echo esc_url(dragonlegend_get_info('vr_url')); ?>" target="_blank" rel="noopener" class="text-gold font-bold underline text-[11px]">
          Mở tab mới ↗
        </a>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 16. FLOATING CONCIERGE HUB                 -->
  <!-- ========================================== -->
  <aside class="fixed bottom-6 right-6 z-40 flex flex-col items-center gap-3" aria-label="Hỗ trợ trực tuyến">
    <!-- Messenger Chat -->
    <a href="<?php echo esc_url(dragonlegend_get_info('messenger')); ?>" target="_blank" rel="noopener" class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-xl hover:scale-110 transition-transform" aria-label="Chat Messenger">
      <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.614 4.469 8.654V24l4.088-2.242c1.092.302 2.25.464 3.443.464 6.627 0 12-4.975 12-11.111S18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26 6.559-6.962 3.13 3.26 5.889-3.26-6.56 6.962z"/></svg>
    </a>

    <!-- Hotline Ripple Button -->
    <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="w-14 h-14 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep flex items-center justify-center shadow-2xl hover:scale-105 transition-transform hotline-ripple" aria-label="Gọi điện thoại trực tiếp">
      <svg class="w-6 h-6 text-navy-deep animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
    </a>

    <!-- Back to top -->
    <button id="back-to-top" class="w-10 h-10 rounded-full bg-navy-deep text-gold border border-gold/40 flex items-center justify-center shadow-md opacity-0 invisible transition-all hover:bg-gold hover:text-navy-deep" aria-label="Lên đầu trang">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
    </button>
  </aside>

  <?php wp_footer(); ?>
</body>
</html>
