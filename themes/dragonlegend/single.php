<?php
/**
 * The template for displaying all single posts
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
      <article class="bg-white p-5 sm:p-8 lg:p-10 rounded-3xl shadow-lg border border-slate-200/80">
        <header class="mb-6 sm:mb-8 border-b border-slate-100 pb-5 sm:pb-6">
          <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-500 mb-2.5">
            <span>Đăng ngày: <strong class="font-semibold text-slate-700"><?php echo get_the_date('d/m/Y'); ?></strong> bởi: <strong class="font-semibold text-slate-700"><?php the_author(); ?></strong></span>
            <?php 
            $location = get_post_meta(get_the_ID(), '_dl_amenity_location', true);
            if ($location) : ?>
              <span>•</span>
              <span class="text-amber-600 font-bold tracking-wider uppercase"><?php echo esc_html($location); ?></span>
            <?php endif; ?>
          </div>
          <h1 class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.3]"><?php the_title(); ?></h1>
        </header>

        <?php 
        // Chỉ hiển thị ảnh đại diện ở đầu bài viết nếu trong nội dung bài viết chưa có thẻ <img> (ví dụ: các bài Dịch vụ mẫu)
        // Tránh lặp lại 2 lần ảnh thumbnail đối với các bài viết Tin tức đã chèn sẵn ảnh trong nội dung
        $raw_post_content = get_post_field('post_content', get_the_ID());
        $has_content_img  = (stripos($raw_post_content, '<img') !== false);
        $thumb_url        = '';
        if (!$has_content_img) {
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumb_url) {
                $thumb_url = get_post_meta(get_the_ID(), '_dl_post_featured_image', true);
            }
            if (!$thumb_url) {
                $thumb_url = get_post_meta(get_the_ID(), '_dl_amenity_sample_image', true);
            }
            if (!$thumb_url) {
                $thumb_url = get_post_meta(get_the_ID(), '_dl_offer_sample_image', true);
            }
        }
        if ($thumb_url) : ?>
          <div class="max-w-3xl mx-auto rounded-2xl overflow-hidden mb-8 max-h-[420px] shadow-md">
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

        <div class="entry-content prose max-w-none text-slate-700 leading-relaxed text-[15px] sm:text-base">
          <?php the_content(); ?>
        </div>

        <?php
        // Hiển thị danh sách Từ khóa (Tags) nếu có
        $post_tags = get_the_tags();
        if (!empty($post_tags)) :
        ?>
          <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Từ khóa:</span>
            <?php foreach ($post_tags as $tag_obj) : ?>
              <a href="<?php echo esc_url(get_tag_link($tag_obj->term_id)); ?>" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-amber-50 text-xs font-medium text-slate-700 hover:text-amber-800 border border-slate-200 hover:border-amber-300 transition-colors">
                #<?php echo esc_html($tag_obj->name); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>

      <?php
      /**
       * ======================================================================
       * DANH SÁCH BÀI VIẾT LIÊN QUAN (Theo Từ khóa -> Theo Danh mục -> Ngẫu nhiên)
       * ======================================================================
       */
      $current_post_id = get_the_ID();
      $target_count    = has_category('dich-vu', $current_post_id) ? 3 : 6;
      $related_ids     = array();
      $exclude_ids     = array($current_post_id);

      // 1. Ưu tiên 1: Lấy bài viết cùng Từ khóa (Tags)
      $tags = wp_get_post_tags($current_post_id, array('fields' => 'ids'));
      if (!empty($tags)) {
          $tag_posts = get_posts(array(
              'post_type'      => 'post',
              'post_status'    => 'publish',
              'posts_per_page' => $target_count,
              'tag__in'        => $tags,
              'post__not_in'   => $exclude_ids,
              'fields'         => 'ids',
              'orderby'        => 'rand',
          ));
          if (!empty($tag_posts)) {
              $related_ids = array_merge($related_ids, $tag_posts);
              $exclude_ids = array_merge($exclude_ids, $tag_posts);
          }
      }

      // 2. Ưu tiên 2: Lấy bài viết cùng Chuyên mục (Categories) nếu chưa đủ số lượng
      $remaining = $target_count - count($related_ids);
      $cats      = wp_get_post_categories($current_post_id, array('fields' => 'ids'));
      if ($remaining > 0 && !empty($cats)) {
          $cat_posts = get_posts(array(
              'post_type'      => 'post',
              'post_status'    => 'publish',
              'posts_per_page' => $remaining,
              'category__in'   => $cats,
              'post__not_in'   => $exclude_ids,
              'fields'         => 'ids',
              'orderby'        => 'rand',
          ));
          if (!empty($cat_posts)) {
              $related_ids = array_merge($related_ids, $cat_posts);
              $exclude_ids = array_merge($exclude_ids, $cat_posts);
          }
      }

      // 3. Ưu tiên 3: Lấy bài viết ngẫu nhiên khác trên website nếu vẫn chưa đủ
      $remaining = $target_count - count($related_ids);
      if ($remaining > 0) {
          $rand_posts = get_posts(array(
              'post_type'      => 'post',
              'post_status'    => 'publish',
              'posts_per_page' => $remaining,
              'post__not_in'   => $exclude_ids,
              'fields'         => 'ids',
              'orderby'        => 'rand',
          ));
          if (!empty($rand_posts)) {
              $related_ids = array_merge($related_ids, $rand_posts);
          }
      }

      if (!empty($related_ids)) :
          $related_query = new WP_Query(array(
              'post_type'           => 'post',
              'post_status'         => 'publish',
              'post__in'            => $related_ids,
              'orderby'             => 'post__in',
              'posts_per_page'      => count($related_ids),
              'ignore_sticky_posts' => true,
          ));
          $primary_cat      = (!empty($cats)) ? get_category($cats[0]) : null;
          $primary_cat_link = ($primary_cat && !is_wp_error($primary_cat)) ? get_category_link($primary_cat->term_id) : home_url('/tin-tuc/');
          $primary_cat_name = ($primary_cat && !is_wp_error($primary_cat)) ? $primary_cat->name : 'Tin tức';
      ?>
        <section class="mt-14 pt-10 border-t border-slate-200/80" aria-label="Bài viết liên quan">
          <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
              <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
                <span class="w-5 h-[2px] bg-gold inline-block"></span>
                <span>Gợi Ý Dành Cho Bạn</span>
              </div>
              <h2 class="font-display text-2xl sm:text-3xl font-bold text-navy-deep">Bài Viết Liên Quan</h2>
            </div>
            <a href="<?php echo esc_url($primary_cat_link); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-gold uppercase tracking-wider transition-colors">
              <span>Xem tất cả <?php echo esc_html($primary_cat_name); ?></span>
              <span>&rarr;</span>
            </a>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <?php
            while ($related_query->have_posts()) : $related_query->the_post();
                $rel_id  = get_the_ID();
                $rel_img = get_the_post_thumbnail_url($rel_id, 'large');
                if (empty($rel_img)) {
                    $rel_img = get_post_meta($rel_id, '_dl_post_featured_image', true);
                }
                if (empty($rel_img)) {
                    $rel_img = get_post_meta($rel_id, '_dl_offer_sample_image', true);
                }
                if (empty($rel_img)) {
                    $rel_img = get_post_meta($rel_id, '_dl_amenity_sample_image', true);
                }
                if (empty($rel_img)) {
                    if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', get_the_content(), $m)) {
                        $rel_img = $m[1];
                    }
                }
                if (empty($rel_img)) {
                    $rel_img = 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg';
                }
            ?>
              <article class="bg-white rounded-3xl overflow-hidden shadow-md border border-slate-200/80 flex flex-col justify-between hover:shadow-xl hover:border-gold transition-all duration-300">
                <div>
                  <a href="<?php the_permalink(); ?>" class="block relative h-48 sm:h-52 overflow-hidden image-zoom-container">
                    <img src="<?php echo esc_url($rel_img); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                  </a>
                  <div class="p-5 sm:p-6">
                    <span class="text-[11px] text-slate-500 block mb-1.5">Đăng ngày: <?php echo get_the_date('d/m/Y'); ?></span>
                    <h3 class="font-display font-bold text-lg text-navy-deep mb-2 leading-snug line-clamp-2">
                      <a href="<?php the_permalink(); ?>" class="hover:text-gold transition-colors"><?php the_title(); ?></a>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                      <?php
                      $rel_excerpt = get_the_excerpt();
                      if (empty($rel_excerpt)) {
                          $rel_excerpt = wp_trim_words(wp_strip_all_tags(get_the_content()), 22);
                      }
                      echo esc_html($rel_excerpt);
                      ?>
                    </p>
                  </div>
                </div>
                <div class="px-5 sm:px-6 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                  <a href="<?php the_permalink(); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-gold transition-colors uppercase tracking-wider">
                    <span>Đọc tiếp</span>
                    <span>&rarr;</span>
                  </a>
                </div>
              </article>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        </section>
      <?php endif; ?>
    <?php endwhile; ?>
  </div>
</main>

<?php
get_footer();
