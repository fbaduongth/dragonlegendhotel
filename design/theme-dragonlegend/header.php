<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <link rel="profile" href="https://gmpg.org/xfn/11">

  <!-- Google SEO Directives -->
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="author" content="<?php echo esc_attr(dragonlegend_get_info('name')); ?>">
  <link rel="canonical" href="<?php echo esc_url(is_front_page() ? home_url('/') : get_permalink()); ?>">

  <!-- Open Graph / Social Media Meta Tags -->
  <meta property="og:locale" content="vi_VN">
  <meta property="og:type" content="hotel">
  <meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
  <meta property="og:description" content="Khách sạn 4 sao Dragon Legend Hotel toạ lạc tại Đại Lộ Hạ Long Marine Bãi Cháy với 138 phòng nghỉ tân cổ điển sang trọng, hồ bơi vô cực ngắm trọn kỳ quan vịnh Hạ Long.">
  <meta property="og:url" content="<?php echo esc_url(is_front_page() ? home_url('/') : get_permalink()); ?>">
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
  <meta property="og:image" content="https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
  <meta name="twitter:description" content="Khách sạn 4 sao Dragon Legend Hotel toạ lạc tại Đại Lộ Hạ Long Marine Bãi Cháy với 138 phòng nghỉ tân cổ điển sang trọng, hồ bơi vô cực ngắm trọn kỳ quan vịnh Hạ Long.">
  <meta name="twitter:image" content="https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg">

  <!-- ========================================== -->
  <!-- TAILWIND CSS CDN & CONFIGURATION           -->
  <!-- ========================================== -->
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            gold: {
              DEFAULT: '#dfa843',
              50: '#fefcf6',
              100: '#fdf7e7',
              200: '#faecc6',
              300: '#f5dc9b',
              400: '#ecc76f',
              500: '#dfa843',
              600: '#c88d2b',
              700: '#a36c1e',
              800: '#7d4f16',
              900: '#55330e'
            },
            navy: {
              DEFAULT: '#48100f',
              deep: '#320908',
              surface: '#5a1716',
              card: '#6b1d1c'
            },
            maroon: {
              DEFAULT: '#48100f',
              deep: '#320908',
              surface: '#5a1716',
              card: '#6b1d1c'
            },
            cream: {
              DEFAULT: '#fdfaf1',
              surface: '#f6eed6',
              muted: '#eadcb7'
            }
          },
          fontFamily: {
            sans: ['"Montserrat"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
            display: ['"Montserrat"', 'sans-serif'],
            accent: ['"Montserrat"', 'sans-serif'],
            menu: ['"Montserrat"', 'sans-serif']
          }
        }
      }
    };
  </script>

  <!-- ========================================== -->
  <!-- SCHEMA.ORG STRUCTURED DATA (JSON-LD)       -->
  <!-- ========================================== -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Hotel",
    "@id": "<?php echo esc_url(home_url('/#hotel')); ?>",
    "name": "<?php echo esc_attr(dragonlegend_get_info('name')); ?>",
    "alternateName": "Khách Sạn Dragon Legend Bãi Cháy",
    "url": "<?php echo esc_url(home_url('/')); ?>",
    "logo": "https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png",
    "image": [
      "https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg"
    ],
    "description": "Khách sạn 4 sao toạ lạc tại Đại Lộ Hạ Long Marine Bãi Cháy với phong cách tân cổ điển, phòng nghỉ sang trọng giữa lòng kỳ quan thiên nhiên thế giới Vịnh Hạ Long.",
    "starRating": {
      "@type": "Rating",
      "ratingValue": "4",
      "bestRating": "5"
    },
    "priceRange": "1.050.000 VNĐ - 3.500.000 VNĐ",
    "currenciesAccepted": "VND, USD",
    "paymentAccepted": "Cash, Credit Card, Bank Transfer",
    "telephone": "<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>",
    "email": "<?php echo esc_attr(dragonlegend_get_info('email')); ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Đại lộ Hạ Long Marine",
      "addressLocality": "Phường Bãi Cháy",
      "addressRegion": "Tỉnh Quảng Ninh",
      "postalCode": "200000",
      "addressCountry": "VN"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": <?php echo esc_attr(dragonlegend_get_info('latitude')); ?>,
      "longitude": <?php echo esc_attr(dragonlegend_get_info('longitude')); ?>
    },
    "hasMap": "https://maps.google.com/?cid=0x314a598bb174745d:0x207cc82c86dc2d25",
    "checkinTime": "14:00",
    "checkoutTime": "12:00",
    "numberOfRooms": 138,
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.8",
      "reviewCount": "142",
      "bestRating": "5",
      "worstRating": "1"
    }
  }
  <!-- Favicon & Touch Icons -->
  <link rel="icon" type="image/x-icon" href="<?php echo esc_url(home_url('/favicon.ico')); ?>">
  <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/site-icon-180x180.png'); ?>">

  <?php wp_head(); ?>
