<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();
?>

<main id="main" class="pt-36 pb-24 bg-cream text-center px-4" role="main">
  <div class="max-w-md mx-auto space-y-6">
    <span class="font-display text-8xl font-bold text-gold block">404</span>
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy-deep">Không Tìm Thấy Trang</h1>
    <p class="text-sm text-slate-600">Trang bạn yêu cầu không tồn tại hoặc đã được di chuyển. Quý khách vui lòng trở về trang chủ để tiếp tục khám phá.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block px-8 py-3.5 bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider rounded-full shadow-lg hover:brightness-110 transition-all gold-shimmer-btn">
      Về trang chủ
    </a>
  </div>
</main>

<?php
get_footer();
