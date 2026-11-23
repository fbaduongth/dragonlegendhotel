/**
 * Dragon Legend Hotel - Main UI Controller
 * Xử lý giao diện, hiệu ứng animation, rendering dữ liệu và tương tác
 */

document.addEventListener('DOMContentLoaded', () => {
  App.init();
});

const App = {
  currentCategory: 'all',

  init() {
    this.initNavbar();
    this.renderPopularAmenities();
    this.renderRooms();
    this.renderOffers();
    this.renderAmenities();
    this.renderTestimonials();
    this.renderAttractions();
    this.setupRoomTabs();
    this.setupRoomDetailModal();
    this.setupVRModal();
    this.setupBackToTop();
    this.setupStatsCounter();
    this.initSwiperCarousels();
    this.initAOS();

    // Khởi động Booking Engine
    if (window.BookingEngine) {
      window.BookingEngine.init();
    }
  },

  formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
  },

  getAmenitySvg(iconName, customClass = "w-5 h-5 text-[#c5a880]") {
    const icons = {
      'shuttle': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>`,
      'no-smoking': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="2" x2="22" y2="22"/><path d="M18 12H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/><path d="M22 12v4"/><path d="M18 8c0-2.5-2-2.5-2-5"/><path d="M22 8c0-2.5-2-2.5-2-5"/></svg>`,
      'wifi': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13a10 10 0 0 1 14 0"/><path d="M8.5 16.5a5 5 0 0 1 7 0"/><path d="M2 8.82a15 15 0 0 1 20 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>`,
      'concierge': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><circle cx="12" cy="2" r="1"/></svg>`,
      'family': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
      'restaurant': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/></svg>`,
      'reception': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
      'elevator': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 10 3-3 3 3"/><path d="m9 14 3 3 3-3"/></svg>`,
      'snowflake': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>`,
      'coffee': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>`,
      'pool': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="M2 22c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="m7 12 3-3 2.5 2.5"/><circle cx="17" cy="5" r="2"/><path d="m19 9-4 2-3.5-3.5"/></svg>`,
      'parking': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>`,
      'tv': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="15" x="2" y="7" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>`,
      'bath': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6h6a2 2 0 0 1 2 2v2H7V8a2 2 0 0 1 2-2Z"/><path d="M2 12h20v4a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5v-4Z"/><path d="M4 21v1"/><path d="M20 21v1"/></svg>`,
      'wine': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/></svg>`,
      'shield-check': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>`,
      'sparkles': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>`,
      'sun': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`,
      'waves': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>`,
      'car': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>`,
      'gem': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 12L2 9Z"/><path d="M11 3 8 9l4 12 4-12-3-6"/><path d="M2 9h20"/></svg>`,
      'music': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>`,
      'gamepad': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="15" y1="13" x2="15.01" y2="13"/><line x1="18" y1="11" x2="18.01" y2="11"/><rect width="20" height="12" x="2" y="6" rx="6"/></svg>`,
      'utensils': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/><path d="M7 2v20"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/></svg>`,
      'users': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
    };
    return icons[iconName] || `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
  },

  // 1b. Render Popular Amenities Showcase (Các tiện nghi được ưa chuộng nhất)
  renderPopularAmenities() {
    const container = document.getElementById('popular-amenities-container');
    if (!container || typeof POPULAR_AMENITIES === 'undefined') return;

    container.innerHTML = POPULAR_AMENITIES.map((item, index) => `
      <div class="group bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-[#c5a880] transition-all duration-300 flex items-center gap-3.5"
           data-aos="fade-up" data-aos-delay="${index * 40}">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#8c6a38] border border-amber-200/60 flex items-center justify-center flex-shrink-0 group-hover:bg-[#c5a880] group-hover:text-white transition-all duration-300 shadow-xs">
          ${this.getAmenitySvg(item.icon, "w-5 h-5 text-[#8c6a38] transition-colors group-hover:text-white")}
        </div>
        <div class="min-w-0">
          <h4 class="font-bold text-xs sm:text-sm text-[#0b1a2d] group-hover:text-[#b59265] transition-colors truncate">
            ${item.name}
          </h4>
          <p class="text-[11px] text-slate-500 truncate">
            ${item.desc}
          </p>
        </div>
      </div>
    `).join('');
  },

  // 1. Sticky Navigation & Mobile Menu
  initNavbar() {
    const navbar = document.getElementById('main-navbar');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const closeDrawerBtn = document.getElementById('close-drawer-btn');
    const drawerOverlay = document.getElementById('drawer-overlay');

    window.addEventListener('scroll', () => {
      if (window.scrollY > 60) {
        navbar.classList.add('bg-navy-deep/95', 'backdrop-blur-md', 'shadow-xl', 'py-3');
        navbar.classList.remove('bg-transparent', 'py-5');
      } else {
        navbar.classList.remove('bg-navy-deep/95', 'backdrop-blur-md', 'shadow-xl', 'py-3');
        navbar.classList.add('bg-transparent', 'py-5');
      }
    });

    const toggleDrawer = (open) => {
      if (open) {
        mobileDrawer.classList.remove('-translate-x-full');
        drawerOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      } else {
        mobileDrawer.classList.add('-translate-x-full');
        drawerOverlay.classList.add('hidden');
        document.body.style.overflow = '';
      }
    };

    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', () => toggleDrawer(true));
    }
    if (closeDrawerBtn) {
      closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
    }
    if (drawerOverlay) {
      drawerOverlay.addEventListener('click', () => toggleDrawer(false));
    }

    // Close drawer when clicking menu links
    const drawerLinks = document.querySelectorAll('.drawer-link');
    drawerLinks.forEach(link => {
      link.addEventListener('click', () => toggleDrawer(false));
    });
  },

  // 2. Render Rooms Cards
  renderRooms(category = 'all') {
    const container = document.getElementById('rooms-grid');
    if (!container) return;

    const filteredRooms = category === 'all' 
      ? ROOMS_DATA 
      : ROOMS_DATA.filter(r => r.category === category);

    container.innerHTML = filteredRooms.map((room, index) => `
      <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-[#e8dfd2] flex flex-col"
           data-aos="fade-up" data-aos-delay="${index * 100}">
        <!-- Image & Badge -->
        <div class="relative h-64 overflow-hidden">
          <img src="${room.image}" alt="${room.name}" 
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
          <span class="absolute top-4 left-4 bg-gradient-to-r from-amber-600 to-[#c5a880] text-white text-xs font-semibold uppercase px-3 py-1.5 rounded-full shadow-md">
            ${room.badge}
          </span>
          <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end text-white">
            <div>
              <div class="flex items-center gap-1 text-amber-400 text-sm mb-1">
                ${'★'.repeat(5)}
                <span class="text-xs text-white/90 ml-1">(${room.rating})</span>
              </div>
              <span class="text-xs text-white/80 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-md">
                ${room.view}
              </span>
            </div>
            <div class="text-right">
              <span class="text-xs text-white/80 block line-through">${this.formatVND(room.originalPrice)}</span>
              <span class="text-lg font-bold text-amber-300">${this.formatVND(room.price)}</span>
              <span class="text-[11px] text-white/70 block">/ đêm</span>
            </div>
          </div>
        </div>

        <!-- Body Content -->
        <div class="p-6 flex-1 flex flex-col justify-between">
          <div>
            <h3 class="font-display text-xl font-bold text-[#0b1a2d] group-hover:text-[#b59265] transition-colors mb-2">
              ${room.name}
            </h3>
            <p class="text-sm text-slate-600 line-clamp-2 mb-4">
              ${room.subtitle}
            </p>

            <!-- Key Specs -->
            <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 py-3 border-y border-slate-100 mb-4 bg-slate-50/50 rounded-lg p-2.5">
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#c5a880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                <span>${room.size}</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#c5a880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>${room.bed}</span>
              </div>
              <div class="flex items-center gap-1.5 col-span-2">
                <svg class="w-4 h-4 text-[#c5a880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>${room.occupancy}</span>
              </div>
            </div>

            <!-- Amenities Tags with Custom SVG Icons -->
            <div class="flex flex-wrap gap-1.5 mb-6">
              ${room.amenities.slice(0, 4).map(a => `
                <span class="inline-flex items-center gap-1.5 text-[11px] bg-amber-50 text-[#8c6a38] border border-amber-200/60 px-2 py-0.5 rounded-lg font-medium shadow-xs">
                  ${this.getAmenitySvg(a.icon, "w-3.5 h-3.5 text-gold flex-shrink-0")}
                  <span>${a.text}</span>
                </span>
              `).join('')}
              <span class="text-[11px] text-slate-400 self-center font-medium">+${room.amenities.length - 4} tiện ích</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2.5 pt-2">
            <button onclick="App.openRoomDetail('${room.id}')" 
                    class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:border-[#c5a880] hover:text-[#8c6a38] transition-all text-center">
              Xem chi tiết
            </button>
            <button onclick="window.BookingEngine.openBookingModal('${room.id}')" 
                    class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-[#c5a880] to-[#b59265] text-white text-xs font-bold hover:brightness-105 shadow-md shadow-amber-900/10 transition-all text-center">
              Đặt phòng
            </button>
          </div>
        </div>
      </div>
    `).join('');
  },

  // 3. Room Filter Tabs
  setupRoomTabs() {
    const tabs = document.querySelectorAll('.room-filter-btn');
    tabs.forEach(tab => {
      tab.addEventListener('click', (e) => {
        tabs.forEach(t => {
          t.classList.remove('active', 'bg-[#c5a880]', 'text-white');
          t.classList.add('bg-white', 'text-slate-700');
        });
        tab.classList.add('active', 'bg-[#c5a880]', 'text-white');
        tab.classList.remove('bg-white', 'text-slate-700');

        const category = tab.getAttribute('data-category');
        this.currentCategory = category;
        this.renderRooms(category);
      });
    });
  },

  // 4. Render Special Offers
  renderOffers() {
    const container = document.getElementById('offers-container');
    if (!container) return;

    container.innerHTML = SPECIAL_OFFERS.map((offer, index) => `
      <div class="swiper-slide h-auto">
        <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-[#e8dfd2] h-full flex flex-col transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl">
          <div class="relative h-56 overflow-hidden">
            <img src="${offer.image}" alt="${offer.title}" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute top-4 left-4 bg-rose-600 text-white text-xs font-bold uppercase px-3 py-1 rounded-full shadow">
              ${offer.badge}
            </span>
            <span class="absolute bottom-4 left-4 text-white text-xs font-medium bg-black/50 backdrop-blur-sm px-2.5 py-1 rounded">
              ${offer.expiry}
            </span>
          </div>

          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-xs font-semibold text-[#c5a880] uppercase tracking-wider block mb-1">
                ${offer.tag}
              </span>
              <h3 class="font-display text-lg font-bold text-[#0b1a2d] mb-2 leading-snug">
                ${offer.title}
              </h3>
              <p class="text-xs text-slate-600 mb-4 line-clamp-2">
                ${offer.excerpt}
              </p>

              <div class="space-y-1.5 mb-6">
                ${offer.benefits.map(b => `
                  <div class="flex items-center gap-2 text-xs text-slate-700">
                    <span class="text-emerald-500 font-bold">✓</span>
                    <span>${b}</span>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-[11px] text-slate-400 block">Giá ưu đãi</span>
                <span class="text-sm font-bold text-[#b59265]">${offer.priceText}</span>
              </div>
              <button onclick="window.BookingEngine.openBookingModal('superior-room')" 
                      class="px-4 py-2 bg-[#0b1a2d] text-white text-xs font-semibold rounded-xl hover:bg-[#c5a880] transition-colors">
                Nhận ưu đãi
              </button>
            </div>
          </div>
        </div>
      </div>
    `).join('');
  },

  // 5. Render Amenities
  renderAmenities() {
    const container = document.getElementById('amenities-grid');
    if (!container) return;

    container.innerHTML = AMENITIES_DATA.map((item, index) => `
      <div class="group relative rounded-2xl overflow-hidden shadow-xl border border-white/10 h-96 flex flex-col justify-end p-8"
           data-aos="fade-up" data-aos-delay="${index * 120}">
        <!-- Background Image -->
        <img src="${item.image}" alt="${item.title}" 
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-[#07111e] via-[#0b1a2d]/70 to-transparent"></div>

        <!-- Content -->
        <div class="relative z-10">
          <span class="text-xs font-semibold text-[#c5a880] uppercase tracking-widest block mb-1">
            ${item.location}
          </span>
          <h3 class="font-display text-2xl font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">
            ${item.title}
          </h3>
          <p class="text-sm text-slate-300 mb-4 line-clamp-2">
            ${item.description}
          </p>

          <div class="flex flex-wrap gap-2 pt-2 border-t border-white/15">
            ${item.highlights.map(h => `
              <span class="text-[11px] bg-white/10 backdrop-blur-md text-amber-200 px-2.5 py-1 rounded-full border border-white/10">
                ${h}
              </span>
            `).join('')}
          </div>
        </div>
      </div>
    `).join('');
  },

  // 6. Render Testimonials
  renderTestimonials() {
    const container = document.getElementById('testimonials-container');
    if (!container) return;

    container.innerHTML = TESTIMONIALS_DATA.map(t => `
      <div class="swiper-slide h-auto">
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-[#e8dfd2] h-full flex flex-col justify-between transition-all duration-300 hover:shadow-xl">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-1 text-amber-400 text-lg">
                ${'★'.repeat(t.rating)}
              </div>
              <span class="text-xs text-slate-400 font-medium">${t.date}</span>
            </div>
            <h4 class="font-display font-bold text-slate-800 text-base mb-2">
              "${t.title}"
            </h4>
            <p class="text-sm text-slate-600 leading-relaxed italic mb-6">
              "${t.content}"
            </p>
          </div>

          <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <img src="${t.avatar}" alt="${t.author}" class="w-12 h-12 rounded-full object-cover border-2 border-[#c5a880]">
            <div>
              <h5 class="font-bold text-sm text-[#0b1a2d]">${t.author}</h5>
              <span class="text-xs text-[#c5a880]">${t.location}</span>
            </div>
          </div>
        </div>
      </div>
    `).join('');
  },

  // 7. Render Attractions
  renderAttractions() {
    const container = document.getElementById('attractions-grid');
    if (!container) return;

    container.innerHTML = ATTRACTIONS_DATA.map((spot, index) => `
      <div class="bg-white/80 backdrop-blur-sm p-4 rounded-xl border border-amber-200/50 hover:border-[#c5a880] transition-all">
        <div class="flex items-center justify-between mb-1">
          <h4 class="font-bold text-sm text-[#0b1a2d]">${spot.name}</h4>
          <span class="text-xs font-semibold text-[#8c6a38] bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
            ${spot.distance}
          </span>
        </div>
        <p class="text-xs text-slate-600">${spot.description}</p>
      </div>
    `).join('');
  },

  // 8. Room Detail Modal
  setupRoomDetailModal() {
    const modal = document.getElementById('room-detail-modal');
    const closeBtn = document.getElementById('close-room-modal');

    if (closeBtn && modal) {
      closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      });
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        }
      });
    }
  },

  openRoomDetail(roomId) {
    const room = ROOMS_DATA.find(r => r.id === roomId);
    if (!room) return;

    const modal = document.getElementById('room-detail-modal');
    const title = document.getElementById('detail-room-name');
    const img = document.getElementById('detail-room-img');
    const desc = document.getElementById('detail-room-desc');
    const size = document.getElementById('detail-room-size');
    const bed = document.getElementById('detail-room-bed');
    const view = document.getElementById('detail-room-view');
    const occ = document.getElementById('detail-room-occ');
    const price = document.getElementById('detail-room-price');
    const origPrice = document.getElementById('detail-room-orig-price');
    const amenitiesContainer = document.getElementById('detail-room-amenities');
    const bookBtn = document.getElementById('detail-book-btn');

    if (title) title.textContent = room.name;
    if (img) img.src = room.image;
    if (desc) desc.textContent = room.description;
    if (size) size.textContent = room.size;
    if (bed) bed.textContent = room.bed;
    if (view) view.textContent = room.view;
    if (occ) occ.textContent = room.occupancy;
    if (price) price.textContent = this.formatVND(room.price);
    if (origPrice) origPrice.textContent = this.formatVND(room.originalPrice);

    if (amenitiesContainer) {
      amenitiesContainer.innerHTML = room.amenities.map(a => `
        <div class="flex items-center gap-2.5 text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-xs">
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-gold flex items-center justify-center flex-shrink-0 border border-amber-200/60">
            ${this.getAmenitySvg(a.icon, "w-4 h-4 text-[#8c6a38]")}
          </div>
          <span class="font-medium">${a.text}</span>
        </div>
      `).join('');
    }

    if (bookBtn) {
      bookBtn.onclick = () => {
        modal.classList.add('hidden');
        window.BookingEngine.openBookingModal(room.id);
      };
    }

    if (modal) {
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
  },

  // 9. VR 360 Tour Modal
  setupVRModal() {
    const vrModal = document.getElementById('vr-modal');
    const openBtns = document.querySelectorAll('.open-vr-btn');
    const closeBtn = document.getElementById('close-vr-btn');

    openBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        if (vrModal) {
          vrModal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    if (closeBtn && vrModal) {
      closeBtn.addEventListener('click', () => {
        vrModal.classList.add('hidden');
        document.body.style.overflow = '';
      });
      vrModal.addEventListener('click', (e) => {
        if (e.target === vrModal) {
          vrModal.classList.add('hidden');
          document.body.style.overflow = '';
        }
      });
    }
  },

  // 10. Back to top button
  setupBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
      if (window.scrollY > 400) {
        btn.classList.remove('opacity-0', 'invisible', 'translate-y-4');
        btn.classList.add('opacity-100', 'visible', 'translate-y-0');
      } else {
        btn.classList.add('opacity-0', 'invisible', 'translate-y-4');
        btn.classList.remove('opacity-100', 'visible', 'translate-y-0');
      }
    });

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  },

  // 11. Animated Stats Counter
  setupStatsCounter() {
    const statElements = document.querySelectorAll('.stat-number');
    if (!statElements.length) return;

    let started = false;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !started) {
          started = true;
          statElements.forEach(el => {
            const target = parseFloat(el.getAttribute('data-target'));
            const isDecimal = el.getAttribute('data-decimal') === 'true';
            const duration = 1800;
            const start = performance.now();

            const updateCount = (currentTime) => {
              const elapsed = currentTime - start;
              const progress = Math.min(elapsed / duration, 1);
              // Ease out quadratic
              const easeProgress = 1 - (1 - progress) * (1 - progress);
              const currentVal = easeProgress * target;

              el.textContent = isDecimal ? currentVal.toFixed(1) : Math.floor(currentVal);

              if (progress < 1) {
                requestAnimationFrame(updateCount);
              } else {
                el.textContent = isDecimal ? target.toFixed(1) : target;
              }
            };
            requestAnimationFrame(updateCount);
          });
        }
      });
    }, { threshold: 0.3 });

    const statsSection = document.getElementById('stats-section');
    if (statsSection) observer.observe(statsSection);
  },

  // 12. Initialize Swipers
  initSwiperCarousels() {
    if (typeof Swiper === 'undefined') return;

    // Offers Swiper
    new Swiper('.offers-swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false
      },
      pagination: {
        el: '.offers-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.offers-next',
        prevEl: '.offers-prev'
      },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }
    });

    // Testimonials Swiper
    new Swiper('.testimonials-swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: {
        delay: 4500,
        disableOnInteraction: false
      },
      pagination: {
        el: '.testimonials-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.testimonials-next',
        prevEl: '.testimonials-prev'
      },
      breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }
    });
  },

  // 13. Initialize AOS (Animate On Scroll)
  initAOS() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80
      });
    }
  }
};

window.App = App;
