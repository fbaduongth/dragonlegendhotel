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

<main id="main" class="pt-28 pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    
    <!-- Breadcrumb -->
    <div class="dl-breadcrumb-wrapper mb-6">
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
    <div class="mb-10 text-center max-w-3xl mx-auto">
      <span class="text-xs font-bold uppercase tracking-widest text-amber-800 bg-amber-100/70 px-4 py-1.5 rounded-full inline-block mb-3 border border-amber-200">
        ✦ Kết Nối Cùng Dragon Legend ✦
      </span>
      <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy-deep tracking-tight">
        <?php the_title(); ?>
      </h1>
      <p class="text-slate-600 mt-3 text-base sm:text-lg">
        Quý khách vui lòng gửi thông tin hoặc liên hệ trực tiếp với chúng tôi để được tư vấn và phục vụ chu đáo nhất 24/7.
      </p>
    </div>

    <!-- 2 Columns Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
      
      <!-- Cột 1: Thông tin liên hệ & Google Map gọi từ Rank Math -->
      <div class="lg:col-span-6 space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80">
          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-700 flex items-center justify-center font-bold text-lg">
              📍
            </div>
            <div>
              <h2 class="text-xl font-bold text-navy-deep">Thông Tin & Vị Trí Khách Sạn</h2>
              <p class="text-xs text-slate-500">Dữ liệu định vị Local SEO & Google Maps</p>
            </div>
          </div>

          <!-- Rank Math Contact Info Shortcode -->
          <div class="dl-rank-math-contact-box">
            <?php echo do_shortcode('[rank_math_contact_info]'); ?>
          </div>

          <!-- Quick Action Buttons -->
          <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-50 hover:bg-gold/15 text-navy-deep font-bold text-xs sm:text-sm border border-slate-200 hover:border-gold transition-all">
              <span>📞</span> Gọi: <?php echo esc_html(dragonlegend_get_info('hotline')); ?>
            </a>
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr(dragonlegend_get_info('latitude')); ?>,<?php echo esc_attr(dragonlegend_get_info('longitude')); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-50 hover:bg-gold/15 text-navy-deep font-bold text-xs sm:text-sm border border-slate-200 hover:border-gold transition-all">
              <span>🗺️</span> Chỉ đường Google Maps
            </a>
          </div>
        </div>
      </div>

      <!-- Cột 2: Form liên hệ -->
      <div class="lg:col-span-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80">
          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-gold/20 text-amber-700 flex items-center justify-center font-bold text-lg">
              ✉️
            </div>
            <div>
              <h2 class="text-xl font-bold text-navy-deep">Gửi Tin Nhắn Cho Chúng Tôi</h2>
              <p class="text-xs text-slate-500">Phản hồi nhanh chóng trong vòng 15-30 phút</p>
            </div>
          </div>

          <form id="dl-contact-form" class="space-y-4">
            <input type="hidden" name="action" value="dl_submit_contact_form">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('dl_contact_nonce'); ?>">

            <!-- Họ và tên -->
            <div>
              <label for="contact-name" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                Họ và tên <span class="text-rose-500">*</span>
              </label>
              <input type="text" id="contact-name" name="name" required placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/50 focus:bg-white">
            </div>

            <!-- Số điện thoại & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="contact-phone" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                  Số điện thoại <span class="text-rose-500">*</span>
                </label>
                <input type="tel" id="contact-phone" name="phone" required placeholder="0912 345 678" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/50 focus:bg-white">
              </div>
              <div>
                <label for="contact-email" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                  Địa chỉ Email
                </label>
                <input type="email" id="contact-email" name="email" placeholder="example@gmail.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/50 focus:bg-white">
              </div>
            </div>

            <!-- Nhu cầu liên hệ -->
            <div>
              <label for="contact-subject" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                Nhu cầu liên hệ <span class="text-rose-500">*</span>
              </label>
              <select id="contact-subject" name="subject" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/50 focus:bg-white">
                <option value="Tư vấn đặt phòng nghỉ dưỡng">Tư vấn đặt phòng nghỉ dưỡng</option>
                <option value="Báo giá hội thảo, gala & sự kiện">Báo giá hội thảo, gala & sự kiện</option>
                <option value="Đặt bàn & Dịch vụ ẩm thực Dragon Pearl">Đặt bàn & Dịch vụ ẩm thực Dragon Pearl</option>
                <option value="Thuê xe đưa đón & Tour vịnh Hạ Long">Thuê xe đưa đón & Tour vịnh Hạ Long</option>
                <option value="Hợp tác kinh doanh & Đại lý du lịch">Hợp tác kinh doanh & Đại lý du lịch</option>
                <option value="Ý kiến đóng góp & Phản hồi khác">Ý kiến đóng góp & Phản hồi khác</option>
              </select>
            </div>

            <!-- Nội dung tin nhắn -->
            <div>
              <label for="contact-message" class="block text-xs font-bold text-navy-deep uppercase tracking-wider mb-1.5">
                Nội dung yêu cầu <span class="text-rose-500">*</span>
              </label>
              <textarea id="contact-message" name="message" rows="4" required placeholder="Quý khách vui lòng nhập thông tin chi tiết về số lượng người, ngày dự kiến hoặc yêu cầu đặc biệt..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-sm text-slate-800 transition-all bg-slate-50/50 focus:bg-white resize-y"></textarea>
            </div>

            <!-- Thông báo trạng thái -->
            <div id="contact-alert" class="hidden p-4 rounded-xl text-sm font-medium"></div>

            <!-- Nút gửi -->
            <button type="submit" id="contact-submit-btn" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-gold to-gold-500 hover:from-gold-400 hover:to-gold-600 text-navy-deep font-bold text-sm uppercase tracking-wider shadow-lg hover:shadow-gold/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2 cursor-pointer">
              <span id="contact-btn-text">Gửi Yêu Cầu Liên Hệ Ngay</span>
              <span id="contact-btn-icon">✈</span>
            </button>
          </form>
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
    btnIcon.textContent = '⏳';
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
      btnIcon.textContent = '✈';

      if (data.success) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 block';
        alertBox.innerHTML = '<div class=\"flex items-start gap-2\"><span>✅</span><span>' + (data.data.message || 'Gửi liên hệ thành công!') + '</span></div>';
        form.reset();
      } else {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
        alertBox.innerHTML = '<div class=\"flex items-start gap-2\"><span>⚠️</span><span>' + (data.data.message || 'Có lỗi xảy ra, vui lòng thử lại.') + '</span></div>';
      }
    })
    .catch(err => {
      submitBtn.disabled = false;
      btnText.textContent = 'Gửi Yêu Cầu Liên Hệ Ngay';
      btnIcon.textContent = '✈';
      alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
      alertBox.innerHTML = '<div class=\"flex items-start gap-2\"><span>⚠️</span><span>Lỗi kết nối mạng, vui lòng gọi điện thoại trực tiếp để được hỗ trợ nhanh nhất.</span></div>';
    });
  });
});
</script>

<?php
get_footer();
