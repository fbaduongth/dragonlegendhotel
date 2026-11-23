<?php
/**
 * Single Room Template (single-dl_room.php)
 * Giao diện chi tiết từng hạng phòng nghỉ Dragon Legend Hotel
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();

while (have_posts()) : the_post();
    $post_id     = get_the_ID();
    $subtitle    = get_post_meta($post_id, '_dl_room_subtitle', true);
    $price       = get_post_meta($post_id, '_dl_room_price', true);
    $orig_price  = get_post_meta($post_id, '_dl_room_original_price', true);
    $size        = get_post_meta($post_id, '_dl_room_size', true) ?: '28 - 32 m²';
    $bed         = get_post_meta($post_id, '_dl_room_bed', true) ?: '1 Giường đôi King';
    $occupancy   = get_post_meta($post_id, '_dl_room_occupancy', true) ?: '2 Người lớn + 1 Trẻ em';
    $view        = get_post_meta($post_id, '_dl_room_view', true) ?: 'Hướng thành phố';
    $badge       = get_post_meta($post_id, '_dl_room_badge', true);
    $rating      = get_post_meta($post_id, '_dl_room_rating', true) ?: '4.8';
    $reviews_cnt = intval(get_post_meta($post_id, '_dl_room_reviews_count', true)) ?: 142;
    $gallery     = get_post_meta($post_id, '_dl_room_gallery', true);
    if (!is_array($gallery) || empty($gallery)) {
        $feat = get_the_post_thumbnail_url($post_id, 'full');
        $gallery = $feat ? array($feat) : array('https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg');
    }

    $amenities_keys = get_post_meta($post_id, '_dl_room_amenities', true);
    if (!is_array($amenities_keys)) $amenities_keys = array();
    $std_amenities = function_exists('DragonLegend_Metaboxes::get_standard_amenities_list') 
        ? DragonLegend_Metaboxes::get_standard_amenities_list() 
        : array();
?>

<main id="main" class="pt-28 pb-20 bg-cream" role="main">
  <!-- Breadcrumb & Title Header -->
  <div class="max-w-7xl mx-auto px-4 mb-8">
    <div class="dl-breadcrumb-wrapper mb-4">
      <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
        <?php rank_math_the_breadcrumbs(); ?>
      <?php else : ?>
        <nav class="flex items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-gold">Trang chủ</a>
          <span>/</span>
          <a href="<?php echo esc_url(get_post_type_archive_link('dl_room')); ?>" class="hover:text-gold">Hạng phòng</a>
          <span>/</span>
          <span class="text-navy-deep font-semibold"><?php the_title(); ?></span>
        </nav>
      <?php endif; ?>
    </div>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <?php if ($badge) : ?>
          <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider inline-block mb-2">
            <?php echo esc_html($badge); ?>
          </span>
        <?php endif; ?>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-navy-deep">
          <?php the_title(); ?>
        </h1>
        <?php if ($subtitle) : ?>
          <p class="text-sm sm:text-base text-slate-600 mt-2"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
      </div>

      <div class="text-left md:text-right flex-shrink-0">
        <?php if ($orig_price) : ?>
          <span class="text-sm line-through text-slate-400 block"><?php echo dl_format_price($orig_price); ?></span>
        <?php endif; ?>
        <span class="text-2xl sm:text-3xl font-extrabold text-amber-700"><?php echo dl_format_price($price ?: 1050000); ?></span>
        <span class="text-xs text-slate-500 block">/ đêm (Chưa gồm ưu đãi)</span>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-10">
    
    <!-- Left Column: Gallery & Details -->
    <div class="lg:col-span-8 space-y-8">
      <!-- Main Gallery Carousel -->
      <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-200/80">
        <div class="relative h-[320px] sm:h-[480px]">
          <img id="single-room-main-img" src="<?php echo esc_url($gallery[0]); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
        </div>
        <?php if (count($gallery) > 1) : ?>
          <div class="p-4 flex gap-3 overflow-x-auto bg-slate-50 border-t border-slate-100">
            <?php foreach ($gallery as $idx => $g_img) : ?>
              <button type="button" onclick="document.getElementById('single-room-main-img').src='<?php echo esc_url($g_img); ?>'" class="w-20 h-16 rounded-xl overflow-hidden border-2 hover:border-gold focus:border-gold flex-shrink-0 transition-all">
                <img src="<?php echo esc_url($g_img); ?>" alt="Gallery <?php echo $idx; ?>" class="w-full h-full object-cover">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Specs Quick Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6 bg-white rounded-3xl shadow-sm border border-slate-200/80 text-center">
        <div>
          <span class="text-xs text-slate-400 block uppercase tracking-wider">Diện tích</span>
          <span class="font-bold text-navy-deep text-base sm:text-lg"><?php echo esc_html($size); ?></span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block uppercase tracking-wider">Loại giường</span>
          <span class="font-bold text-navy-deep text-base sm:text-lg"><?php echo esc_html($bed); ?></span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block uppercase tracking-wider">Sức chứa</span>
          <span class="font-bold text-navy-deep text-base sm:text-lg"><?php echo esc_html($occupancy); ?></span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block uppercase tracking-wider">Hướng nhìn</span>
          <span class="font-bold text-navy-deep text-base sm:text-lg"><?php echo esc_html($view); ?></span>
        </div>
      </div>

      <!-- Description Content -->
      <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-display text-2xl font-bold text-navy-deep">Không Gian & Thiết Kế</h2>
        <div class="entry-content prose max-w-none text-slate-600 leading-relaxed text-sm sm:text-base">
          <?php the_content(); ?>
        </div>
      </div>

      <!-- Amenities Section -->
      <?php if (!empty($amenities_keys)) : ?>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80">
          <h2 class="font-display text-2xl font-bold text-navy-deep mb-6">Tiện Nghi Phòng Cao Cấp</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($amenities_keys as $ak) : 
                $label = isset($std_amenities[$ak]) ? $std_amenities[$ak] : $ak;
            ?>
              <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-gold border border-gold/30 flex items-center justify-center flex-shrink-0">
                  <?php echo dl_get_amenity_svg($ak, "w-4 h-4 text-gold"); ?>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-slate-700"><?php echo esc_html($label); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Right Column: Sticky Booking Widget -->
    <div class="lg:col-span-4">
      <div class="sticky top-28 bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-gold/40 space-y-6">
        <div>
          <span class="text-xs font-bold text-gold uppercase tracking-widest block">Ưu đãi độc quyền</span>
          <h3 class="font-display text-xl font-bold text-navy-deep">Đặt Phòng Giá Tốt Nhất</h3>
          <p class="text-xs text-slate-500 mt-1">Miễn phí buffet sáng & hồ bơi vô cực tầng 12</p>
        </div>

        <div class="space-y-3 pt-2">
          <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200 text-xs text-amber-900 space-y-1">
            <div class="flex justify-between font-semibold">
              <span>Đánh giá từ khách:</span>
              <span class="text-gold font-bold">★ <?php echo esc_html($rating); ?>/5 (<?php echo intval($reviews_cnt); ?> lượt)</span>
            </div>
            <div class="flex justify-between">
              <span>Thanh toán:</span>
              <span>Linh hoạt tại khách sạn</span>
            </div>
            <div class="flex justify-between">
              <span>Hủy phòng:</span>
              <span class="text-emerald-700 font-semibold">Miễn phí trước 24h</span>
            </div>
          </div>

          <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal('<?php echo esc_js(get_post_field('post_name', $post_id)); ?>')" 
                  class="w-full py-4 rounded-2xl bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-sm uppercase tracking-wider shadow-xl hover:brightness-110 gold-shimmer-btn transition-all text-center">
            Đặt phòng này ngay
          </button>

          <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="w-full py-3 rounded-2xl border border-slate-200 hover:border-gold text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            <span>Tư vấn: <?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
          </a>
        </div>
      </div>
    </div>

  </div>
</main>

<?php
endwhile;

get_footer();
