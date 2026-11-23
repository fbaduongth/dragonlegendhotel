<?php
/**
 * The main template file (Archive, Category, Post Type Archive fallback)
 *
 * @package DragonLegend
 * @version 1.3.0
 */

get_header();

// Lấy tiêu đề và mô tả động từ dữ liệu chuyên mục (Category/Taxonomy) hoặc Post Type Archive
$archive_tag   = '';
$archive_title = 'Tin Tức & Hoạt Động';
$archive_desc  = 'Cập nhật tin tức du lịch Hạ Long và các sự kiện tại Dragon Legend Hotel';

if (is_category() || is_tag() || is_tax()) {
    $archive_title = single_term_title('', false);
    $raw_desc      = term_description();
    if (!empty($raw_desc)) {
        $archive_desc = wp_strip_all_tags($raw_desc);
    } elseif (is_category('dich-vu')) {
        $archive_desc = function_exists('dl_get_content')
            ? dl_get_content('sec_amenities_dsc', 'Các dịch vụ, tiện ích đẳng cấp chuẩn 4 sao tại Dragon Legend Hotel.')
            : 'Các dịch vụ, tiện ích đẳng cấp chuẩn 4 sao tại Dragon Legend Hotel.';
    } elseif (is_category('tin-tuc')) {
        $archive_desc = 'Cập nhật tin tức du lịch Hạ Long, cẩm nang khám phá kỳ quan và các sự kiện nổi bật tại Dragon Legend Hotel.';
    } else {
        $archive_desc = '';
    }
    if (is_category('dich-vu')) {
        $archive_tag = function_exists('dl_get_content') ? dl_get_content('sec_amenities_tag', 'Trải Nghiệm Trọn Vẹn') : 'Trải Nghiệm Trọn Vẹn';
    } elseif (is_category('tin-tuc')) {
        $archive_tag = 'Cẩm Nang & Sự Kiện';
    } else {
        $archive_tag = 'Chuyên Mục';
    }
} elseif (is_post_type_archive('dl_offer')) {
    $archive_tag   = function_exists('dl_get_content') ? dl_get_content('sec_offers_tag', 'Đặc Quyền Nghỉ Dưỡng') : 'Đặc Quyền Nghỉ Dưỡng';
    $archive_title = post_type_archive_title('', false) ?: (function_exists('dl_get_content') ? dl_get_content('sec_offers_ttl', 'Ưu Đãi & Khuyến Mãi') : 'Ưu Đãi & Khuyến Mãi');
    $pt_obj        = get_queried_object();
    if ($pt_obj && !empty($pt_obj->description)) {
        $archive_desc = $pt_obj->description;
    } else {
        $archive_desc = function_exists('dl_get_content')
            ? dl_get_content('sec_offers_dsc', 'Tận hưởng trọn vẹn kỳ nghỉ đẳng cấp với các gói ưu đãi giá phòng, ẩm thực và dịch vụ được thiết kế riêng cho bạn.')
            : 'Tận hưởng trọn vẹn kỳ nghỉ đẳng cấp với các gói ưu đãi giá phòng, ẩm thực và dịch vụ được thiết kế riêng cho bạn.';
    }
} elseif (is_post_type_archive()) {
    $archive_title = post_type_archive_title('', false);
    $pt_obj        = get_queried_object();
    $archive_desc  = ($pt_obj && !empty($pt_obj->description)) ? $pt_obj->description : '';
} elseif (is_home() && !is_front_page()) {
    $page_for_posts = get_option('page_for_posts');
    if ($page_for_posts) {
        $archive_title = get_the_title($page_for_posts);
        $archive_desc  = get_the_excerpt($page_for_posts);
    }
}
?>

