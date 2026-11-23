<?php
/**
 * Archive Room & Room Type Taxonomy Template (archive-dl_room.php / taxonomy-dl_room_type.php)
 * Danh sách các hạng phòng nghỉ Dragon Legend Hotel
 *
 * @package DragonLegend
 * @version 1.3.0
 */

get_header();

$is_room_tax   = is_tax('dl_room_type');
$current_term  = $is_room_tax ? get_queried_object() : null;
$current_slug  = ($current_term && isset($current_term->slug)) ? $current_term->slug : '';

$header_title = 'Các Hạng Phòng & Suite Sang Trọng';
$header_desc  = 'Khám phá không gian nghỉ dưỡng chuẩn 4 sao quốc tế với 138 phòng tiện nghi, tầm nhìn khoáng đạt ôm trọn kỳ quan Vịnh Hạ Long.';

if ($is_room_tax && $current_term) {
    $term_title = single_term_title('', false);
    if (!empty($term_title)) {
        $header_title = $term_title;
    }
    $raw_term_desc = term_description();
    if (!empty($raw_term_desc)) {
        $header_desc = wp_strip_all_tags($raw_term_desc);
    }
}

$room_type_terms = get_terms(array(
    'taxonomy'   => 'dl_room_type',
    'hide_empty' => true,
));
if (!is_wp_error($room_type_terms) && !empty($room_type_terms)) {
    usort($room_type_terms, function($a, $b) {
        $oa = get_term_meta($a->term_id, '_dl_room_type_order', true);
        $ob = get_term_meta($b->term_id, '_dl_room_type_order', true);
        $oa = ($oa !== '' && $oa !== false) ? (int) $oa : 999;
        $ob = ($ob !== '' && $ob !== false) ? (int) $ob : 999;
        if ($oa !== $ob) {
            return $oa - $ob;
        }
        return ((int) $a->term_id) - ((int) $b->term_id);
    });
}
?>

<main id="main" class="pt-6 sm:pt-10 lg:pt-12 pb-12 sm:pb-16 lg:pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
      <div class="dl-breadcrumb-wrapper mb-4 sm:mb-6">
        <?php rank_math_the_breadcrumbs(); ?>
      </div>
    <?php endif; ?>
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-12">
      <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
        <span>Danh Sách Phòng Nghỉ</span>
        <span class="w-6 h-[2px] bg-gold inline-block"></span>
      </div>
      <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28] mb-2.5">
        <?php echo esc_html($header_title); ?>
      </h1>
      <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed max-w-2xl mx-auto">
        <?php echo esc_html($header_desc); ?>
      </p>

      <?php if (!empty($room_type_terms) && !is_wp_error($room_type_terms)) : ?>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 sm:gap-2.5">
          <a href="<?php echo esc_url(get_post_type_archive_link('dl_room')); ?>"
             class="px-4 py-2 rounded-full text-xs font-bold transition-all <?php echo empty($current_slug) ? 'bg-gold text-navy-deep shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:border-gold'; ?>">
            Tất cả phòng
          </a>
          <?php foreach ($room_type_terms as $rt) :
              $rt_link = get_term_link($rt, 'dl_room_type');
              if (is_wp_error($rt_link)) continue;
          ?>
            <a href="<?php echo esc_url($rt_link); ?>"
               class="px-4 py-2 rounded-full text-xs font-bold transition-all <?php echo ($current_slug === $rt->slug) ? 'bg-gold text-navy-deep shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:border-gold'; ?>">
              <?php echo esc_html($rt->name); ?>
            </a>
          <?php endforeach; ?>
          <button type="button" onclick="window.App && window.App.openCompareModal()" class="dl-compare-header-btn px-4 py-2 rounded-full bg-navy-deep text-gold border border-gold/40 hover:bg-navy text-xs font-bold inline-flex items-center gap-2 shadow-md transition-all">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>So sánh phòng</span>
            <span class="dl-compare-count-badge hidden" data-compare-count>0</span>
          </button>
        </div>
      <?php else : ?>
        <div class="mt-4 flex justify-center">
          <button type="button" onclick="window.App && window.App.openCompareModal()" class="dl-compare-header-btn px-5 py-2.5 rounded-full bg-navy-deep text-gold border border-gold/40 hover:bg-navy text-xs font-bold inline-flex items-center gap-2 shadow-md transition-all">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>Bảng so sánh các hạng phòng</span>
            <span class="dl-compare-count-badge hidden" data-compare-count>0</span>
          </button>
        </div>
      <?php endif; ?>
    </div>

    <!-- Grid of Rooms -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <?php
      if (have_posts()) :
          while (have_posts()) : the_post();
              $post_id    = get_the_ID();
              $room_slug  = get_post_field('post_name', $post_id);
              $price      = get_post_meta($post_id, '_dl_room_price', true);
              $orig_price = get_post_meta($post_id, '_dl_room_original_price', true);
              $size       = get_post_meta($post_id, '_dl_room_size', true) ?: '28 - 32 m²';
              $bed        = get_post_meta($post_id, '_dl_room_bed', true) ?: '1 Giường King hoặc 2 Giường đơn';
              $view       = get_post_meta($post_id, '_dl_room_view', true) ?: 'Hướng thành phố';
              $badge      = get_post_meta($post_id, '_dl_room_badge', true);
              $img_url    = get_the_post_thumbnail_url($post_id, 'large');
              if (!$img_url) {
                  $img_url = get_post_meta($post_id, '_dl_room_sample_image', true) ?: 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg';
              }
      ?>
        <article class="group bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 flex flex-col justify-between hover:shadow-2xl hover:border-gold transition-all duration-300">
          <div>
            <div class="relative h-60 image-zoom-container cursor-pointer" onclick="window.App && window.App.openRoomDetail('<?php echo esc_js($room_slug); ?>')">
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
              <?php if ($badge) : ?>
                <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider shadow pointer-events-none">
                  <?php echo esc_html($badge); ?>
                </span>
              <?php endif; ?>
              <button type="button"
                      data-compare-room="<?php echo esc_attr($room_slug); ?>"
                      onclick="window.App && window.App.toggleCompareRoom('<?php echo esc_js($room_slug); ?>', event)"
                      class="dl-compare-card-pill"
                      title="Thêm vào so sánh phòng">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="dl-compare-btn-label">So sánh</span>
              </button>
              <span class="absolute bottom-4 right-4 px-3 py-1 rounded-full text-[10px] font-semibold bg-navy-deep/80 backdrop-blur-md text-slate-200 pointer-events-none">
                <?php echo esc_html($view); ?>
              </span>
            </div>

            <div class="p-6">
              <h2 class="font-display font-bold text-xl text-navy-deep mb-2">
                <a href="<?php the_permalink(); ?>"
                   onclick="if (window.App) { event.preventDefault(); window.App.openRoomDetail('<?php echo esc_js($room_slug); ?>'); }"
                   class="hover:text-gold transition-colors cursor-pointer inline-flex items-center gap-1.5"
                   title="Xem nhanh <?php the_title_attribute(); ?>">
                  <span><?php the_title(); ?></span>
                </a>
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

    <?php
    if (function_exists('dragonlegend_render_pagination')) {
        dragonlegend_render_pagination();
    }
    ?>
  </div>
</main>

<?php
get_footer();
