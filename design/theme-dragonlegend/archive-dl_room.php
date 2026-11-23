<?php
/**
 * Archive Room Template (archive-dl_room.php)
 * Danh sách toàn bộ các hạng phòng nghỉ Dragon Legend Hotel
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();
?>

<main id="main" class="pt-28 pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
      <div class="dl-breadcrumb-wrapper mb-6">
        <?php rank_math_the_breadcrumbs(); ?>
      </div>
    <?php endif; ?>
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-14">
      <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-2">
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
        <span>Danh Sách Phòng Nghỉ</span>
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
      </div>
      <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-navy-deep mb-4">
        Các Hạng Phòng & Suite Sang Trọng
      </h1>
      <p class="text-sm sm:text-base text-slate-600">
        Khám phá không gian nghỉ dưỡng chuẩn 4 sao quốc tế với 138 phòng tiện nghi, tầm nhìn khoáng đạt ôm trọn kỳ quan Vịnh Hạ Long.
      </p>
    </div>

    <!-- Grid of Rooms -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      if (have_posts()) :
          while (have_posts()) : the_post();
              $post_id    = get_the_ID();
              $price      = get_post_meta($post_id, '_dl_room_price', true);
              $orig_price = get_post_meta($post_id, '_dl_room_original_price', true);
              $size       = get_post_meta($post_id, '_dl_room_size', true) ?: '28 - 32 m²';
              $bed        = get_post_meta($post_id, '_dl_room_bed', true) ?: '1 Giường King hoặc 2 Giường đơn';
              $view       = get_post_meta($post_id, '_dl_room_view', true) ?: 'Hướng thành phố';
              $badge      = get_post_meta($post_id, '_dl_room_badge', true);
              $img_url    = get_the_post_thumbnail_url($post_id, 'large');
              if (!$img_url) {
                  $img_url = get_post_meta($post_id, '_dl_room_sample_image', true) ?: 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg';
              }
      ?>
        <article class="bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 flex flex-col justify-between hover:shadow-2xl hover:border-gold transition-all duration-300">
          <div>
            <div class="relative h-60 image-zoom-container">
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
              <?php if ($badge) : ?>
                <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider shadow">
                  <?php echo esc_html($badge); ?>
                </span>
              <?php endif; ?>
              <span class="absolute bottom-4 right-4 px-3 py-1 rounded-full text-[10px] font-semibold bg-navy-deep/80 backdrop-blur-md text-slate-200">
                <?php echo esc_html($view); ?>
              </span>
            </div>

            <div class="p-6">
              <h2 class="font-display font-bold text-xl text-navy-deep mb-2">
                <a href="<?php the_permalink(); ?>" class="hover:text-gold transition-colors"><?php the_title(); ?></a>
              </h2>
              <p class="text-xs text-slate-500 mb-4 line-clamp-2"><?php echo get_the_excerpt(); ?></p>

              <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                <div>Diện tích: <strong class="text-navy-deep"><?php echo esc_html($size); ?></strong></div>
                <div>Giường: <strong class="text-navy-deep"><?php echo esc_html($bed); ?></strong></div>
              </div>
            </div>
          </div>

          <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
            <div>
              <?php if ($orig_price) : ?>
                <span class="text-xs line-through text-slate-400 block"><?php echo dl_format_price($orig_price); ?></span>
              <?php endif; ?>
              <span class="text-lg font-bold text-amber-700"><?php echo dl_format_price($price ?: 1050000); ?></span>
              <span class="text-[10px] text-slate-400 block">/ đêm</span>
            </div>

            <div class="flex items-center gap-2">
              <a href="<?php the_permalink(); ?>" class="px-3.5 py-2 border border-slate-200 text-slate-700 hover:border-gold font-bold text-xs rounded-xl transition-colors">
                Chi tiết
              </a>
              <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal('<?php echo esc_js(get_post_field('post_name', $post_id)); ?>')" 
                      class="px-4 py-2 bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs rounded-xl shadow hover:brightness-110 transition-all">
                Đặt ngay
              </button>
            </div>
          </div>
        </article>
      <?php
          endwhile;
      endif;
      ?>
    </div>
  </div>
</main>

<?php
get_footer();
