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
        $gallery = $feat ? array($feat) : array('https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg');
    }

    $amenities_keys = get_post_meta($post_id, '_dl_room_amenities', true);
    if (!is_array($amenities_keys)) $amenities_keys = array();
    $amenities_title = method_exists('DragonLegend_Metaboxes', 'get_room_amenities_title')
        ? DragonLegend_Metaboxes::get_room_amenities_title()
        : get_option('dragonlegend_room_amenities_title', 'Tiện Nghi Phòng Cao Cấp');
?>

<main id="main" class="pt-6 sm:pt-10 lg:pt-12 pb-12 sm:pb-16 lg:pb-20 bg-cream" role="main">
  <!-- Breadcrumb & Title Header -->
  <div class="max-w-7xl mx-auto px-4 mb-6 sm:mb-8">
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
        <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28]">
          <?php the_title(); ?>
        </h1>
        <?php if ($subtitle) : ?>
          <p class="text-[13.5px] sm:text-[15px] text-slate-600 mt-1.5"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
      </div>

      <div class="text-left md:text-right flex-shrink-0">
        <?php if ($orig_price) : ?>
          <span class="text-sm line-through text-slate-400 block"><?php echo dl_format_price($orig_price); ?></span>
        <?php endif; ?>
        <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-amber-700"><?php echo dl_format_price($price ?: 1050000); ?></span>
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
      <div class="bg-white p-5 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep">Không Gian & Thiết Kế</h2>
        <div class="entry-content prose max-w-none text-slate-600 leading-relaxed text-[15px] sm:text-base">
          <?php the_content(); ?>
        </div>
      </div>

      <!-- Amenities Section -->
      <?php if (!empty($amenities_keys)) : ?>
        <div class="bg-white p-5 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
          <h2 class="font-display text-xl sm:text-2xl font-bold text-navy-deep mb-5 sm:mb-6"><?php echo esc_html($amenities_title ?: 'Tiện Nghi Phòng Cao Cấp'); ?></h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <?php foreach ($amenities_keys as $ak) :
                if (function_exists('dl_get_room_amenity_detail')) {
                    $am_detail = dl_get_room_amenity_detail($ak);
                } elseif (method_exists('DragonLegend_Metaboxes', 'get_room_amenity_detail')) {
                    $am_detail = DragonLegend_Metaboxes::get_room_amenity_detail($ak);
                } else {
                    $am_detail = array('label' => is_string($ak) ? $ak : '', 'icon' => is_string($ak) ? $ak : 'sparkles');
                }
                $label = !empty($am_detail['label']) ? $am_detail['label'] : (is_string($ak) ? $ak : '');
                $icon  = !empty($am_detail['icon']) ? $am_detail['icon'] : (is_string($ak) ? $ak : 'sparkles');
                if (empty($label)) continue;
            ?>
              <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-gold border border-gold/30 flex items-center justify-center flex-shrink-0 overflow-hidden">
                  <?php echo dl_get_amenity_svg($icon, "w-4 h-4 text-gold"); ?>
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
      <div class="sticky top-24 bg-white p-5 sm:p-8 rounded-3xl shadow-xl border border-gold/40 space-y-6">
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

          <button type="button"
                  onclick="window.App && window.App.compareWithRoom('<?php echo esc_js(get_post_field('post_name', $post_id)); ?>')"
                  class="w-full py-3 rounded-2xl bg-navy-deep hover:bg-navy text-gold border border-gold/40 font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-sm">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>So sánh với hạng phòng khác</span>
          </button>

          <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="w-full py-3 rounded-2xl border border-slate-200 hover:border-gold text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            <span>Tư vấn: <?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
          </a>
        </div>
      </div>
    </div>

  </div>

  <?php
  /**
   * ======================================================================
   * DANH SÁCH CÁC HẠNG PHÒNG LIÊN QUAN
   * ======================================================================
   */
  $related_room_ids = array();
  $exclude_room_ids = array($post_id);
  $target_rooms     = 3;

  // 1. Ưu tiên lấy phòng cùng phân hạng (dl_room_type) nếu có
  $room_terms = wp_get_post_terms($post_id, 'dl_room_type', array('fields' => 'ids'));
  if (!empty($room_terms) && !is_wp_error($room_terms)) {
      $same_type_rooms = get_posts(array(
          'post_type'      => 'dl_room',
          'post_status'    => 'publish',
          'posts_per_page' => $target_rooms,
          'post__not_in'   => $exclude_room_ids,
          'fields'         => 'ids',
          'tax_query'      => array(
              array(
                  'taxonomy' => 'dl_room_type',
                  'field'    => 'term_id',
                  'terms'    => $room_terms,
              ),
          ),
      ));
      if (!empty($same_type_rooms)) {
          $related_room_ids = array_merge($related_room_ids, $same_type_rooms);
          $exclude_room_ids = array_merge($exclude_room_ids, $same_type_rooms);
      }
  }

  // 2. Bổ sung các hạng phòng khác để đủ 3 phòng gợi ý
  $remaining_rooms = $target_rooms - count($related_room_ids);
  if ($remaining_rooms > 0) {
      $more_rooms = get_posts(array(
          'post_type'      => 'dl_room',
          'post_status'    => 'publish',
          'posts_per_page' => $remaining_rooms,
          'post__not_in'   => $exclude_room_ids,
          'fields'         => 'ids',
          'orderby'        => 'menu_order date',
          'order'          => 'ASC',
      ));
      if (!empty($more_rooms)) {
          $related_room_ids = array_merge($related_room_ids, $more_rooms);
      }
  }

  if (!empty($related_room_ids)) :
      $related_rooms_query = new WP_Query(array(
          'post_type'           => 'dl_room',
          'post_status'         => 'publish',
          'post__in'            => $related_room_ids,
          'orderby'             => 'post__in',
          'posts_per_page'      => count($related_room_ids),
          'ignore_sticky_posts' => true,
      ));
  ?>
    <section class="max-w-7xl mx-auto px-4 mt-12 sm:mt-16 pt-10 border-t border-slate-200/80" aria-label="Các hạng phòng liên quan">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
          <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
            <span class="w-5 h-[2px] bg-gold inline-block"></span>
            <span>Gợi Ý Lựa Chọn Khác</span>
          </div>
          <h2 class="font-display text-2xl sm:text-3xl font-bold text-navy-deep leading-[1.28]">Các Hạng Phòng Liên Quan</h2>
        </div>
        <a href="<?php echo esc_url(get_post_type_archive_link('dl_room')); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-gold uppercase tracking-wider transition-colors">
          <span>Xem tất cả hạng phòng</span>
          <span>&rarr;</span>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <?php
        while ($related_rooms_query->have_posts()) : $related_rooms_query->the_post();
            $r_id         = get_the_ID();
            $r_slug       = get_post_field('post_name', $r_id);
            $r_price      = get_post_meta($r_id, '_dl_room_price', true);
            $r_orig_price = get_post_meta($r_id, '_dl_room_original_price', true);
            $r_size       = get_post_meta($r_id, '_dl_room_size', true) ?: '28 - 32 m²';
            $r_bed        = get_post_meta($r_id, '_dl_room_bed', true) ?: '1 Giường King hoặc 2 Giường đơn';
            $r_view       = get_post_meta($r_id, '_dl_room_view', true) ?: 'Hướng thành phố';
            $r_badge      = get_post_meta($r_id, '_dl_room_badge', true);
            $r_subtitle   = get_post_meta($r_id, '_dl_room_subtitle', true);
            $r_img        = get_the_post_thumbnail_url($r_id, 'large');
            if (!$r_img) {
                $r_img = get_post_meta($r_id, '_dl_room_sample_image', true) ?: 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg';
            }
        ?>
          <article class="group bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 flex flex-col justify-between hover:shadow-2xl hover:border-gold transition-all duration-300">
            <div>
              <div class="relative h-56 sm:h-60 image-zoom-container overflow-hidden cursor-pointer"
                   onclick="window.App && window.App.openRoomDetail('<?php echo esc_js($r_slug); ?>')">
                <img src="<?php echo esc_url($r_img); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                <?php if ($r_badge) : ?>
                  <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider shadow pointer-events-none">
                    <?php echo esc_html($r_badge); ?>
                  </span>
                <?php endif; ?>
                <button type="button"
                        data-compare-room="<?php echo esc_attr($r_slug); ?>"
                        onclick="window.App && window.App.toggleCompareRoom('<?php echo esc_js($r_slug); ?>', event)"
                        class="dl-compare-card-pill"
                        title="Thêm vào so sánh phòng">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                  <span class="dl-compare-btn-label">So sánh</span>
                </button>
                <span class="absolute bottom-4 right-4 px-3 py-1 rounded-full text-[10px] font-semibold bg-navy-deep/80 backdrop-blur-md text-slate-200 pointer-events-none">
                  <?php echo esc_html($r_view); ?>
                </span>
              </div>

              <div class="p-5 sm:p-6">
                <h3 class="font-display font-bold text-lg sm:text-xl text-navy-deep mb-2 leading-snug">
                  <a href="<?php the_permalink(); ?>"
                     onclick="if (window.App) { event.preventDefault(); window.App.openRoomDetail('<?php echo esc_js($r_slug); ?>'); }"
                     class="hover:text-gold transition-colors cursor-pointer inline-flex items-center gap-1.5"
                     title="Xem nhanh <?php the_title_attribute(); ?>">
                    <span><?php the_title(); ?></span>
                  </a>
                </h3>
                <p class="text-[13px] sm:text-sm text-slate-500 mb-4 line-clamp-2 leading-relaxed">
                  <?php
                  $r_excerpt = $r_subtitle ?: get_the_excerpt();
                  if (empty($r_excerpt)) {
                      $r_excerpt = wp_trim_words(wp_strip_all_tags(get_the_content()), 22);
                  }
                  echo esc_html($r_excerpt);
                  ?>
                </p>

                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 pt-2.5 border-t border-slate-100">
                  <div class="truncate">Diện tích: <strong class="text-navy-deep"><?php echo esc_html($r_size); ?></strong></div>
                  <div class="truncate">Giường: <strong class="text-navy-deep"><?php echo esc_html($r_bed); ?></strong></div>
                </div>
              </div>
            </div>

            <div class="px-5 sm:px-6 pb-5 sm:pb-6 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
              <div>
                <?php if ($r_orig_price) : ?>
                  <span class="text-xs line-through text-slate-400 block"><?php echo dl_format_price($r_orig_price); ?></span>
                <?php endif; ?>
                <span class="text-base sm:text-lg font-bold text-amber-700"><?php echo dl_format_price($r_price ?: 1050000); ?></span>
                <span class="text-[10px] text-slate-400 block">/ đêm</span>
              </div>

              <div class="flex items-center gap-2">
                <a href="<?php the_permalink(); ?>" class="px-3.5 py-2 border border-slate-200 text-slate-700 hover:border-gold hover:text-gold font-bold text-xs rounded-xl transition-colors whitespace-nowrap">
                  Chi tiết
                </a>
                <button type="button" onclick="window.BookingEngine && window.BookingEngine.openBookingModal('<?php echo esc_js(get_post_field('post_name', $r_id)); ?>')" 
                        class="px-4 py-2 bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs rounded-xl shadow hover:brightness-110 transition-all whitespace-nowrap">
                  Đặt ngay
                </button>
              </div>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php
endwhile;

get_footer();
