<?php
/**
 * Template Name: Trang Liên Hệ
 *
 * Giao diện 2 cột: Cột 1 thông tin Local SEO & Google Map từ Rank Math [rank_math_contact_info]
 *                  Cột 2 Form liên hệ trực tuyến cao cấp
 *
 * @package DragonLegend
 * @version 1.0.0
 */

get_header();
?>

<main id="main" class="pt-6 sm:pt-10 lg:pt-12 pb-12 sm:pb-16 lg:pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    
    <!-- Breadcrumb -->
    <div class="dl-breadcrumb-wrapper mb-4 sm:mb-6">
      <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
        <?php rank_math_the_breadcrumbs(); ?>
      <?php else : ?>
        <nav class="flex items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-gold">Trang chủ</a>
          <span>/</span>
          <span class="text-navy-deep font-semibold"><?php the_title(); ?></span>
        </nav>
      <?php endif; ?>
    </div>

    <!-- Page Header Banner -->
    <header class="mb-8 sm:mb-11 text-center max-w-2xl mx-auto">
      <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
        <span>Kết Nối Cùng Dragon Legend</span>
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
      </div>
      <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28] mb-2.5">
        <?php the_title(); ?>
      </h1>
      <p class="text-slate-600 text-[13.5px] sm:text-[15px] leading-relaxed">
        Quý khách vui lòng gửi thông tin hoặc liên hệ trực tiếp với chúng tôi để được tư vấn và phục vụ chu đáo nhất 24/7.
      </p>
    </header>

    <!-- 2 Columns Layout (Equal Height & Balanced) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
      
      <!-- Cột 1: Thông tin liên hệ & Google Map gọi từ Rank Math -->
      <div class="lg:col-span-6 flex flex-col">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-200/80 h-full flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-slate-100">
              <div class="w-11 h-11 rounded-2xl bg-[#fdfaf1] border border-amber-200/80 text-[#9b671a] flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              </div>
              <div>
                <h2 class="font-display text-xl font-bold text-navy-deep leading-tight">Thông Tin Khách Sạn</h2>
                <p class="text-xs text-slate-500 mt-0.5">Vị trí khách sạn &amp; chỉ đường trên Google Maps</p>
              </div>
            </div>

            <!-- Rank Math Contact Info Shortcode (Styled as Bento Grid) -->
            <div class="dl-rank-math-contact-box">
              <?php
              $rm_contact_html = do_shortcode('[rank_math_contact_info]');
              if (strpos($rm_contact_html, '<iframe') === false && function_exists('dl_enhance_rank_math_contact_info')) {
                  $rm_contact_html = dl_enhance_rank_math_contact_info($rm_contact_html);
              }
              echo $rm_contact_html;
              ?>
            </div>
          </div>

          <!-- Quick Action Buttons -->
          <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#fdfaf1] hover:bg-gold text-navy-deep font-bold text-xs sm:text-sm border border-amber-200/80 hover:border-gold transition-all shadow-sm">
              <svg class="w-4 h-4 text-[#9b671a] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
              <span>Gọi: <?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
            </a>
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr(dragonlegend_get_info('latitude')); ?>,<?php echo esc_attr(dragonlegend_get_info('longitude')); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-50 hover:bg-gold/15 text-navy-deep font-bold text-xs sm:text-sm border border-slate-200 hover:border-gold transition-all shadow-sm">
              <svg class="w-4 h-4 text-[#9b671a] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
              <span>Chỉ đường Google Maps</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Cột 2: Form liên hệ -->
      <div class="lg:col-span-6 flex flex-col">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-200/80 h-full flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-slate-100">
              <div class="w-11 h-11 rounded-2xl bg-[#fdfaf1] border border-amber-200/80 text-[#9b671a] flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <div>
                <h2 class="font-display text-xl font-bold text-navy-deep leading-tight">Gửi Tin Nhắn Cho Chúng Tôi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Bộ phận Lễ tân &amp; CSKH phản hồi nhanh chóng trong 15 – 30 phút</p>
              </div>
            </div>

            <form id="dl-contact-form" class="space-y-4">
              <input type="hidden" name="action" value="dl_submit_contact_form">
              <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('dl_contact_nonce'); ?>">

              <!-- Họ và tên -->
              <div>
                <label for="contact-name" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                  Họ và tên Quý khách <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="contact-name" name="name" required placeholder="Nhập họ và tên của bạn..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/60 focus:bg-white">
              </div>

              <!-- Số điện thoại & Email -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="contact-phone" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                    Số điện thoại <span class="text-rose-500">*</span>
                  </label>
                  <input type="tel" id="contact-phone" name="phone" required placeholder="0912 345 678" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/60 focus:bg-white">
                </div>
                <div>
                  <label for="contact-email" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                    Địa chỉ Email
                  </label>
                  <input type="email" id="contact-email" name="email" placeholder="example@gmail.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/60 focus:bg-white">
                </div>
              </div>

              <!-- Nhu cầu liên hệ -->
              <div>
                <label for="contact-subject" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                  Chủ đề / Nhu cầu tư vấn <span class="text-rose-500">*</span>
                </label>
                <select id="contact-subject" name="subject" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/60 focus:bg-white">
                  <option value="Tư vấn đặt phòng nghỉ dưỡng">Tư vấn đặt phòng nghỉ dưỡng</option>
                  <option value="Báo giá hội thảo, gala & sự kiện">Báo giá hội thảo, gala &amp; sự kiện</option>
                  <option value="Đặt bàn & Dịch vụ ẩm thực">Đặt bàn &amp; Dịch vụ ẩm thực</option>
                  <option value="Thuê xe đưa đón & Tour vịnh Hạ Long">Thuê xe đưa đón &amp; Tour vịnh Hạ Long</option>
                  <option value="Hợp tác kinh doanh & Đại lý du lịch">Hợp tác kinh doanh &amp; Đại lý du lịch</option>
                  <option value="Ý kiến đóng góp & Phản hồi khác">Ý kiến đóng góp &amp; Phản hồi khác</option>
                </select>
              </div>

              <!-- Nội dung tin nhắn -->
              <div>
                <label for="contact-message" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                  Nội dung yêu cầu chi tiết <span class="text-rose-500">*</span>
                </label>
                <textarea id="contact-message" name="message" rows="5" required placeholder="Quý khách vui lòng nhập thông tin chi tiết về số lượng khách, ngày dự kiến lưu trú hoặc yêu cầu đặc biệt..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/60 focus:bg-white resize-y"></textarea>
              </div>

              <!-- Thông báo trạng thái -->
              <div id="contact-alert" class="hidden p-4 rounded-xl text-sm font-medium"></div>

              <!-- Nút gửi -->
              <button type="submit" id="contact-submit-btn" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-gold to-gold-500 hover:from-gold-400 hover:to-gold-600 text-navy-deep font-bold text-xs sm:text-sm uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer gold-shimmer-btn">
                <span id="contact-btn-text">Gửi Yêu Cầu Liên Hệ Ngay</span>
                <span id="contact-btn-icon">&rarr;</span>
              </button>
            </form>
          </div>

          <!-- Footer note inside Form Card to balance Column 1 -->
          <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
              <span>Hỗ trợ trực tuyến 24/7 (Cả Lễ, Tết)</span>
            </span>
            <span class="font-medium text-slate-600 inline-flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <span>Bảo mật thông tin tuyệt đối</span>
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Nội dung bổ sung nếu có soạn thảo trong Page -->
    <?php while (have_posts()) : the_post(); ?>
      <?php if (get_the_content()) : ?>
        <div class="mt-12 bg-white p-8 rounded-3xl shadow-sm border border-slate-200 entry-content prose max-w-none">
          <?php the_content(); ?>
        </div>
      <?php endif; ?>
    <?php endwhile; ?>

  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('dl-contact-form');
  if (!form) return;

  const alertBox = document.getElementById('contact-alert');
  const submitBtn = document.getElementById('contact-submit-btn');
  const btnText = document.getElementById('contact-btn-text');
  const btnIcon = document.getElementById('contact-btn-icon');

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Disable button & show spinner
    submitBtn.disabled = true;
    btnText.textContent = 'Đang gửi thông tin...';
    btnIcon.innerHTML = '<svg class="w-4 h-4 animate-spin inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    alertBox.className = 'hidden';

    const formData = new FormData(form);
    const ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      submitBtn.disabled = false;
      btnText.textContent = 'Gửi Yêu Cầu Liên Hệ Ngay';
      btnIcon.innerHTML = '&rarr;';

      if (data.success) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 block';
        alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>' + (data.data.message || 'Gửi liên hệ thành công!') + '</span></div>';
        form.reset();
      } else {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
        alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>' + (data.data.message || 'Có lỗi xảy ra, vui lòng thử lại.') + '</span></div>';
      }
    })
    .catch(err => {
      submitBtn.disabled = false;
      btnText.textContent = 'Gửi Yêu Cầu Liên Hệ Ngay';
      btnIcon.innerHTML = '&rarr;';
      alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
      alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Lỗi kết nối mạng, vui lòng gọi điện thoại trực tiếp để được hỗ trợ nhanh nhất.</span></div>';
    });
  });
});
</script>

<?php
get_footer();
