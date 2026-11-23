<?php
/**
 * Archive Job Template (archive-dl_job.php)
 * Giao diện danh sách tin Tuyển dụng Dragon Legend Hotel
 * Độ rộng chuẩn: max-w-7xl mx-auto px-4
 *
 * @package DragonLegend_Recruitment
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$hr_settings = DragonLegend_Recruitment::get_hr_settings();
$departments = get_terms(array(
    'taxonomy'   => 'dl_job_department',
    'hide_empty' => false,
));
$current_dept = '';
if (is_tax('dl_job_department')) {
    $queried_obj = get_queried_object();
    if ($queried_obj && isset($queried_obj->slug)) {
        $current_dept = $queried_obj->slug;
    }
}
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
          <span class="text-navy-deep font-semibold">Tuyển dụng</span>
        </nav>
      <?php endif; ?>
    </div>

    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-11">
      <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
        <span>Cơ Hội Nghề Nghiệp</span>
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
      </div>
      <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28] mb-2.5">
        Tuyển Dụng Nhân Sự Dragon Legend
      </h1>
      <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
        Gia nhập đội ngũ nhân sự chuyên nghiệp tại khách sạn 4 sao Dragon Legend Hạ Long. Chúng tôi luôn chào đón những ứng viên nhiệt huyết, tận tâm cùng xây dựng dịch vụ nghỉ dưỡng đẳng cấp.
      </p>
    </div>

    <!-- 4 Values Highlight Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2" stroke-width="1.8"/><circle cx="12" cy="12" r="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 12h.01M18 12h.01"/></svg>
        </div>
        <div>
          <h3 class="text-xs sm:text-sm font-bold text-navy-deep">Thu Nhập Hấp Dẫn</h3>
          <p class="text-[11px] text-slate-500">Lương cứng + Thưởng phí dịch vụ</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 12 2 2 4-4"/></svg>
        </div>
        <div>
          <h3 class="text-xs sm:text-sm font-bold text-navy-deep">Đãi Ngộ Toàn Diện</h3>
          <p class="text-[11px] text-slate-500">BHXH đầy đủ, hỗ trợ ăn ca, đồng phục</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div>
          <h3 class="text-xs sm:text-sm font-bold text-navy-deep">Môi Trường 4 Sao</h3>
          <p class="text-[11px] text-slate-500">Văn hóa làm việc văn minh, tôn trọng</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
          <h3 class="text-xs sm:text-sm font-bold text-navy-deep">Lộ Trình Thăng Tiến</h3>
          <p class="text-[11px] text-slate-500">Đào tạo nghiệp vụ khách sạn miễn phí</p>
        </div>
      </div>
    </div>

    <!-- Filter by Department -->
    <?php if (!empty($departments) && !is_wp_error($departments)) : ?>
      <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-2.5 mb-10">
        <a href="<?php echo esc_url(get_post_type_archive_link('dl_job')); ?>"
           class="px-4 py-2 rounded-full text-xs font-bold transition-all <?php echo empty($current_dept) ? 'bg-navy-deep text-gold shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:border-gold'; ?>">
          Tất cả vị trí
        </a>
        <?php foreach ($departments as $dept) :
          $dept_url = get_term_link($dept, 'dl_job_department');
          if (is_wp_error($dept_url)) {
              continue;
          }
        ?>
          <a href="<?php echo esc_url($dept_url); ?>"
             class="px-4 py-2 rounded-full text-xs font-bold transition-all <?php echo ($current_dept === $dept->slug) ? 'bg-navy-deep text-gold shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:border-gold'; ?>">
            <?php echo esc_html($dept->name); ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Job Listings Grid -->
    <?php
    $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    $query_args = array(
        'post_type'      => 'dl_job',
        'posts_per_page' => 6,
        'paged'          => $paged,
        'post_status'    => 'publish',
    );
    if (!empty($current_dept)) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'dl_job_department',
                'field'    => 'slug',
                'terms'    => $current_dept,
            )
        );
    }
    $jobs_query = new WP_Query($query_args);
    ?>

    <?php if ($jobs_query->have_posts()) : ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <?php
        while ($jobs_query->have_posts()) : $jobs_query->the_post();
            $job_id     = get_the_ID();
            $status     = get_post_meta($job_id, '_dl_job_status', true) ?: 'open';
            $salary     = get_post_meta($job_id, '_dl_job_salary', true) ?: 'Thỏa thuận theo năng lực';
            $quantity   = get_post_meta($job_id, '_dl_job_quantity', true) ?: '01 người';
            $job_type   = get_post_meta($job_id, '_dl_job_type', true) ?: 'Toàn thời gian';
            $experience = get_post_meta($job_id, '_dl_job_experience', true) ?: 'Trao đổi khi phỏng vấn';
            $deadline   = get_post_meta($job_id, '_dl_job_deadline', true) ?: 'Tuyển liên tục';
            $terms      = get_the_terms($job_id, 'dl_job_department');
            $dept_term  = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;
            $dept_name  = $dept_term ? $dept_term->name : 'Khối Khách Sạn';
            $dept_link  = $dept_term ? get_term_link($dept_term, 'dl_job_department') : '';
        ?>
          <article class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg border border-slate-200/80 hover:shadow-2xl hover:border-gold transition-all duration-300 flex flex-col justify-between">
            <div>
              <!-- Top Badges -->
              <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <?php if ($dept_link && !is_wp_error($dept_link)) : ?>
                  <a href="<?php echo esc_url($dept_link); ?>" class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60 hover:bg-amber-100 transition-colors no-underline">
                    <?php echo esc_html($dept_name); ?>
                  </a>
                <?php else : ?>
                  <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                    <?php echo esc_html($dept_name); ?>
                  </span>
                <?php endif; ?>
                <?php if ($status === 'urgent') : ?>
                  <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Tuyển gấp</span>
                  </span>
                <?php elseif ($status === 'closed') : ?>
                  <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                    Đã tuyển đủ
                  </span>
                <?php else : ?>
                  <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang tuyển
                  </span>
                <?php endif; ?>
              </div>

              <!-- Job Title -->
              <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep mb-2 hover:text-gold transition-colors">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>

              <!-- Excerpt -->
              <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 mb-5 leading-relaxed">
                <?php echo esc_html(get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 28)); ?>
              </p>

              <!-- Key Specs Box -->
              <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs mb-6">
                <div>
                  <span class="text-slate-400 block text-[11px]">Mức thu nhập:</span>
                  <span class="font-bold text-amber-700"><?php echo esc_html($salary); ?></span>
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]">Số lượng cần tuyển:</span>
                  <span class="font-bold text-navy-deep"><?php echo esc_html($quantity); ?></span>
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]">Hình thức làm việc:</span>
                  <span class="font-medium text-slate-700"><?php echo esc_html($job_type); ?></span>
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]">Hạn nộp hồ sơ:</span>
                  <span class="font-medium text-slate-700"><?php echo esc_html($deadline); ?></span>
                </div>
              </div>
            </div>

            <!-- Action Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <span class="text-xs text-slate-500 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Bãi Cháy, TP. Hạ Long</span>
              </span>
              <a href="<?php the_permalink(); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow hover:brightness-110 transition-all">
                <span>Xem Chi Tiết & Ứng Tuyển</span>
                <span>&rarr;</span>
              </a>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <?php
      if (function_exists('dragonlegend_render_pagination')) {
          echo '<div class="mb-14">';
          dragonlegend_render_pagination($jobs_query);
          echo '</div>';
      }
      wp_reset_postdata();
      ?>
    <?php else : ?>
      <div class="bg-white p-12 rounded-3xl text-center border border-slate-200 mb-14">
        <p class="text-lg font-bold text-navy-deep mb-2">Hiện chưa có vị trí tuyển dụng nào trong mục này</p>
        <p class="text-sm text-slate-500 mb-4">Quý ứng viên vui lòng xem tất cả các bộ phận khác hoặc gửi CV dự tuyển trực tiếp cho Phòng Nhân sự.</p>
        <a href="<?php echo esc_url(get_post_type_archive_link('dl_job')); ?>" class="inline-block px-6 py-2.5 rounded-full bg-gold text-navy-deep font-bold text-xs uppercase">Xem tất cả vị trí</a>
      </div>
    <?php endif; ?>

    <!-- HR Contact Info Banner -->
    <div class="bg-navy-deep text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-gold/30">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-8 space-y-2">
          <span class="text-gold text-xs font-bold uppercase tracking-widest block">Thông Tin Liên Hệ Ứng Tuyển</span>
          <h3 class="font-display text-2xl sm:text-3xl font-bold text-white"><?php echo esc_html($hr_settings['hr_name']); ?></h3>
          <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
            <?php echo esc_html($hr_settings['hr_note']); ?>
          </p>
          <div class="pt-3 flex flex-wrap gap-y-2 gap-x-6 text-xs sm:text-sm text-amber-200">
            <span class="inline-flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <span><strong>Địa chỉ:</strong> <?php echo esc_html($hr_settings['hr_address']); ?></span>
            </span>
            <span class="inline-flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              <span><strong>Điện thoại:</strong> <?php echo esc_html($hr_settings['hr_phone']); ?></span>
            </span>
            <span class="inline-flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span><strong>Email:</strong> <?php echo esc_html($hr_settings['hr_email']); ?></span>
            </span>
          </div>
        </div>
        <div class="lg:col-span-4 flex lg:justify-end">
          <a href="mailto:<?php echo esc_attr($hr_settings['hr_email']); ?>?subject=Ứng tuyển Nhân sự Dragon Legend Hotel"
             class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs sm:text-sm uppercase tracking-wider shadow-lg hover:scale-105 transition-all">
            <svg class="w-4 h-4 text-navy-deep flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span>Gửi CV Qua Email Ngay</span>
          </a>
        </div>
      </div>
    </div>

  </div>
</main>

<?php
get_footer();
