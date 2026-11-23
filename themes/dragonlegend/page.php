<?php
/**
 * The template for displaying all pages
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();
?>

<main id="main" class="pt-6 sm:pt-10 lg:pt-12 pb-12 sm:pb-16 lg:pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
      <div class="dl-breadcrumb-wrapper mb-4 sm:mb-6">
        <?php rank_math_the_breadcrumbs(); ?>
      </div>
    <?php endif; ?>
    <?php while (have_posts()) : the_post(); ?>
      <article class="bg-white p-5 sm:p-8 lg:p-12 rounded-3xl shadow-xl border border-slate-200/80">
        <header class="mb-6 sm:mb-8 border-b border-slate-100 pb-5 sm:pb-6">
          <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28] tracking-tight"><?php the_title(); ?></h1>
        </header>

        <div class="entry-content prose max-w-none text-slate-700 leading-relaxed text-[15px] sm:text-base">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</main>

<?php
get_footer();
