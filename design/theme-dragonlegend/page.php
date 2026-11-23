<?php
/**
 * The template for displaying all pages
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
      <article class="bg-white p-8 sm:p-12 md:p-16 rounded-3xl shadow-xl border border-slate-200/80">
        <header class="mb-10 border-b border-slate-100 pb-6">
          <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-navy-deep tracking-tight"><?php the_title(); ?></h1>
        </header>

        <div class="entry-content prose prose-lg max-w-none text-slate-700 leading-relaxed">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</main>

<?php
get_footer();
