<?php
/**
 * Single Job Template (single-dl_job.php)
 * Giao diện chi tiết từng vị trí Tuyển dụng & Form ứng tuyển trực tuyến
 * Độ rộng chuẩn: max-w-7xl mx-auto px-4
 *
 * @package DragonLegend_Recruitment
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$hr_settings = DragonLegend_Recruitment::get_hr_settings();

while (have_posts()) : the_post();
    $job_id       = get_the_ID();
    $status       = get_post_meta($job_id, '_dl_job_status', true) ?: 'open';
    $salary       = get_post_meta($job_id, '_dl_job_salary', true) ?: 'Thỏa thuận theo năng lực';
    $quantity     = get_post_meta($job_id, '_dl_job_quantity', true) ?: '01 người';
    $job_type     = get_post_meta($job_id, '_dl_job_type', true) ?: 'Toàn thời gian (Full-time)';
    $experience   = get_post_meta($job_id, '_dl_job_experience', true) ?: 'Trao đổi khi phỏng vấn';
    $deadline     = get_post_meta($job_id, '_dl_job_deadline', true) ?: 'Tuyển liên tục';
    $location     = get_post_meta($job_id, '_dl_job_location', true) ?: 'Dragon Legend Hotel - Bãi Cháy, Hạ Long';
    $requirements = get_post_meta($job_id, '_dl_job_requirements', true);
    $benefits     = get_post_meta($job_id, '_dl_job_benefits', true);

    $req_arr      = !empty($requirements) ? array_filter(array_map('trim', explode("\n", $requirements))) : array();
    $ben_arr      = !empty($benefits) ? array_filter(array_map('trim', explode("\n", $benefits))) : array();

    $terms        = get_the_terms($job_id, 'dl_job_department');
    $dept_term    = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;
    $dept_name    = $dept_term ? $dept_term->name : 'Khối Khách Sạn';
    $dept_link    = $dept_term ? get_term_link($dept_term, 'dl_job_department') : '';
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
          <a href="<?php echo esc_url(get_post_type_archive_link('dl_job')); ?>" class="hover:text-gold">Tuyển dụng</a>
          <span>/</span>
          <span class="text-navy-deep font-semibold"><?php the_title(); ?></span>
        </nav>
      <?php endif; ?>
    </div>

    <!-- Job Header Card -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-200/80 mb-8">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-3">
          <div class="flex flex-wrap items-center gap-2">
            <?php if ($dept_link && !is_wp_error($dept_link)) : ?>
              <a href="<?php echo esc_url($dept_link); ?>" class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors no-underline">
                <?php echo esc_html($dept_name); ?>
              </a>
            <?php else : ?>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                <?php echo esc_html($dept_name); ?>
              </span>
            <?php endif; ?>
            <?php if ($status === 'urgent') : ?>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Tuyển gấp</span>
              </span>
            <?php elseif ($status === 'closed') : ?>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                <span>Đã tuyển đủ</span>
              </span>
            <?php else : ?>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Đang nhận hồ sơ</span>
              </span>
            <?php endif; ?>
          </div>

          <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-navy-deep">
            <?php the_title(); ?>
          </h1>

          <p class="text-xs sm:text-sm text-slate-500 flex flex-wrap items-center gap-x-5 gap-y-1">
            <span class="inline-flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <span><?php echo esc_html($location); ?></span>
            </span>
            <span class="inline-flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>Ngày đăng: <?php echo get_the_date('d/m/Y'); ?></span>
            </span>
          </p>
        </div>

        <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100 flex-shrink-0">
          <div class="text-left lg:text-right">
            <span class="text-xs text-slate-400 block">Mức thu nhập dự kiến</span>
            <span class="text-xl sm:text-2xl font-extrabold text-amber-700"><?php echo esc_html($salary); ?></span>
          </div>
          <a href="#apply-form-box" class="px-6 py-3 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow-md hover:brightness-110 transition-all">
            Ứng Tuyển Vị Trí Này &darr;
          </a>
        </div>
      </div>

      <!-- 4 Key Specs Row -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
          <span class="text-[11px] text-slate-400 uppercase block">Số lượng cần tuyển</span>
          <span class="text-sm sm:text-base font-bold text-navy-deep"><?php echo esc_html($quantity); ?></span>
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
          <span class="text-[11px] text-slate-400 uppercase block">Hình thức làm việc</span>
          <span class="text-sm sm:text-base font-bold text-navy-deep"><?php echo esc_html($job_type); ?></span>
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
          <span class="text-[11px] text-slate-400 uppercase block">Kinh nghiệm yêu cầu</span>
          <span class="text-sm sm:text-base font-bold text-navy-deep"><?php echo esc_html($experience); ?></span>
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
          <span class="text-[11px] text-slate-400 uppercase block">Hạn nộp hồ sơ</span>
          <span class="text-sm sm:text-base font-bold text-amber-700"><?php echo esc_html($deadline); ?></span>
        </div>
      </div>
    </div>

    <!-- Main 2 Columns Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

      <!-- Left Column: Job Description, Requirements, Benefits -->
      <div class="lg:col-span-8 space-y-8">

        <!-- Mô tả công việc -->
        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-sm border border-slate-200/80">
          <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep mb-4 pb-3 border-b border-slate-100">
            1. Mô Tả Công Việc
          </h2>
          <div class="entry-content prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base">
            <?php the_content(); ?>
          </div>
        </div>

        <!-- Yêu cầu ứng viên -->
        <?php if (!empty($req_arr)) : ?>
          <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-sm border border-slate-200/80">
            <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep mb-4 pb-3 border-b border-slate-100">
              2. Yêu Cầu Ứng Viên
            </h2>
            <ul class="space-y-3">
              <?php foreach ($req_arr as $req) : ?>
                <li class="flex items-start gap-3 text-sm sm:text-base text-slate-700">
                  <span class="text-gold font-bold mt-0.5">✦</span>
                  <span><?php echo esc_html($req); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Quyền lợi & Đãi ngộ -->
        <?php if (!empty($ben_arr)) : ?>
          <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-sm border border-slate-200/80">
            <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep mb-4 pb-3 border-b border-slate-100">
              3. Quyền Lợi & Chế Độ Đãi Ngộ
            </h2>
            <div class="grid grid-cols-1 gap-3">
              <?php foreach ($ben_arr as $ben) : ?>
                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-amber-50/50 border border-amber-200/50 text-sm sm:text-base text-slate-800">
                  <span class="text-emerald-600 font-bold">✓</span>
                  <span><?php echo esc_html($ben); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- Right Column: Online Application Form & HR Contact -->
      <div id="apply-form-box" class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border-2 border-gold/40">
          <div class="mb-5 pb-4 border-b border-slate-100">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-widest block">Nộp Hồ Sơ Nhanh</span>
            <h3 class="font-display text-xl font-bold text-navy-deep">Ứng Tuyển Trực Tuyến</h3>
            <p class="text-xs text-slate-500 mt-1">Điền thông tin bên dưới, Phòng Nhân sự sẽ liên hệ hẹn lịch phỏng vấn.</p>
          </div>

          <form id="dl-job-apply-form" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="action" value="dl_submit_job_application">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('dl_job_apply_nonce'); ?>">
            <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
            <input type="hidden" name="job_title" value="<?php the_title_attribute(); ?>">

            <div>
              <label class="block text-xs font-bold text-navy-deep uppercase mb-1">Vị trí ứng tuyển</label>
              <input type="text" value="<?php the_title_attribute(); ?>" readonly class="w-full px-3.5 py-2.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs font-bold text-navy-deep">
            </div>

            <div>
              <label for="app-name" class="block text-xs font-bold text-navy-deep uppercase mb-1">Họ và tên <span class="text-rose-500">*</span></label>
              <input type="text" id="app-name" name="name" required placeholder="Nhập họ và tên của bạn" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-gold outline-none text-sm">
            </div>

            <div>
              <label for="app-phone" class="block text-xs font-bold text-navy-deep uppercase mb-1">Số điện thoại <span class="text-rose-500">*</span></label>
              <input type="tel" id="app-phone" name="phone" required placeholder="0912 345 678" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-gold outline-none text-sm">
            </div>

            <div>
              <label for="app-email" class="block text-xs font-bold text-navy-deep uppercase mb-1">Địa chỉ Email</label>
              <input type="email" id="app-email" name="email" placeholder="example@gmail.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-gold outline-none text-sm">
            </div>

            <div>
              <label for="app-cv" class="block text-xs font-bold text-navy-deep uppercase mb-1">Đính kèm CV (PDF, Word, Ảnh)</label>
              <input type="file" id="app-cv" name="cv_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200">
            </div>

            <div>
              <label for="app-msg" class="block text-xs font-bold text-navy-deep uppercase mb-1">Kinh nghiệm / Giới thiệu ngắn</label>
              <textarea id="app-msg" name="message" rows="3" placeholder="Tóm tắt kinh nghiệm làm việc hoặc mong muốn của bạn..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-gold outline-none text-sm"></textarea>
            </div>

            <div id="job-apply-alert" class="hidden p-3.5 rounded-xl text-xs font-medium"></div>

            <button type="submit" id="job-apply-btn" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow-lg hover:brightness-110 transition-all cursor-pointer">
              Gửi Hồ Sơ Ứng Tuyển Ngay
            </button>
          </form>
        </div>

        <!-- HR Direct Box -->
        <div class="bg-navy-deep text-white p-6 rounded-3xl shadow-lg space-y-2.5 text-xs">
          <span class="text-gold font-bold uppercase tracking-wider block">Liên hệ trực tiếp Phòng Nhân sự</span>
          <p class="text-slate-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span>Hotline: <strong class="text-white"><?php echo esc_html($hr_settings['hr_phone']); ?></strong></span>
          </p>
          <p class="text-slate-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span>Email: <strong class="text-white"><?php echo esc_html($hr_settings['hr_email']); ?></strong></span>
          </p>
          <p class="text-slate-300 flex items-start gap-2">
            <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span><?php echo esc_html($hr_settings['hr_address']); ?></span>
          </p>
        </div>
      </div>

    </div>

  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('dl-job-apply-form');
  if (!form) return;
  const btn = document.getElementById('job-apply-btn');
  const alertBox = document.getElementById('job-apply-alert');

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    btn.disabled = true;
    btn.textContent = 'Đang gửi hồ sơ...';
    alertBox.className = 'hidden';

    const formData = new FormData(form);
    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      btn.textContent = 'Gửi Hồ Sơ Ứng Tuyển Ngay';
      if (res.success) {
        alertBox.className = 'p-3.5 rounded-xl text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 block';
        alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + res.data.message + '</span></div>';
        form.reset();
      } else {
        alertBox.className = 'p-3.5 rounded-xl text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
        alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + (res.data.message || 'Có lỗi xảy ra, vui lòng thử lại.') + '</span></div>';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Gửi Hồ Sơ Ứng Tuyển Ngay';
      alertBox.className = 'p-3.5 rounded-xl text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200 block';
      alertBox.innerHTML = '<div class="flex items-start gap-2"><svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Lỗi kết nối, vui lòng liên hệ trực tiếp qua số điện thoại nhân sự.</span></div>';
    });
  });
});
</script>

<?php
endwhile;
get_footer();
