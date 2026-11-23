<?php
/**
 * Template Name: Front Page Template
 * The front page template file for Dragon Legend Hotel
 * Chuẩn SEO Google & Tối ưu tốc độ tải trang Core Web Vitals
 *
 * @package DragonLegend
 * @version 1.2.0
 */

get_header();
?>

<main id="main" role="main">
  <!-- ========================================== -->
  <!-- 3. CINEMATIC HERO SECTION & BOOKING ENGINE -->
  <!-- ========================================== -->
  <section id="hero" class="relative min-h-[540px] lg:min-h-[calc(100vh-88px)] flex items-center justify-center py-10 sm:py-14 lg:py-16 px-4 overflow-hidden" aria-label="Giới thiệu mở đầu">
    <!-- Hero Background Visual with Parallax -->
    <!-- Hero Background Image with Ken Burns Zoom -->
    <div class="absolute inset-0 z-0">
      <img src="<?php echo esc_url(function_exists('dl_get_content') ? dl_get_content('hero_bg_image', 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg') : 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg'); ?>" 
           alt="<?php echo esc_attr(dragonlegend_get_info('name')); ?>" 
           width="1920" height="1080"
           fetchpriority="high"
           decoding="async"
           class="w-full h-full object-cover scale-105 filter brightness-90 animate-float" style="animation-duration: 25s;">
      <div class="hero-overlay absolute inset-0"></div>
    </div>

    <!-- Hero Content & Booking Bar -->
    <div class="relative z-10 max-w-5xl mx-auto w-full flex flex-col items-center text-center">
      <!-- Luxury Badge -->
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-gold/40 text-amber-200 text-[11px] font-semibold uppercase tracking-wider mb-3.5 sm:mb-4"
           data-aos="fade-down">
        <span class="text-gold">★ ★ ★ ★</span>
        <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('hero_badge_text', 'Khách Sạn Tân Cổ Điển 4 Sao Bãi Cháy') : 'Khách Sạn Tân Cổ Điển 4 Sao Bãi Cháy'); ?></span>
      </div>

      <!-- Main SEO H1 Headline -->
      <h1 class="font-display text-2xl sm:text-4xl md:text-[42px] lg:text-5xl font-bold text-white tracking-tight leading-[1.28] sm:leading-[1.22] mb-3.5 sm:mb-4 max-w-4xl"
          data-aos="fade-up" data-aos-delay="100">
        <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('hero_h1_line1', 'Dragon Legend Hotel') : 'Dragon Legend Hotel'); ?> <br class="hidden sm:inline">
        <span class="text-gold-shine"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('hero_h1_line2', 'Khách Sạn 4 Sao Bãi Cháy Hạ Long') : 'Khách Sạn 4 Sao Bãi Cháy Hạ Long'); ?></span>
      </h1>

      <!-- Subtitle -->
      <p class="text-[13.5px] sm:text-[15px] md:text-base text-[#fdfaf1]/90 font-normal max-w-2xl mb-5 sm:mb-7 leading-relaxed"
         data-aos="fade-up" data-aos-delay="200">
        <?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('hero_description', 'Khám phá không gian nghỉ dưỡng 138 phòng tiện nghi tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo trên đại lộ Hạ Long Marine.') : 'Khám phá không gian nghỉ dưỡng 138 phòng tiện nghi tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo trên đại lộ Hạ Long Marine.'); ?>
      </p>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-4 mb-6 sm:mb-9" data-aos="fade-up" data-aos-delay="300">
        <a href="<?php echo esc_url(function_exists('dl_get_content') ? dl_get_content('hero_btn1_url', '#rooms') : '#rooms'); ?>" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs sm:text-sm tracking-wider uppercase shadow-xl hover:brightness-110 gold-shimmer-btn transition-all">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('hero_btn1_text', 'Khám phá phòng nghỉ') : 'Khám phá phòng nghỉ'); ?>
        </a>
        <button type="button" class="open-vr-btn inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md text-[#fdfaf1] hover:text-gold border border-gold/40 hover:border-gold text-xs sm:text-sm font-semibold transition-all" aria-label="Mở tour khám phá thực tế ảo 360 độ">
          <svg class="w-4 h-4 text-gold animate-spin" style="animation-duration: 6s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('header_vr_text', 'Tour VR 360°') : 'Khám phá Tour VR 360°'); ?></span>
        </button>
      </div>

      <!-- ========================================== -->
      <!-- FLOATING QUICK BOOKING ENGINE               -->
      <!-- ========================================== -->
      <div class="w-full max-w-5xl" data-aos="zoom-in" data-aos-delay="400">
        <form id="hero-booking-form" class="glass-panel-light p-3 sm:p-4 rounded-2xl sm:rounded-3xl shadow-2xl border border-gold/40 text-left" aria-label="Công cụ tìm kiếm phòng nhanh">
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 items-stretch">
            
            <!-- 1. Check-in Date -->
            <div class="col-span-1 relative bg-white/95 rounded-xl sm:rounded-2xl p-2.5 sm:p-3 border border-amber-900/15 hover:border-gold transition-colors shadow-xs">
              <label for="hero-checkin" class="block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500 mb-0.5 truncate">
                Nhận phòng
              </label>
              <div class="flex items-center justify-between gap-1">
                <span id="hero-checkin-text" class="text-xs sm:text-sm font-bold text-navy-deep">24/10/2026</span>
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              <input type="date" id="hero-checkin" aria-label="Chọn ngày nhận phòng" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
            </div>

            <!-- 2. Check-out Date -->
            <div class="col-span-1 relative bg-white/95 rounded-xl sm:rounded-2xl p-2.5 sm:p-3 border border-amber-900/15 hover:border-gold transition-colors shadow-xs">
              <label for="hero-checkout" class="block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500 mb-0.5 truncate">
                Trả phòng <span id="hero-nights-text" class="text-gold font-normal">(1 đêm)</span>
              </label>
              <div class="flex items-center justify-between gap-1">
                <span id="hero-checkout-text" class="text-xs sm:text-sm font-bold text-navy-deep">25/10/2026</span>
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              <input type="date" id="hero-checkout" aria-label="Chọn ngày trả phòng" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
            </div>

            <!-- 3. Rooms & Guests Selector -->
            <div class="col-span-2 sm:col-span-1 relative">
              <button type="button" id="hero-guests-toggle" aria-expanded="false" aria-label="Chọn số phòng và số lượng khách" class="w-full h-full text-left bg-white/95 rounded-xl sm:rounded-2xl p-2.5 sm:p-3 border border-amber-900/15 hover:border-gold transition-colors shadow-xs">
                <span class="block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500 mb-0.5">
                  Số phòng & Số khách
                </span>
                <div class="flex items-center justify-between">
                  <span id="hero-guests-label" class="text-xs sm:text-sm font-bold text-navy-deep truncate">1 Phòng, 2 Khách</span>
                  <svg class="w-4 h-4 text-gold flex-shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </button>

              <!-- Guests Dropdown Popup -->
              <div id="hero-guests-dropdown" class="hidden absolute top-full left-0 right-0 sm:w-80 mt-2 p-4 rounded-2xl glass-dropdown z-30 border border-gold/30 shadow-2xl">
                <!-- Rooms Stepper -->
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                  <div>
                    <span class="text-sm font-bold text-slate-800 block">Số phòng</span>
                    <span class="text-xs text-slate-500">Tối đa 10 phòng</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button type="button" id="step-rooms-minus" aria-label="Giảm số phòng" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">-</button>
                    <span id="step-rooms-val" class="text-sm font-bold w-4 text-center">1</span>
                    <button type="button" id="step-rooms-plus" aria-label="Tăng số phòng" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">+</button>
                  </div>
                </div>

                <!-- Adults Stepper -->
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                  <div>
                    <span class="text-sm font-bold text-slate-800 block">Người lớn</span>
                    <span class="text-xs text-slate-500">Từ 12 tuổi trở lên</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button type="button" id="step-adults-minus" aria-label="Giảm số người lớn" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">-</button>
                    <span id="step-adults-val" class="text-sm font-bold w-4 text-center">2</span>
                    <button type="button" id="step-adults-plus" aria-label="Tăng số người lớn" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">+</button>
                  </div>
                </div>

                <!-- Children Stepper -->
                <div class="flex items-center justify-between py-2 mb-3">
                  <div>
                    <span class="text-sm font-bold text-slate-800 block">Trẻ em</span>
                    <span class="text-xs text-slate-500">Dưới 12 tuổi</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button type="button" id="step-kids-minus" aria-label="Giảm số trẻ em" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">-</button>
                    <span id="step-kids-val" class="text-sm font-bold w-4 text-center">0</span>
                    <button type="button" id="step-kids-plus" aria-label="Tăng số trẻ em" class="w-8 h-8 rounded-full border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-slate-100">+</button>
                  </div>
                </div>

                <button type="button" id="guests-done-btn" class="w-full py-2 bg-gold text-navy-deep text-xs font-bold rounded-xl hover:bg-gold-500 transition-colors">
                  Xong
                </button>
              </div>
            </div>

            <!-- 4. Submit Button -->
            <div class="col-span-2 sm:col-span-1 flex">
              <button type="submit" class="w-full h-full min-h-[46px] sm:min-h-[52px] py-3 sm:py-3.5 px-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs sm:text-sm uppercase tracking-wider hover:brightness-110 shadow-lg gold-shimmer-btn flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-navy-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span>Tìm phòng ngay</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 4. STATS COUNTER BAR                       -->
  <!-- ========================================== -->
  <!-- ========================================== -->
  <!-- 4. STATS COUNTER BAR                       -->
  <!-- ========================================== -->
  <section id="stats-section" class="bg-navy-deep text-white py-8 border-y border-gold/20 overflow-hidden" aria-label="Thành tựu và số liệu nổi bật">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
      <div class="space-y-1">
        <?php $s1 = function_exists('dl_get_content') ? dl_get_content('stats_num_1', '138') : '138'; ?>
        <span class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-gold stats-counter" data-target="<?php echo esc_attr($s1); ?>"><?php echo esc_html($s1); ?></span><span class="text-gold text-xl sm:text-2xl font-bold"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_unit_1', '+') : '+'); ?></span>
        <p class="text-[11px] sm:text-xs text-slate-300 font-medium"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_label_1', 'Phòng Nghỉ Tiện Nghi') : 'Phòng Nghỉ Tiện Nghi'); ?></p>
      </div>
      <div class="space-y-1">
        <?php $s2 = function_exists('dl_get_content') ? dl_get_content('stats_num_2', '4') : '4'; ?>
        <span class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-gold stats-counter" data-target="<?php echo esc_attr($s2); ?>"><?php echo esc_html($s2); ?></span><span class="text-gold text-xl sm:text-2xl font-bold"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_unit_2', '★') : '★'); ?></span>
        <p class="text-[11px] sm:text-xs text-slate-300 font-medium"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_label_2', 'Tiêu Chuẩn Quốc Tế') : 'Tiêu Chuẩn Quốc Tế'); ?></p>
      </div>
      <div class="space-y-1">
        <?php $s3 = function_exists('dl_get_content') ? dl_get_content('stats_num_3', '12') : '12'; ?>
        <span class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-gold stats-counter" data-target="<?php echo esc_attr($s3); ?>"><?php echo esc_html($s3); ?></span><span class="text-gold text-xl sm:text-2xl font-bold"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_unit_3', ' Tầng') : ' Tầng'); ?></span>
        <p class="text-[11px] sm:text-xs text-slate-300 font-medium"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_label_3', 'Hồ Bơi Vô Cực Trên Cao') : 'Hồ Bơi Vô Cực Trên Cao'); ?></p>
      </div>
      <div class="space-y-1">
        <?php $s4 = function_exists('dl_get_content') ? dl_get_content('stats_num_4', '100') : '100'; ?>
        <span class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-gold stats-counter" data-target="<?php echo esc_attr($s4); ?>"><?php echo esc_html($s4); ?></span><span class="text-gold text-xl sm:text-2xl font-bold"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_unit_4', '%') : '%'); ?></span>
        <p class="text-[11px] sm:text-xs text-slate-300 font-medium"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('stats_label_4', 'View Đại Lộ & Vịnh Biển') : 'View Đại Lộ & Vịnh Biển'); ?></p>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 5. ABOUT HOTEL SECTION                     -->
  <!-- ========================================== -->
  <section id="about" class="py-12 sm:py-16 px-4 max-w-7xl mx-auto overflow-hidden" aria-labelledby="about-heading">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
      
      <!-- Visual Collage -->
      <div class="lg:col-span-6 relative" data-aos="fade-right">
        <div class="relative z-10 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-4 border-white image-zoom-container">
          <img src="<?php echo esc_url(function_exists('dl_get_content') ? dl_get_content('about_image_main', 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg') : 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg'); ?>" 
               alt="Không gian sảnh Dragon Legend Hotel" 
               width="600" height="450" 
               loading="lazy"
               class="w-full h-[300px] sm:h-[380px] object-cover">
        </div>
        <div class="absolute -bottom-6 -right-3 sm:-right-6 z-20 w-40 sm:w-56 rounded-xl sm:rounded-2xl overflow-hidden shadow-2xl border-4 border-white image-zoom-container hidden sm:block">
          <img src="<?php echo esc_url(function_exists('dl_get_content') ? dl_get_content('about_image_sub', 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg') : 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg'); ?>" 
               alt="Phòng ngủ sang trọng" 
               width="300" height="200" 
               loading="lazy"
               class="w-full h-32 sm:h-36 object-cover">
        </div>
        <div class="absolute -top-6 -left-6 z-0 w-32 h-32 bg-amber-100/50 rounded-full blur-2xl hidden sm:block"></div>
      </div>

      <!-- Text Narrative -->
      <div class="lg:col-span-6 space-y-4 sm:space-y-5" data-aos="fade-left">
        <div>
          <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
            <span class="w-6 h-[2px] bg-gold inline-block"></span>
            <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_tagline', 'Về Chúng Tôi') : 'Về Chúng Tôi'); ?></span>
          </div>
          <h2 id="about-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28]">
            <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_title', 'Viên Ngọc Rực Rỡ Giữa Lòng Kỳ Quan Di Sản') : 'Viên Ngọc Rực Rỡ Giữa Lòng Kỳ Quan Di Sản'); ?>
          </h2>
        </div>

        <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
          <?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('about_p1', 'Tọa lạc tại vị trí đắc địa trên đại lộ Hạ Long Marine sầm uất thuộc trung tâm Bãi Cháy, <strong>Dragon Legend Hotel</strong> là điểm dừng chân hoàn hảo kết hợp nét trang nhã cổ điển châu Âu cùng sự năng động hiện đại của thành phố biển.') : 'Tọa lạc tại vị trí đắc địa trên đại lộ Hạ Long Marine sầm uất thuộc trung tâm Bãi Cháy, <strong>Dragon Legend Hotel</strong> là điểm dừng chân hoàn hảo kết hợp nét trang nhã cổ điển châu Âu cùng sự năng động hiện đại của thành phố biển.'); ?>
        </p>

        <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
          <?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('about_p2', 'Sở hữu 138 phòng nghỉ tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan thiên nhiên thế giới, trung tâm ẩm thực phong phú cùng đội ngũ nhân viên nhiệt thành, chúng tôi cam kết đem lại cho bạn kỳ nghỉ trọn vẹn và đáng nhớ nhất.') : 'Sở hữu 138 phòng nghỉ tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan thiên nhiên thế giới, trung tâm ẩm thực phong phú cùng đội ngũ nhân viên nhiệt thành, chúng tôi cam kết đem lại cho bạn kỳ nghỉ trọn vẹn và đáng nhớ nhất.'); ?>
        </p>

        <!-- Feature Badges -->
        <div class="grid grid-cols-2 gap-3 pt-1">
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-amber-50/70 border border-gold/20">
            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-gold flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
            </div>
            <span class="text-xs font-bold text-navy-deep"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_badge1_title', 'Vị Trí Đắc Địa Bãi Cháy') : 'Vị Trí Đắc Địa Bãi Cháy'); ?></span>
          </div>
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-amber-50/70 border border-gold/20">
            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-gold flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2"></path></svg>
            </div>
            <span class="text-xs font-bold text-navy-deep"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_badge2_title', 'Ẩm Thực Á - Âu Đặc Sắc') : 'Ẩm Thực Á - Âu Đặc Sắc'); ?></span>
          </div>
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-amber-50/70 border border-gold/20">
            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-gold flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
            </div>
            <span class="text-xs font-bold text-navy-deep"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_badge3_title', 'Sky Pool Tầng 12') : 'Sky Pool Tầng 12'); ?></span>
          </div>
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-amber-50/70 border border-gold/20">
            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-gold flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <span class="text-xs font-bold text-navy-deep"><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_badge4_title', 'An Ninh & Lễ Tân 24/7') : 'An Ninh & Lễ Tân 24/7'); ?></span>
          </div>
        </div>

        <div class="pt-3 flex items-center gap-4">
          <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="px-5 py-2.5 rounded-full bg-navy-deep text-white hover:bg-navy font-bold text-xs uppercase tracking-wider transition-colors">
            <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_btn_contact_text', 'Liên hệ đặt phòng') : 'Liên hệ đặt phòng'); ?>
          </a>
          <button type="button" class="open-vr-btn text-xs font-bold text-gold hover:text-navy-deep underline uppercase tracking-wider transition-colors">
            <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('about_btn_vr_text', 'Xem toàn cảnh 360° →') : 'Xem toàn cảnh 360° →'); ?>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 5b. POPULAR AMENITIES SHOWCASE             -->
  <!-- ========================================== -->
  <section id="popular-amenities" class="py-10 sm:py-12 bg-white border-b border-slate-200/80 overflow-hidden" aria-labelledby="amenities-popular-heading">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto mb-8" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_popular_tag', 'Trải Nghiệm Khách Sạn') : 'Trải Nghiệm Khách Sạn'); ?></span>
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
        </div>
        <h3 id="amenities-popular-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28]">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_popular_ttl', 'Tiện Nghi Nổi Bật Được Yêu Thích') : 'Tiện Nghi Nổi Bật Được Yêu Thích'); ?>
        </h3>
        <p class="text-[13.5px] sm:text-[15px] text-slate-500 mt-2 leading-relaxed">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_popular_dsc', 'Tận hưởng hệ sinh thái tiện ích đồng bộ, hoàn hảo từ những chi tiết nhỏ nhất nhằm mang đến sự thoải mái tối đa cho quý khách.') : 'Tận hưởng hệ sinh thái tiện ích đồng bộ, hoàn hảo từ những chi tiết nhỏ nhất nhằm mang đến sự thoải mái tối đa cho quý khách.'); ?>
        </p>
      </div>

      <!-- Popular Amenities Grid (Rendered via PHP & synchronized with JS) -->
      <div id="popular-amenities-container" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <?php
        $popular_amenities_list = function_exists('dl_get_popular_amenities') ? dl_get_popular_amenities() : array();
        if (!empty($popular_amenities_list)) :
          foreach ($popular_amenities_list as $p_index => $p_item) :
        ?>
        <div class="group bg-white p-3 sm:p-4 rounded-2xl border border-amber-900/10 shadow-sm hover:shadow-md hover:border-[#dfa843] transition-all duration-300 flex items-center gap-2.5 sm:gap-3.5"
             data-aos="fade-up" data-aos-delay="<?php echo esc_attr($p_index * 30); ?>">
          <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-[#9b671a] border border-amber-200/60 flex items-center justify-center flex-shrink-0 group-hover:bg-[#dfa843] group-hover:text-white transition-all duration-300 shadow-xs">
            <?php echo dl_get_amenity_svg($p_item['icon'], "w-4 h-4 sm:w-5 sm:h-5 text-[#9b671a] transition-colors group-hover:text-white"); ?>
          </div>
          <div class="min-w-0 flex-1">
            <h4 class="font-bold text-[12.5px] sm:text-sm text-[#320908] group-hover:text-[#dfa843] transition-colors leading-snug line-clamp-1">
              <?php echo esc_html($p_item['name']); ?>
            </h4>
            <p class="text-[11px] text-stone-500 truncate mt-0.5">
              <?php echo esc_html($p_item['desc']); ?>
            </p>
          </div>
        </div>
        <?php 
          endforeach;
        endif; 
        ?>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 6. LUXURY ROOMS & SUITES                   -->
  <!-- ========================================== -->
  <section id="rooms" class="py-12 sm:py-16 bg-cream-surface border-y border-gold/20 overflow-hidden" aria-labelledby="rooms-heading">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Section Header -->
      <div class="text-center max-w-4xl mx-auto mb-8 sm:mb-10" data-aos="fade-up">
        <div class="max-w-2xl mx-auto">
          <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
            <span class="w-6 h-[2px] bg-gold inline-block"></span>
            <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_rooms_tag', 'Không Gian Nghỉ Dưỡng') : 'Không Gian Nghỉ Dưỡng'); ?></span>
            <span class="w-6 h-[2px] bg-gold inline-block"></span>
          </div>
          <h2 id="rooms-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28] mb-2.5">
            <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_rooms_ttl', 'Kiệt Tác Phòng Nghỉ Hoàng Gia') : 'Kiệt Tác Phòng Nghỉ Hoàng Gia'); ?>
          </h2>
          <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
            <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_rooms_dsc', 'Mỗi căn phòng tại Dragon Legend Hotel là một tác phẩm kiến trúc tinh tế, kết hợp hài hòa giữa sự êm dịu hoàng gia và đầy đủ trang thiết bị hiện đại.') : 'Mỗi căn phòng tại Dragon Legend Hotel là một tác phẩm kiến trúc tinh tế, kết hợp hài hòa giữa sự êm dịu hoàng gia và đầy đủ trang thiết bị hiện đại.'); ?>
          </p>
        </div>

        <!-- Filter Tabs (Gọi động danh sách Loại Phòng từ Database: taxonomy dl_room_type) -->
        <?php
        $dl_room_categories = function_exists('dl_get_room_categories_for_frontend')
            ? dl_get_room_categories_for_frontend()
            : array();
        ?>
        <div class="flex flex-nowrap sm:flex-wrap items-center justify-start sm:justify-center gap-2 sm:gap-2.5 mt-6 max-w-6xl mx-auto overflow-x-auto sm:overflow-visible no-scrollbar py-2.5 px-3" role="tablist" aria-label="Bộ lọc hạng phòng">
          <button class="room-filter-btn whitespace-nowrap px-4 py-2 sm:px-5 sm:py-2.5 rounded-full text-xs font-bold transition-all active bg-gold text-navy-deep shadow-md flex-shrink-0" data-category="all" role="tab" aria-selected="true">
            Tất cả phòng
          </button>
          <?php if (!empty($dl_room_categories)) : ?>
            <?php foreach ($dl_room_categories as $room_cat) : ?>
              <button class="room-filter-btn whitespace-nowrap px-4 py-2 sm:px-5 sm:py-2.5 rounded-full text-xs font-bold transition-all bg-white text-slate-700 hover:bg-slate-100 shadow-xs flex-shrink-0"
                      data-category="<?php echo esc_attr($room_cat['slug']); ?>"
                      role="tab"
                      aria-selected="false">
                <?php echo esc_html($room_cat['name']); ?>
              </button>
            <?php endforeach; ?>
          <?php endif; ?>
          <button type="button" onclick="window.App && window.App.openCompareModal()" class="dl-compare-header-btn whitespace-nowrap px-4 py-2 sm:px-5 sm:py-2.5 rounded-full text-xs font-bold transition-all bg-navy-deep text-gold border border-gold/40 hover:bg-navy shadow-sm flex-shrink-0 inline-flex items-center gap-1.5" title="So sánh chi tiết các hạng phòng">
            <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>So sánh phòng</span>
            <span class="dl-compare-count-badge hidden" data-compare-count>0</span>
          </button>
        </div>
      </div>

      <!-- Rooms Slider Carousel -->
      <div class="relative group/rooms-slider px-0 sm:px-1">
        <!-- Floating Prev Button -->
        <button class="rooms-prev swiper-button-prev-custom hidden sm:flex absolute -left-3 sm:-left-5 lg:-left-6 top-[40%] -translate-y-1/2 z-20" aria-label="Phòng trước">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        <!-- Floating Next Button -->
        <button class="rooms-next swiper-button-next-custom hidden sm:flex absolute -right-3 sm:-right-5 lg:-right-6 top-[40%] -translate-y-1/2 z-20" aria-label="Phòng tiếp theo">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <!-- Swiper Track -->
        <div class="swiper rooms-swiper pb-12 pt-2" data-aos="fade-up" data-aos-delay="100">
          <div id="rooms-grid" class="swiper-wrapper"></div>
          <!-- Pagination Bullets -->
          <div class="rooms-pagination swiper-pagination"></div>
        </div>
      </div>

      <!-- Banner Callout for Booking -->
      <div class="mt-10 sm:mt-12 p-5 sm:p-6 rounded-2xl sm:rounded-3xl bg-navy-deep text-white flex flex-col md:flex-row items-center justify-between gap-5 shadow-xl border border-gold/30"
           data-aos="fade-up">
        <div class="space-y-1 text-center md:text-left">
          <h3 class="font-display text-lg sm:text-xl font-bold text-gold">Đặt Trực Tiếp - Luôn Nhận Giá Tốt Nhất</h3>
          <p class="text-xs sm:text-sm text-slate-300">Tặng kèm buffet sáng, miễn phí hồ bơi vô cực & đồ uống chào mừng.</p>
        </div>
        <div class="flex items-center gap-4 flex-shrink-0">
          <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal()" 
                  class="px-5 py-2.5 sm:px-6 sm:py-3 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider hover:brightness-110 shadow-lg">
            Kiểm tra phòng trống
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 7. SPECIAL OFFERS & PACKAGES               -->
  <!-- ========================================== -->
  <section id="offers" class="py-12 sm:py-16 px-4 max-w-7xl mx-auto overflow-hidden" aria-labelledby="offers-heading">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10" data-aos="fade-up">
      <div>
        <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_offers_tag', 'Đặc Quyền Nghỉ Dưỡng') : 'Đặc Quyền Nghỉ Dưỡng'); ?></span>
        </div>
        <h2 id="offers-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28]">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_offers_ttl', 'Ưu Đãi & Combo Trải Nghiệm Độc Quyền') : 'Ưu Đãi & Combo Trải Nghiệm Độc Quyền'); ?>
        </h2>
      </div>

      <!-- Navigation Arrows -->
      <div class="flex items-center gap-3 mt-4 md:mt-0">
        <button class="offers-prev swiper-button-prev-custom" aria-label="Ưu đãi trước">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="offers-next swiper-button-next-custom" aria-label="Ưu đãi tiếp theo">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>

    <!-- Offers Swiper -->
    <div class="swiper offers-swiper pb-10" data-aos="fade-up" data-aos-delay="100">
      <div id="offers-container" class="swiper-wrapper">
        <?php
        $offers_query = new WP_Query(array(
            'post_type'      => 'dl_offer',
            'posts_per_page' => 6,
            'post_status'    => 'publish'
        ));

        if ($offers_query->have_posts()) :
            while ($offers_query->have_posts()) : $offers_query->the_post();
                $post_id   = get_the_ID();
                $tag       = get_post_meta($post_id, '_dl_offer_tag', true);
                $badge     = get_post_meta($post_id, '_dl_offer_badge', true);
                $price_txt = get_post_meta($post_id, '_dl_offer_price_text', true);
                $expiry    = get_post_meta($post_id, '_dl_offer_expiry', true);
                $benefits  = get_post_meta($post_id, '_dl_offer_benefits', true);
                $benefits_arr = !empty($benefits) ? explode("\n", trim($benefits)) : array();

                $img_url   = get_the_post_thumbnail_url($post_id, 'large');
                if (!$img_url) {
                    $img_url = get_post_meta($post_id, '_dl_offer_sample_image', true);
                }
                if (!$img_url) {
                    $img_url = 'https://media.dragonlegendhotel.com/2026/02/fsdffsdfsd-658x492.jpg';
                }
        ?>
          <div class="swiper-slide h-auto">
            <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 flex flex-col h-full hover:shadow-2xl hover:border-gold transition-all duration-300">
              <div class="relative h-56 image-zoom-container">
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                <?php if ($badge) : ?>
                  <span class="absolute top-4 left-4 px-3 py-1 bg-rose-600 text-white text-[11px] font-bold rounded-full uppercase tracking-wider shadow">
                    <?php echo esc_html($badge); ?>
                  </span>
                <?php endif; ?>
                <?php if ($tag) : ?>
                  <span class="absolute bottom-4 right-4 px-3 py-1 bg-navy-deep/80 backdrop-blur-md text-amber-200 text-[10px] font-semibold rounded-full uppercase">
                    <?php echo esc_html($tag); ?>
                  </span>
                <?php endif; ?>
              </div>
              <div class="p-6 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-display font-bold text-xl text-navy-deep mb-2">
                    <a href="<?php the_permalink(); ?>" class="hover:text-gold transition-colors"><?php the_title(); ?></a>
                  </h3>
                  <p class="text-xs text-slate-500 mb-4 line-clamp-2"><?php echo get_the_excerpt(); ?></p>
                  
                  <?php if (!empty($benefits_arr)) : ?>
                    <ul class="space-y-1.5 mb-6 text-xs text-slate-600">
                      <?php foreach (array_slice($benefits_arr, 0, 4) as $ben) : ?>
                        <li class="flex items-center gap-2">
                          <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                          <span><?php echo esc_html(trim($ben)); ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                  <div>
                    <span class="text-[10px] text-slate-400 block"><?php echo esc_html($expiry); ?></span>
                    <span class="text-sm font-bold text-gold"><?php echo esc_html($price_txt); ?></span>
                  </div>
                  <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal()" class="px-4 py-2 bg-navy-deep hover:bg-gold hover:text-navy-deep text-white font-bold text-xs rounded-xl transition-all">
                    Nhận ưu đãi
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
      </div>
      <div class="offers-pagination swiper-pagination !bottom-0"></div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 8. 4-STAR AMENITIES & EXPERIENCES          -->
  <!-- ========================================== -->
  <section id="amenities" class="py-12 sm:py-16 bg-navy-deep text-white border-y border-gold/20 overflow-hidden" aria-labelledby="amenities-heading">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_tag', 'Trải nghiệm trọn vẹn') : 'Trải nghiệm trọn vẹn'); ?></span>
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
        </div>
        <h2 id="amenities-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold leading-[1.28] mb-2.5">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_ttl', 'Dịch Vụ & Tiện Ích Đỉnh Cao') : 'Dịch Vụ & Tiện Ích Đỉnh Cao'); ?>
        </h2>
        <p class="text-[13.5px] sm:text-[15px] text-slate-300 leading-relaxed">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_amenities_dsc', 'Tận hưởng những giây phút thư giãn bất tận với hệ thống tiện ích đẳng cấp từ tầng 12 đến các sảnh hội nghị sang trọng.') : 'Tận hưởng những giây phút thư giãn bất tận với hệ thống tiện ích đẳng cấp từ tầng 12 đến các sảnh hội nghị sang trọng.'); ?>
        </p>
      </div>

      <!-- Amenities Grid (Rendered via PHP or js/main.js fallback) -->
      <div id="amenities-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php
        $amenities_cat_id = function_exists('dragonlegend_get_amenities_category_id') ? dragonlegend_get_amenities_category_id() : 0;
        
        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
        );

        if ($amenities_cat_id > 0) {
            $query_args['cat'] = $amenities_cat_id;
        }

        $amenities_query = new WP_Query($query_args);

        // Fallback: nếu chưa có bài viết nào trong Category chỉ định, query dl_amenity để không bị trống trang
        if (!$amenities_query->have_posts()) {
            $amenities_query = new WP_Query(array(
                'post_type'      => 'dl_amenity',
                'posts_per_page' => 4,
                'post_status'    => 'publish',
            ));
        }

        if ($amenities_query->have_posts()) :
            while ($amenities_query->have_posts()) : $amenities_query->the_post();
                $post_id    = get_the_ID();
                $location   = get_post_meta($post_id, '_dl_amenity_location', true);
                if (!$location) {
                    $location = 'Dịch Vụ Đẳng Cấp';
                }
                $highlights = get_post_meta($post_id, '_dl_amenity_highlights', true);
                $hl_arr     = !empty($highlights) ? explode("\n", trim($highlights)) : array();

                $img_url    = get_the_post_thumbnail_url($post_id, 'large');
                if (!$img_url) {
                    $img_url = get_post_meta($post_id, '_dl_amenity_sample_image', true);
                }
                if (!$img_url) {
                    $img_url = 'https://media.dragonlegendhotel.com/2015/07/DJI_0081-HDR.jpg';
                }
                $permalink  = get_permalink($post_id);
        ?>
          <div class="group relative rounded-2xl sm:rounded-3xl overflow-hidden bg-navy-surface border border-gold/30 shadow-xl transition-all duration-300 hover:border-gold hover:shadow-2xl flex flex-col justify-between" data-aos="fade-up">
            <a href="<?php echo esc_url($permalink); ?>" class="block h-52 sm:h-60 image-zoom-container overflow-hidden relative">
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover filter brightness-90 group-hover:scale-105 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-navy-deep/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
            </a>
            <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
              <div>
                <span class="text-xs text-gold font-bold uppercase tracking-wider block mb-1"><?php echo esc_html($location); ?></span>
                <h3 class="font-display text-xl sm:text-2xl font-bold text-white mb-2 group-hover:text-gold transition-colors">
                  <a href="<?php echo esc_url($permalink); ?>">
                    <?php the_title(); ?>
                  </a>
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 mb-4 leading-relaxed">
                  <?php 
                  $excerpt = get_the_excerpt();
                  if (empty($excerpt)) {
                      $excerpt = wp_strip_all_tags(get_the_content());
                  }
                  echo esc_html(wp_trim_words($excerpt, 30, '...')); 
                  ?>
                </p>
              </div>
              <?php if (!empty($hl_arr)) : ?>
                <div class="flex flex-wrap gap-2 mt-auto pt-2">
                  <?php foreach ($hl_arr as $hl) : ?>
                    <?php $clean_hl = trim($hl); if (empty($clean_hl)) continue; ?>
                    <span class="px-2.5 py-1 rounded-full bg-white/10 text-amber-200 text-[11px] border border-white/10 flex items-center gap-1">
                      <span class="text-gold">★</span> <?php echo esc_html(ltrim($clean_hl, "★\t ")); ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 9. GUEST TESTIMONIALS                      -->
  <!-- ========================================== -->
  <section id="reviews" class="py-12 sm:py-16 px-4 max-w-7xl mx-auto overflow-hidden" aria-labelledby="reviews-heading">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10" data-aos="fade-up">
      <div>
        <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
          <span class="w-6 h-[2px] bg-gold inline-block"></span>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_reviews_tag', 'Khách hàng nói gì về chúng tôi') : 'Khách hàng nói gì về chúng tôi'); ?></span>
        </div>
        <h2 id="reviews-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28]">
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_reviews_ttl', 'Trải Nghiệm Thực Tế Từ Du Khách') : 'Trải Nghiệm Thực Tế Từ Du Khách'); ?>
        </h2>
      </div>

      <!-- Navigation Arrows -->
      <div class="flex items-center gap-3 mt-4 md:mt-0">
        <button class="testimonials-prev swiper-button-prev-custom" aria-label="Đánh giá trước">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="testimonials-next swiper-button-next-custom" aria-label="Đánh giá tiếp theo">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>

    <!-- Testimonials Swiper -->
    <div class="swiper testimonials-swiper pb-10" data-aos="fade-up" data-aos-delay="100">
      <div id="testimonials-container" class="swiper-wrapper">
        <?php
        $testi_query = new WP_Query(array(
            'post_type'      => 'dl_testimonial',
            'posts_per_page' => 6,
            'post_status'    => 'publish'
        ));

        if ($testi_query->have_posts()) :
            while ($testi_query->have_posts()) : $testi_query->the_post();
                $post_id  = get_the_ID();
                $location = get_post_meta($post_id, '_dl_testimonial_location', true);
                $rating   = intval(get_post_meta($post_id, '_dl_testimonial_rating', true)) ?: 5;
                $date     = get_post_meta($post_id, '_dl_testimonial_date', true);
                $quote    = get_post_meta($post_id, '_dl_testimonial_title', true);

                $avatar   = get_the_post_thumbnail_url($post_id, 'thumbnail');
                if (!$avatar) {
                    $avatar = get_post_meta($post_id, '_dl_testimonial_sample_avatar', true);
                }
                if (!$avatar) {
                    $avatar = 'https://media.dragonlegendhotel.com/2026/02/408634589_10226615376023160_1198021278240416737_n.jpg';
                }
        ?>
          <div class="swiper-slide h-auto">
            <div class="bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-lg border border-slate-200/80 flex flex-col justify-between h-full hover:shadow-xl transition-shadow">
              <div class="space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between">
                  <div class="text-amber-400 text-sm">
                    <?php echo str_repeat('★', $rating); ?>
                  </div>
                  <span class="text-[11px] text-slate-400"><?php echo esc_html($date); ?></span>
                </div>
                <?php if ($quote) : ?>
                  <h4 class="font-display font-bold text-base text-navy-deep">"<?php echo esc_html($quote); ?>"</h4>
                <?php endif; ?>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic">
                  <?php echo get_the_content(); ?>
                </p>
              </div>

              <div class="flex items-center gap-3 pt-4 sm:pt-6 mt-4 sm:mt-6 border-t border-slate-100">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php the_title_attribute(); ?>" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover border-2 border-gold">
                <div>
                  <h5 class="font-bold text-sm text-navy-deep"><?php the_title(); ?></h5>
                  <span class="text-xs text-slate-500"><?php echo esc_html($location); ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
      </div>
      <div class="testimonials-pagination swiper-pagination !bottom-0"></div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 10. LOCATION & CONTACT                     -->
  <!-- ========================================== -->
  <section id="contact" class="py-12 sm:py-16 bg-cream-surface border-t border-gold/20 overflow-hidden" aria-labelledby="contact-heading">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
        
        <!-- Info Column -->
        <div class="lg:col-span-5 space-y-5 sm:space-y-6" data-aos="fade-right">
          <div>
            <div class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest mb-1.5">
              <span class="w-6 h-[2px] bg-gold inline-block"></span>
              <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_contact_tag', 'Điểm Đến Lý Tưởng') : 'Điểm Đến Lý Tưởng'); ?></span>
            </div>
            <h2 id="contact-heading" class="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-navy-deep leading-[1.28]">
              <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('sec_contact_ttl', 'Vị Trí Trung Tâm & Kết Nối Thuận Tiện') : 'Vị Trí Trung Tâm & Kết Nối Thuận Tiện'); ?>
            </h2>
          </div>

          <p class="text-[13.5px] sm:text-[15px] text-slate-600 leading-relaxed">
            <?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('sec_contact_dsc', 'Nằm trên đại lộ sầm uất nhất khu du lịch Bãi Cháy, Dragon Legend Hotel giúp bạn dễ dàng di chuyển tới các điểm vui chơi, bãi tắm và cảng du thuyền quốc tế trong bán kính chưa đầy 5-10 phút.') : 'Nằm trên đại lộ sầm uất nhất khu du lịch Bãi Cháy, Dragon Legend Hotel giúp bạn dễ dàng di chuyển tới các điểm vui chơi, bãi tắm và cảng du thuyền quốc tế trong bán kính chưa đầy 5-10 phút.'); ?>
          </p>

          <!-- Contact items -->
          <div class="space-y-4 pt-2 text-sm">
            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-xl bg-amber-50 text-gold border border-gold/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
              </div>
              <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Địa chỉ khách sạn</span>
                <span class="font-medium text-navy-deep"><?php echo esc_html(dragonlegend_get_info('address')); ?></span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-xl bg-amber-50 text-gold border border-gold/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
              </div>
              <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Hotline đặt phòng 24/7</span>
                <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="font-bold text-gold hover:underline text-base"><?php echo esc_html(dragonlegend_get_info('hotline')); ?></a>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-xl bg-amber-50 text-gold border border-gold/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Email liên hệ</span>
                <a href="mailto:<?php echo esc_attr(dragonlegend_get_info('email')); ?>" class="font-medium text-navy-deep hover:text-gold"><?php echo esc_html(dragonlegend_get_info('email')); ?></a>
              </div>
            </div>
          </div>

          <!-- Attractions Distance Checklist -->
          <div class="pt-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block mb-3">Khoảng cách tới các địa điểm nổi tiếng:</span>
            <div id="attractions-list" class="space-y-2.5"></div>
          </div>
        </div>

        <!-- Google Maps Embed -->
        <div class="lg:col-span-7" data-aos="fade-left">
          <div class="rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-full min-h-[300px] sm:min-h-[420px] bg-slate-200">
            <iframe src="<?php echo esc_url(dragonlegend_get_info('map_embed')); ?>" 
                    width="100%" height="100%" style="border:0; min-height: 300px;" 
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    title="Bản đồ chỉ dẫn đến khách sạn Dragon Legend Hotel"></iframe>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<?php
get_footer();
