<?php
/**
 * Single Offer Template (single-dl_offer.php)
 * Giao diện chi tiết từng gói ưu đãi & khuyến mãi
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();

while (have_posts()) : the_post();
    $post_id   = get_the_ID();
    $tag       = get_post_meta($post_id, '_dl_offer_tag', true);
    $badge     = get_post_meta($post_id, '_dl_offer_badge', true);
    $price_txt = get_post_meta($post_id, '_dl_offer_price_text', true);
    $expiry    = get_post_meta($post_id, '_dl_offer_expiry', true);
    $benefits  = get_post_meta($post_id, '_dl_offer_benefits', true);
    $benefits_arr = !empty($benefits) ? explode("\n", trim($benefits)) : array();

    $img_url   = get_the_post_thumbnail_url($post_id, 'full');
    if (!$img_url) {
        $img_url = get_post_meta($post_id, '_dl_offer_sample_image', true) ?: 'https://media.dragonlegendhotel.com/2026/02/fsdffsdfsd-658x492.jpg';
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
          <a href="<?php echo esc_url(home_url('/#offers')); ?>" class="hover:text-gold">Ưu đãi</a>
          <span>/</span>
          <span class="text-navy-deep font-semibold"><?php the_title(); ?></span>
        </nav>
      <?php endif; ?>
    </div>

    <!-- Card Container -->
    <article class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80">
      <div class="relative h-[280px] sm:h-[420px]">
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-navy-deep/85 via-navy-deep/30 to-transparent"></div>
        <div class="absolute bottom-5 left-5 right-5 sm:bottom-6 sm:left-6 sm:right-6 text-white">
          <?php if ($badge) : ?>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white uppercase tracking-wider inline-block mb-2">
              <?php echo esc_html($badge); ?>
            </span>
          <?php endif; ?>
          <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold leading-[1.28]"><?php the_title(); ?></h1>
          <?php if ($expiry) : ?>
            <span class="text-xs text-amber-200 mt-1 block">Thời hạn: <?php echo esc_html($expiry); ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="p-5 sm:p-8 lg:p-10 space-y-6 sm:space-y-8">
        <!-- Price Banner -->
        <div class="p-6 rounded-2xl bg-amber-50 border border-gold/40 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <span class="text-xs text-slate-500 uppercase tracking-wider block">Mức giá áp dụng</span>
            <span class="font-display text-2xl font-bold text-amber-800"><?php echo esc_html($price_txt); ?></span>
          </div>
          <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal()" 
                  class="px-8 py-3.5 bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all gold-shimmer-btn">
            Đặt gói ưu đãi này
          </button>
        </div>

        <!-- Benefits Checklist -->
        <?php if (!empty($benefits_arr)) : ?>
          <div>
            <h2 class="font-display text-xl font-bold text-navy-deep mb-4">Đặc Quyền & Quyền Lợi Đi Kèm</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <?php foreach ($benefits_arr as $b) : ?>
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs sm:text-sm text-slate-700">
                  <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span><?php echo esc_html(trim($b)); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Full Content -->
        <div>
          <h2 class="font-display text-xl font-bold text-navy-deep mb-4">Chi Tiết Chương Trình</h2>
          <div class="entry-content prose max-w-none text-slate-700 leading-relaxed text-[15px] sm:text-base">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </article>

    <?php
    // Các chương trình ưu đãi liên quan
    $other_offers = new WP_Query(array(
        'post_type'      => 'dl_offer',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'post__not_in'   => array($post_id),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    if ($other_offers->have_posts()) :
    ?>
      <section class="mt-14 pt-10 border-t border-slate-200/80" aria-label="Các ưu đãi khác">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
          <div>
            <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
              <span class="w-5 h-[2px] bg-gold inline-block"></span>
              <span>Khám Phá Thêm</span>
            </div>
            <h2 class="font-display text-2xl sm:text-3xl font-bold text-navy-deep">Các Gói Ưu Đãi Khác</h2>
          </div>
          <a href="<?php echo esc_url(get_post_type_archive_link('dl_offer')); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-gold uppercase tracking-wider transition-colors">
            <span>Xem tất cả ưu đãi</span>
            <span>&rarr;</span>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <?php
          while ($other_offers->have_posts()) : $other_offers->the_post();
              $oid   = get_the_ID();
              $oimg  = get_the_post_thumbnail_url($oid, 'large') ?: get_post_meta($oid, '_dl_offer_sample_image', true);
              $obadge = get_post_meta($oid, '_dl_offer_badge', true);
              $oprice = get_post_meta($oid, '_dl_offer_price_text', true);
          ?>
            <article class="bg-white rounded-3xl overflow-hidden shadow-md border border-slate-200/80 flex flex-col justify-between hover:shadow-xl hover:border-gold transition-all duration-300">
              <div>
                <a href="<?php the_permalink(); ?>" class="block relative h-52 overflow-hidden image-zoom-container">
                  <img src="<?php echo esc_url($oimg); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                  <?php if ($obadge) : ?>
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider shadow">
                      <?php echo esc_html($obadge); ?>
                    </span>
                  <?php endif; ?>
                  <?php if ($oprice) : ?>
                    <span class="absolute bottom-3 right-3 px-3 py-1 rounded-full text-[11px] font-bold bg-navy-deep/85 text-amber-200 backdrop-blur-sm">
                      <?php echo esc_html($oprice); ?>
                    </span>
                  <?php endif; ?>
                </a>
                <div class="p-6">
                  <h3 class="font-display font-bold text-lg text-navy-deep mb-2 leading-snug">
                    <a href="<?php the_permalink(); ?>" class="hover:text-gold transition-colors"><?php the_title(); ?></a>
                  </h3>
                  <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                    <?php echo esc_html(get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 25)); ?>
                  </p>
                </div>
              </div>
              <div class="px-6 pb-6 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="<?php the_permalink(); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-gold transition-colors uppercase tracking-wider">
                  <span>Xem chi tiết</span>
                  <span>&rarr;</span>
                </a>
              </div>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php
endwhile;

get_footer();