</head>
<body <?php body_class('bg-cream text-slate-800 antialiased selection:bg-gold selection:text-white'); ?>>
<?php wp_body_open(); ?>

  <!-- ========================================== -->
  <!-- 1. TOP LUXURY NOTIFICATION BAR              -->
  <!-- ========================================== -->
  <aside class="bg-navy-deep text-slate-300 text-[11px] sm:text-xs py-2 border-b border-white/10 relative z-40 hidden sm:block" aria-label="Thông báo ưu đãi">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-1.5 text-gold font-medium">
          <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.784 1.399 8.196-7.333-3.856-7.333 3.856 1.399-8.196-5.934-5.784 8.2-1.192zm0 5.702l-2.232 4.522-4.991.725 3.612 3.521-.852 4.988 4.463-2.347 4.463 2.347-.852-4.988 3.612-3.521-4.991-.725z"/></svg>
          <?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('topbar_badge', 'Đặt trực tiếp - Luôn cam kết giá tốt nhất') : 'Đặt trực tiếp - Luôn cam kết giá tốt nhất'); ?>
        </span>
        <span class="hidden md:inline text-slate-500">|</span>
        <span class="hidden md:inline text-slate-300"><?php echo wp_kses_post(function_exists('dl_get_content') ? dl_get_content('topbar_promo', 'Nhập mã <strong class="text-amber-300 font-semibold">DRAGON2026</strong> giảm ngay 10%') : 'Nhập mã <strong class="text-amber-300 font-semibold">DRAGON2026</strong> giảm ngay 10%'); ?></span>
      </div>
      <div class="flex items-center gap-4 text-[11px]">
        <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="flex items-center gap-1.5 hover:text-gold transition-colors font-medium">
          <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
          <span><?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
        </a>
        <a href="<?php echo esc_url(dragonlegend_get_info('vr_url')); ?>" target="_blank" rel="noopener" class="open-vr-btn hidden lg:inline-flex items-center gap-1.5 text-amber-300 hover:text-white transition-colors cursor-pointer" aria-label="Khám phá Tour VR 360 độ">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('header_vr_text', 'Tour VR 360°') : 'Tour VR 360°'); ?></span>
        </a>

        <!-- Ngôn ngữ tự động (Phía sau Tour VR 360°) -->
        <?php
        if (function_exists('dl_render_language_switcher')) {
            dl_render_language_switcher();
        } else {
            do_action('dragonlegend_after_vr_tour');
        }
        ?>
      </div>
    </div>
  </aside>

  <!-- ========================================== -->
  <!-- 2. STICKY MAIN NAVIGATION                   -->
  <!-- ========================================== -->
  <header id="main-navbar" class="sticky top-0 z-40 transition-all duration-300 py-2.5 sm:py-3 bg-navy-deep/95 backdrop-blur-md border-b border-white/10 shadow-md" role="banner">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
      
      <!-- Brand Logo -->
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center group focus:outline-none flex-shrink-0" aria-label="Trang chủ Dragon Legend Hotel">
        <?php 
        $custom_header_logo = function_exists('dl_get_content') ? dl_get_content('header_logo_url', '') : '';
        if (!empty($custom_header_logo)) : 
        ?>
          <img src="<?php echo esc_url($custom_header_logo); ?>" 
               alt="<?php echo esc_attr(dragonlegend_get_info('name')); ?>" 
               class="h-9 sm:h-11 lg:h-12 w-auto max-h-12 max-w-[200px] sm:max-w-[240px] lg:max-w-[280px] object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow">
        <?php elseif (has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" 
               alt="<?php echo esc_attr(dragonlegend_get_info('name')); ?>" 
               class="h-9 sm:h-11 lg:h-12 w-auto max-h-12 max-w-[200px] sm:max-w-[240px] lg:max-w-[280px] object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow">
        <?php endif; ?>
      </a>

      <!-- Desktop Navigation Menu -->
      <nav class="hidden lg:flex items-center gap-3.5 xl:gap-5 2xl:gap-6 text-[11px] xl:text-xs font-semibold uppercase tracking-wider text-slate-200 whitespace-nowrap" aria-label="Menu chính">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'walker'         => new DragonLegend_Desktop_Nav_Walker(),
            'fallback_cb'    => 'dragonlegend_default_desktop_nav',
        ));
        ?>
      </nav>

      <!-- Right Header CTAs -->
      <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
        <!-- Hotline Call (Hidden on small laptops/tablets to keep menu perfectly spacious) -->
        <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="hidden 2xl:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md text-amber-200 border border-gold/30 text-xs font-bold transition-all whitespace-nowrap">
          <svg class="w-3.5 h-3.5 text-gold animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
          <span><?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
        </a>

        <!-- Primary Booking Button -->
        <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal()" 
                class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-full bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow-lg hover:shadow-gold/40 hover:brightness-110 transition-all gold-shimmer-btn flex items-center gap-1.5 whitespace-nowrap">
          <span><?php echo esc_html(function_exists('dl_get_content') ? dl_get_content('header_book_btn_text', 'Đặt phòng ngay') : 'Đặt phòng ngay'); ?></span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>

        <!-- Mobile Drawer Toggle -->
        <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 focus:outline-none flex items-center justify-center" aria-label="Mở menu điều hướng di động">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
      </div>
    </div>
  </header>

  <!-- ========================================== -->
  <!-- MOBILE DRAWER NAVIGATION                   -->
  <!-- ========================================== -->
  <div id="drawer-overlay" class="fixed inset-0 bg-navy-deep/80 backdrop-blur-sm z-50 hidden transition-opacity"></div>
  
  <div id="mobile-drawer" class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-navy-deep text-white z-50 p-6 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 shadow-2xl border-r border-gold/30">
    <div>
      <div class="flex items-center justify-between pb-6 border-b border-white/10">
        <div class="flex items-center">
          <?php 
          $drawer_logo = function_exists('dl_get_content') ? dl_get_content('header_logo_url', '') : '';
          if (empty($drawer_logo)) {
              $drawer_logo = 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';
          }
          ?>
          <img src="<?php echo esc_url($drawer_logo); ?>" alt="<?php echo esc_attr(dragonlegend_get_info('name')); ?>" class="h-10 w-auto max-w-[200px] object-contain">
        </div>
        <button id="close-drawer-btn" class="p-2 text-slate-400 hover:text-white rounded-lg focus:outline-none" aria-label="Đóng menu">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Drawer Nav Links -->
      <nav class="py-6 flex flex-col gap-4 text-sm font-semibold tracking-wider uppercase">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'walker'         => new DragonLegend_Mobile_Nav_Walker(),
            'fallback_cb'    => 'dragonlegend_default_mobile_nav',
        ));
        ?>
      </nav>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="pt-6 border-t border-white/10 space-y-3">
      <!-- Ngôn ngữ trên thiết bị di động -->
      <div class="mb-2">
        <p class="text-[10px] text-slate-400 mb-1.5 uppercase tracking-wider font-semibold">Ngôn ngữ / Language</p>
        <?php
        if (function_exists('dl_render_language_switcher_mobile')) {
            dl_render_language_switcher_mobile();
        } else {
            do_action('dragonlegend_mobile_language_switcher');
        }
        ?>
      </div>

      <!-- Nút Khám phá VR 360 trên Mobile Drawer -->
      <button type="button" class="open-vr-btn w-full py-3 rounded-xl bg-navy-surface hover:bg-navy-surface/80 border border-gold/40 text-amber-200 flex items-center justify-center gap-2 text-xs font-bold transition-all shadow-md">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <svg class="w-4 h-4 text-gold animate-spin" style="animation-duration: 6s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
        <span>Khám phá Tour VR 360°</span>
      </button>

      <a href="tel:<?php echo esc_attr(dragonlegend_get_info('hotline_tel')); ?>" class="w-full py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center gap-2 text-xs font-bold transition-colors">
        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
        <span>Hotline: <?php echo esc_html(dragonlegend_get_info('hotline')); ?></span>
      </a>
      <button onclick="window.BookingEngine && window.BookingEngine.openBookingModal()" class="w-full py-3 rounded-xl bg-gradient-to-r from-gold to-gold-500 text-navy-deep font-bold text-xs uppercase tracking-wider shadow-lg hover:brightness-110 transition-all text-center">
        Đặt phòng trực tiếp
      </button>
    </div>
  </div>

  <!-- Site Content Wrapper - wraps main content and footer -->
  <div id="site-wrapper" class="w-full relative">
