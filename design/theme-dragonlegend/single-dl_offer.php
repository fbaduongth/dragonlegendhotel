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
        $img_url = get_post_meta($post_id, '_dl_offer_sample_image', true) ?: 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/fsdffsdfsd-658x492.jpg';
    }
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
          <a href="<?php echo esc_url(home_url('/#offers')); ?>" class="hover:text-gold">Ưu đãi</a>
          <span>/</span>
          <span class="text-navy-deep font-semibold"><?php the_title(); ?></span>
        </nav>
      <?php endif; ?>
    </div>

    <!-- Card Container -->
    <article class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80">
      <div class="relative h-[360px] sm:h-[450px]">
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-navy-deep/80 via-transparent to-transparent"></div>
        <div class="absolute bottom-6 left-6 right-6 text-white">
          <?php if ($badge) : ?>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white uppercase tracking-wider inline-block mb-2">
              <?php echo esc_html($badge); ?>
            </span>
          <?php endif; ?>
          <h1 class="font-display text-3xl sm:text-4xl font-bold leading-tight"><?php the_title(); ?></h1>
          <?php if ($expiry) : ?>
            <span class="text-xs text-amber-200 mt-1 block">Thời hạn: <?php echo esc_html($expiry); ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="p-8 sm:p-10 space-y-8">
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
          <div class="entry-content prose max-w-none text-slate-600 leading-relaxed text-sm sm:text-base">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </article>
  </div>
</main>

<?php
endwhile;

get_footer();
