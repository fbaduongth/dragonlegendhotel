<?php
/**
 * The template for displaying all single posts
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
    <?php while (have_posts()) : the_post(); ?>
      <article class="bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-slate-200/80">
        <header class="mb-8 border-b border-slate-100 pb-6">
          <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
            <span><?php echo get_the_date(); ?></span>
            <span>•</span>
            <span><?php the_author(); ?></span>
            <?php 
            $location = get_post_meta(get_the_ID(), '_dl_amenity_location', true);
            if ($location) : ?>
              <span>•</span>
              <span class="text-amber-600 font-bold tracking-wider uppercase"><?php echo esc_html($location); ?></span>
            <?php endif; ?>
          </div>
          <h1 class="font-display text-3xl sm:text-4xl font-bold text-navy-deep"><?php the_title(); ?></h1>
        </header>

        <?php 
        $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if (!$thumb_url) {
            $thumb_url = get_post_meta(get_the_ID(), '_dl_amenity_sample_image', true);
        }
        if (!$thumb_url) {
            $thumb_url = get_post_meta(get_the_ID(), '_dl_offer_sample_image', true);
        }
        if ($thumb_url) : ?>
          <div class="rounded-2xl overflow-hidden mb-8 max-h-[450px] shadow-md">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
          </div>
        <?php endif; ?>

        <?php
        $highlights = get_post_meta(get_the_ID(), '_dl_amenity_highlights', true);
        if (!empty($highlights)) :
            $hl_arr = explode("\n", trim($highlights));
        ?>
          <div class="mb-8 p-5 rounded-2xl bg-amber-50/70 border border-amber-200/60">
            <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-3">Điểm nổi bật & Đặc quyền:</h4>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($hl_arr as $hl) : ?>
                <?php $clean_hl = trim($hl); if (empty($clean_hl)) continue; ?>
                <span class="px-3 py-1.5 rounded-full bg-white text-navy-deep text-xs font-medium border border-amber-200 shadow-sm flex items-center gap-1.5">
                  <span class="text-amber-500">★</span> <?php echo esc_html(ltrim($clean_hl, "★\t ")); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="entry-content prose prose-lg max-w-none text-slate-700 leading-relaxed text-base sm:text-lg">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</main>

<?php
get_footer();