<main id="main" class="pt-6 sm:pt-10 lg:pt-12 pb-12 sm:pb-16 lg:pb-20 bg-cream" role="main">
  <div class="max-w-7xl mx-auto px-4">
    <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
      <div class="dl-breadcrumb-wrapper mb-4 sm:mb-6">
        <?php rank_math_the_breadcrumbs(); ?>
      </div>
    <?php endif; ?>

    <header class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
      <?php if (!empty($archive_tag)) : ?>
        <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
          <span><?php echo esc_html($archive_tag); ?></span>
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
        </div>
      <?php endif; ?>
      <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-navy-deep leading-[1.28] mb-2.5">
        <?php echo esc_html($archive_title); ?>
      </h1>
      <?php if (!empty($archive_desc)) : ?>
        <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
          <?php echo esc_html($archive_desc); ?>
        </p>
      <?php endif; ?>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <?php
      if (have_posts()) :
          while (have_posts()) : the_post();
              $post_id = get_the_ID();

              // Lấy ảnh đại diện (Featured Image) hoặc ảnh mẫu của Bài viết / Ưu đãi / Dịch vụ
              $img_url = get_the_post_thumbnail_url($post_id, 'large');
              if (empty($img_url)) {
                  $img_url = get_post_meta($post_id, '_dl_post_featured_image', true);
              }
              if (empty($img_url)) {
                  $img_url = get_post_meta($post_id, '_dl_offer_sample_image', true);
              }
              if (empty($img_url)) {
                  $img_url = get_post_meta($post_id, '_dl_amenity_sample_image', true);
              }
              if (empty($img_url)) {
                  if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', get_the_content(), $m)) {
                      $img_url = $m[1];
                  }
              }
              if (empty($img_url)) {
                  $img_url = 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg';
              }

              // Lấy nhãn phụ (Badge ưu đãi hoặc Vị trí tầng dịch vụ)
              $badge      = get_post_meta($post_id, '_dl_offer_badge', true);
              $offer_tag  = get_post_meta($post_id, '_dl_offer_tag', true);
              $location   = get_post_meta($post_id, '_dl_amenity_location', true);
              $price_text = get_post_meta($post_id, '_dl_offer_price_text', true);
              $top_badge  = $badge ?: ($offer_tag ?: $location);
      ?>
        <article class="bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 flex flex-col justify-between hover:shadow-2xl hover:border-gold transition-all duration-300">
          <div>
            <a href="<?php the_permalink(); ?>" class="block relative h-56 sm:h-60 overflow-hidden image-zoom-container">
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
              <?php if (!empty($top_badge)) : ?>
                <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[11px] font-bold bg-gold text-navy-deep uppercase tracking-wider shadow">
                  <?php echo esc_html($top_badge); ?>
                </span>
              <?php endif; ?>
              <?php if (!empty($price_text)) : ?>
                <span class="absolute bottom-3 right-3 px-3 py-1 rounded-full text-[11px] font-bold bg-navy-deep/85 text-amber-200 backdrop-blur-sm">
                  <?php echo esc_html($price_text); ?>
                </span>
              <?php endif; ?>
            </a>

            <div class="p-5 sm:p-6">
              <span class="text-[11px] text-slate-400 block mb-1.5"><?php echo get_the_date(); ?></span>
              <h2 class="font-display font-bold text-lg sm:text-xl text-navy-deep mb-2 leading-snug">
                <a href="<?php the_permalink(); ?>" class="hover:text-gold transition-colors"><?php the_title(); ?></a>
              </h2>
              <p class="text-[13px] sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                <?php
                $excerpt = get_the_excerpt();
                if (empty($excerpt)) {
                    $excerpt = wp_trim_words(wp_strip_all_tags(get_the_content()), 28);
                }
                echo esc_html($excerpt);
                ?>
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
      <?php
          endwhile;
      else :
      ?>
        <div class="col-span-full bg-white p-12 rounded-3xl text-center border border-slate-200/80 shadow-sm">
          <p class="text-lg font-bold text-navy-deep mb-2">Chưa có bài viết nào trong chuyên mục này</p>
          <p class="text-sm text-slate-500">Nội dung đang được cập nhật, Quý khách vui lòng quay lại sau.</p>
        </div>
      <?php
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
