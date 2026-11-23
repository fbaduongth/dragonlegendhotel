/**
 * Dragon Legend Hotel - Main UI Controller (WordPress Integrated)
 * Xử lý giao diện, animation AOS, Swiper carousels, tabs lọc phòng và tương tác
 */

document.addEventListener('DOMContentLoaded', () => {
  App.init();
});

const POPULAR_AMENITIES_DATA = [
  { id: "airport-shuttle", name: "Xe đưa đón sân bay", icon: "shuttle", desc: "Hỗ trợ đưa đón sân bay Cát Bi & Vân Đồn tiện lợi" },
  { id: "non-smoking", name: "Phòng không hút thuốc", icon: "no-smoking", desc: "Không khí trong lành, khử khuẩn định kỳ" },
  { id: "free-wifi", name: "WiFi miễn phí tốc độ cao", icon: "wifi", desc: "Độ phủ sóng 100% trong phòng và toàn khách sạn" },
  { id: "room-service", name: "Dịch vụ phòng chu đáo", icon: "concierge", desc: "Phục vụ ẩm thực và dọn dẹp theo yêu cầu 24/7" },
  { id: "family-rooms", name: "Phòng gia đình tiện nghi", icon: "family", desc: "Không gian rộng rãi, giường phụ cho trẻ em" },
  { id: "restaurant", name: "Nhà hàng ẩm thực Á - Âu", icon: "restaurant", desc: "Hải sản tươi sống và tiệc buffet phong phú" },
  { id: "front-desk", name: "Lễ tân phục vụ 24 giờ", icon: "reception", desc: "Nhận/trả phòng linh hoạt, hỗ trợ tour vịnh" },
  { id: "elevator", name: "Thang máy hiện đại", icon: "elevator", desc: "Hệ thống thang máy thẻ từ an ninh cao" },
  { id: "air-conditioner", name: "Điều hòa nhiệt độ 2 chiều", icon: "snowflake", desc: "Vận hành êm dịu, tùy chỉnh theo sở thích" },
  { id: "breakfast", name: "Bữa sáng buffet thơm ngon", icon: "coffee", desc: "Hơn 50 món phong phú từ truyền thống đến quốc tế" },
  { id: "sky-pool", name: "Bể bơi trong nhà & bar", icon: "pool", desc: "Thư giãn" },
  { id: "free-parking", name: "Bãi đỗ xe miễn phí", icon: "parking", desc: "Khuôn viên đậu xe ô tô rộng rãi, an ninh 24/7" },
  { id: "gym-yoga", name: "Gym & Yoga", icon: "gym", desc: "Trang thiết bị hiện đại & phòng tập yoga thư thái" },
  { id: "spa", name: "Spa", icon: "spa", desc: "Thư giãn tinh thần và phục hồi năng lượng chuyên sâu" },
  { id: "conference-hall", name: "Sảnh tiệc & Hội nghị", icon: "conference", desc: "Không gian sang trọng, sức chứa lớn & âm thanh chuẩn quốc tế" }
];

const ATTRACTIONS_DATA = [
  {
    name: "Sun World Hạ Long Complex",
    distance: "1.8 km (4 phút di chuyển)",
    description: "Tổ hợp công viên giải trí hàng đầu Đông Nam Á với Cáp treo Nữ Hoàng và Vòng quay Mặt Trời."
  },
  {
    name: "Bãi tắm Bãi Cháy & Phố đi bộ",
    distance: "1.2 km (3 phút di chuyển)",
    description: "Bờ cát trắng mịn trải dài 3km, làn nước xanh mát cùng các quán cafe biển sôi động về đêm."
  },
  {
    name: "Chợ đêm Hạ Long & Halong Marine Plaza",
    distance: "300 m (Đi bộ 3 phút)",
    description: "Thế giới quà lưu niệm, hải sản sấy khô và khu ẩm thực đường phố sầm uất ngay cạnh khách sạn."
  },
  {
    name: "Cảng tàu khách quốc tế Hạ Long",
    distance: "3.5 km (7 phút di chuyển)",
    description: "Bến xuất phát của các du thuyền thăm quan kỳ quan Vịnh Hạ Long và Đảo Ti Tốp."
  }
];

const App = {
  currentCategory: 'all',
  compareList: [],
  compareDiffOnly: false,

  init() {
    window.App = this;
    this.initNavbar();
    this.loadCompareState();
    this.renderPopularAmenities();
    this.renderRooms();
    this.renderAttractions();
    this.setupRoomTabs();
    this.setupRoomDetailModal();
    this.initRoomCompare();
    this.setupVRModal();
    this.setupBackToTop();
    this.setupStatsCounter();
    this.initSwiperCarousels();
    this.initAOS();
    this.setupNewsletterForm();

    // Khởi động Booking Engine
    if (window.BookingEngine) {
      window.BookingEngine.init();
    }
  },

  getRooms() {
    if (window.DragonLegendAjax && window.DragonLegendAjax.rooms && window.DragonLegendAjax.rooms.length > 0) {
      return window.DragonLegendAjax.rooms;
    }
    return typeof ROOMS_DATA !== 'undefined' ? ROOMS_DATA : [];
  },

  decodeHTML(str) {
    if (!str) return '';
    const cleaned = String(str)
      .replace(/<!--[\s\S]*?-->/g, '')
      .replace(/<\/p>\s*<p[^>]*>/gi, '\n\n')
      .replace(/<br\s*\/?>/gi, '\n')
      .replace(/<[^>]+>/g, '')
      .trim();
    const txt = document.createElement('textarea');
    txt.innerHTML = cleaned;
    return txt.value;
  },

  formatVND(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
  },

  getAmenitySvg(iconName, customClass = "w-5 h-5 text-[#dfa843]") {
    if (!iconName) return '';
    if (iconName.startsWith('http://') || iconName.startsWith('https://') || iconName.startsWith('/')) {
      return `<img src="${iconName}" class="${customClass} object-contain inline-block" alt="Icon" loading="lazy">`;
    }
    if (iconName.startsWith('<svg')) {
      return iconName;
    }
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
      'gym': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>`,
      'dumbbell': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>`,
      'spa': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>`,
      'lotus': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>`,
      'conference': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
      'users': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
      'tennis': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20"/><path d="M2 12a14.5 14.5 0 0 0 20 0"/></svg>`,
      'kids': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"/><path d="M6.5 9a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2Z"/><path d="m9 15 3 6 3-6"/></svg>`,
      'laundry': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 0-3 3"/><circle cx="7" cy="6" r="1"/></svg>`,
      'billiards': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 10v4"/></svg>`,
      'cocktail': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m8 22 4-11V2"/><path d="m16 22-4-11"/><path d="M4 2h16l-8 9-8-9Z"/></svg>`,
      'bicycle': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 6H5m7 11.5 3-7h4.5"/></svg>`,
      'charging': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 11h6l-4 6v-4H8l3-6"/></svg>`,
      'luggage': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="14" x="4" y="7" rx="2"/><path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/><path d="M4 12h16"/></svg>`,
      'money': `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>`
    };
    return icons[iconName] || `<svg class="${customClass}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
  },

  renderPopularAmenities() {
    const container = document.getElementById('popular-amenities-container');
    if (!container) return;

    const amenities = (window.DragonLegendAjax && Array.isArray(window.DragonLegendAjax.popular_amenities) && window.DragonLegendAjax.popular_amenities.length > 0)
      ? window.DragonLegendAjax.popular_amenities
      : POPULAR_AMENITIES_DATA;

    container.innerHTML = amenities.map((item, index) => `
      <div class="group bg-white p-3 sm:p-4 rounded-2xl border border-amber-900/10 shadow-sm hover:shadow-md hover:border-[#dfa843] transition-all duration-300 flex items-center gap-2.5 sm:gap-3.5"
           data-aos="fade-up" data-aos-delay="${index * 30}">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-[#9b671a] border border-amber-200/60 flex items-center justify-center flex-shrink-0 group-hover:bg-[#dfa843] group-hover:text-white transition-all duration-300 shadow-xs">
          ${this.getAmenitySvg(item.icon, "w-4 h-4 sm:w-5 sm:h-5 text-[#9b671a] transition-colors group-hover:text-white")}
        </div>
        <div class="min-w-0 flex-1">
          <h4 class="font-bold text-[12.5px] sm:text-sm text-[#320908] group-hover:text-[#dfa843] transition-colors leading-snug line-clamp-1">
            ${this.decodeHTML(item.name)}
          </h4>
          <p class="text-[11px] text-stone-500 truncate mt-0.5">
            ${this.decodeHTML(item.desc)}
          </p>
        </div>
      </div>
    `).join('');
  },

  renderRooms() {
    const container = document.getElementById('rooms-grid');
    if (!container) return;

    const rooms = this.getRooms();
    const filtered = (!this.currentCategory || this.currentCategory === 'all')
      ? rooms 
      : rooms.filter(r => {
          const target = this.currentCategory.toLowerCase();
          if (Array.isArray(r.categories) && r.categories.length > 0) {
            return r.categories.some(c => String(c).toLowerCase() === target);
          }
          const roomCat = (r.category || '').toLowerCase();
          return roomCat === target || roomCat.includes(target) || target.includes(roomCat);
        });

    container.innerHTML = filtered.map((room, index) => {
      const permalink = room.permalink || (`/phong-nghi/${room.id}/`);
      return `
      <div class="swiper-slide h-auto flex flex-col">
        <div class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-[#f0e5c5] flex flex-col h-full"
             data-aos="fade-up" data-aos-delay="${index * 60}">
          <!-- Image & Badge (Click xem nhanh popup) -->
          <div class="relative h-52 sm:h-60 overflow-hidden cursor-pointer" onclick="App.openRoomDetail('${room.id}')">
            <img src="${room.image}" alt="${room.name}" 
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            ${room.badge ? `
            <span class="absolute top-3 left-3 bg-gradient-to-r from-amber-600 to-[#dfa843] text-white text-[11px] font-semibold uppercase px-2.5 py-1 rounded-full shadow-md">
              ${room.badge}
            </span>
            ` : ''}
            <button type="button"
                    data-compare-room="${room.id}"
                    onclick="App.toggleCompareRoom('${room.id}', event)"
                    class="dl-compare-card-pill"
                    title="Thêm vào so sánh phòng">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
              <span class="dl-compare-btn-label">So sánh</span>
            </button>
            <div class="absolute bottom-3 left-3 right-3 flex justify-between items-end gap-2 text-white">
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1 text-amber-400 text-xs mb-0.5">
                  ${'★'.repeat(5)}
                  <span class="text-[11px] text-white/90 ml-1">(${room.rating || '4.9'})</span>
                </div>
                <span class="inline-block max-w-full truncate text-[11px] text-white/90 bg-black/40 backdrop-blur-sm px-2 py-0.5 rounded">
                  ${room.view}
                </span>
              </div>
              <div class="text-right flex-shrink-0 whitespace-nowrap">
                ${room.originalPrice ? `<span class="text-[11px] text-white/70 block line-through">${this.formatVND(room.originalPrice)}</span>` : ''}
                <span class="text-base sm:text-lg font-bold text-amber-300">${this.formatVND(room.price)}</span>
                <span class="text-[10px] text-white/70 block">/ đêm</span>
              </div>
            </div>
          </div>

          <!-- Body Content -->
          <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="font-display text-base sm:text-lg font-bold text-[#320908] group-hover:text-[#dfa843] transition-colors mb-1.5 cursor-pointer leading-snug" onclick="App.openRoomDetail('${room.id}')">
                ${this.decodeHTML(room.name)}
              </h3>
              <p class="text-xs text-stone-500 line-clamp-2 mb-3">
                ${this.decodeHTML(room.subtitle || room.description)}
              </p>

              <!-- Key Specs -->
              <div class="grid grid-cols-2 gap-2 text-xs text-stone-600 py-2 border-y border-amber-900/10 mb-3 bg-[#fdfaf1] rounded-lg px-2.5">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-[#dfa843] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                  <span class="truncate">${room.size}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-[#dfa843] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="truncate">${room.bed}</span>
                </div>
                <div class="flex items-center gap-1.5 col-span-2">
                  <svg class="w-3.5 h-3.5 text-[#dfa843] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  <span class="truncate">${room.occupancy || '2 Người lớn'}</span>
                </div>
              </div>

              <!-- Amenities Tags with Custom SVG Icons -->
              <div class="flex flex-wrap gap-1.5 mb-4">
                ${(room.amenities || []).slice(0, 3).map(a => `
                  <span class="inline-flex items-center gap-1 text-[11px] bg-amber-50 text-[#9b671a] border border-amber-200/60 px-2 py-0.5 rounded-md font-medium shadow-xs">
                    ${this.getAmenitySvg(a.icon, "w-3 h-3 text-[#dfa843] flex-shrink-0")}
                    <span class="truncate">${a.text}</span>
                  </span>
                `).join('')}
                ${(room.amenities && room.amenities.length > 3) ? `<span class="text-[11px] text-stone-400 self-center font-medium">+${room.amenities.length - 3} tiện ích</span>` : ''}
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 pt-1">
              <a href="${permalink}" 
                 class="flex-1 py-2 px-2.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:border-[#dfa843] hover:text-[#9b671a] hover:bg-amber-50/40 transition-all text-center whitespace-nowrap inline-flex items-center justify-center">
                Xem chi tiết
              </a>
              <button type="button" onclick="window.BookingEngine && window.BookingEngine.openBookingModal('${room.id}')" 
                      class="flex-1 py-2 px-2.5 rounded-xl bg-gradient-to-r from-[#dfa843] to-[#c88d2b] text-[#320908] text-xs font-bold hover:brightness-105 shadow-md shadow-amber-900/10 transition-all text-center whitespace-nowrap">
                Đặt phòng
              </button>
            </div>
          </div>
        </div>
      </div>
    `;}).join('');

    this.initRoomsSwiper();
    this.syncCompareButtonsUI();
  },

  initRoomsSwiper() {
    if (typeof Swiper === 'undefined') {
      setTimeout(() => this.initRoomsSwiper(), 150);
      return;
    }

    if (this.roomsSwiper && typeof this.roomsSwiper.destroy === 'function') {
      try {
        this.roomsSwiper.destroy(true, true);
      } catch (e) {}
      this.roomsSwiper = null;
    }

    const swiperEl = document.querySelector('.rooms-swiper');
    if (!swiperEl) return;

    this.roomsSwiper = new Swiper('.rooms-swiper', {
      slidesPerView: 1.15,
      spaceBetween: 16,
      watchOverflow: true,
      rewind: true,
      grabCursor: true,
      pagination: {
        el: '.rooms-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.rooms-next',
        prevEl: '.rooms-prev',
      },
      breakpoints: {
        540: {
          slidesPerView: 1.6,
          spaceBetween: 18,
        },
        768: {
          slidesPerView: 2.2,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 24,
        },
        1280: {
          slidesPerView: 4,
          spaceBetween: 24,
        },
      }
    });
  },

  setupRoomTabs() {
    const tabs = document.querySelectorAll('.room-filter-btn');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => {
          t.classList.remove('active', 'bg-gold', 'text-white');
          t.classList.add('bg-white', 'text-slate-700');
          t.setAttribute('aria-selected', 'false');
        });

        tab.classList.add('active', 'bg-gold', 'text-white');
        tab.classList.remove('bg-white', 'text-slate-700');
        tab.setAttribute('aria-selected', 'true');

        this.currentCategory = tab.getAttribute('data-category');
        this.renderRooms();
      });
    });
  },

  renderAttractions() {
    const container = document.getElementById('attractions-list');
    if (!container) return;

    const list = (typeof DragonLegendAjax !== 'undefined' && DragonLegendAjax.attractions && DragonLegendAjax.attractions.length) 
                 ? DragonLegendAjax.attractions 
                 : ATTRACTIONS_DATA;

    container.innerHTML = list.map(item => `
      <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-gold border border-gold/25 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h4 class="font-bold text-xs text-navy-deep">${item.name}</h4>
            <span class="text-[10px] font-bold text-gold bg-amber-50 px-2 py-0.5 rounded-full border border-gold/30">${item.distance}</span>
          </div>
          <p class="text-[11px] text-slate-500 mt-0.5">${item.description}</p>
        </div>
      </div>
    `).join('');
  },

  setupRoomDetailModal() {
    const modal = document.getElementById('room-detail-modal');
    const closeBtn = document.getElementById('close-room-modal');
    const bookBtn = document.getElementById('detail-book-btn');

    if (closeBtn && modal) {
      closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      });
    }

    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        }
      });
    }

    const compareBtn = document.getElementById('detail-compare-btn');
    if (compareBtn) {
      compareBtn.addEventListener('click', () => {
        if (modal) modal.classList.add('hidden');
        this.compareWithRoom(this.currentModalRoomId);
      });
    }

    if (bookBtn) {
      bookBtn.addEventListener('click', () => {
        if (modal) modal.classList.add('hidden');
        if (window.BookingEngine) {
          window.BookingEngine.openBookingModal(this.currentModalRoomId);
        }
      });
    }
  },

  /* ========================================== */
  /* ROOM COMPARISON ENGINE (SO SÁNH HẠNG PHÒNG)*/
  /* ========================================== */
  loadCompareState() {
    try {
      const saved = sessionStorage.getItem('dl_compare_rooms');
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) {
          const validIds = this.getRooms().map(r => r.id);
          this.compareList = parsed.filter(id => validIds.includes(id)).slice(0, 4);
        }
      }
    } catch (e) {
      this.compareList = [];
    }
  },

  saveCompareState() {
    try {
      sessionStorage.setItem('dl_compare_rooms', JSON.stringify(this.compareList));
    } catch (e) {}
  },

  initRoomCompare() {
    const modal = document.getElementById('room-compare-modal');
    const closeBtn = document.getElementById('close-compare-modal');
    const clearBtn = document.getElementById('compare-clear-btn');
    const nowBtn = document.getElementById('compare-now-btn');
    const addSlotBtn = document.getElementById('compare-add-slot-btn');
    const diffCheckbox = document.getElementById('compare-diff-only-checkbox');

    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.closeCompareModal());
    }
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) this.closeCompareModal();
      });
    }
    if (clearBtn) {
      clearBtn.addEventListener('click', () => this.clearCompareRooms());
    }
    if (nowBtn) {
      nowBtn.addEventListener('click', () => this.openCompareModal());
    }
    if (addSlotBtn) {
      addSlotBtn.addEventListener('click', () => this.addCompareSlot());
    }
    if (diffCheckbox) {
      diffCheckbox.addEventListener('change', (e) => {
        this.compareDiffOnly = !!e.target.checked;
        this.renderCompareModalTable();
      });
    }

    this.syncCompareButtonsUI();
  },

  toggleCompareRoom(roomId, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    if (!roomId) return;

    const idx = this.compareList.indexOf(roomId);
    if (idx > -1) {
      this.compareList.splice(idx, 1);
    } else {
      if (this.compareList.length >= 4) {
        alert('Bạn có thể chọn tối đa 4 hạng phòng để so sánh cùng lúc.');
        return;
      }
      this.compareList.push(roomId);
    }

    this.saveCompareState();
    this.syncCompareButtonsUI();
  },

  compareWithRoom(roomId) {
    const rooms = this.getRooms();
    if (!rooms.length) return;

    if (roomId && !this.compareList.includes(roomId)) {
      if (this.compareList.length >= 4) {
        this.compareList.pop();
      }
      this.compareList.unshift(roomId);
    }

    // Nếu mới chỉ có 1 phòng, tự động gợi ý thêm 1-2 phòng tiếp theo để so sánh trực quan ngay
    if (this.compareList.length < 2) {
      for (let i = 0; i < rooms.length; i++) {
        if (!this.compareList.includes(rooms[i].id)) {
          this.compareList.push(rooms[i].id);
          if (this.compareList.length >= 3) break;
        }
      }
    }

    this.saveCompareState();
    this.syncCompareButtonsUI();
    this.openCompareModal();
  },

  compareTwoRooms(roomId1, roomId2, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    const list = [];
    if (roomId1) list.push(roomId1);
    if (roomId2 && roomId2 !== roomId1) list.push(roomId2);
    this.compareList = list;
    this.saveCompareState();
    this.syncCompareButtonsUI();
    this.openCompareModal();
  },

  removeCompareRoom(roomId) {
    this.compareList = this.compareList.filter(id => id !== roomId);
    this.saveCompareState();
    this.syncCompareButtonsUI();
    const modal = document.getElementById('room-compare-modal');
    if (modal && !modal.classList.contains('hidden')) {
      if (this.compareList.length === 0) {
        this.closeCompareModal();
      } else {
        this.renderCompareModalTable();
      }
    }
  },

  clearCompareRooms() {
    this.compareList = [];
    this.saveCompareState();
    this.syncCompareButtonsUI();
    this.closeCompareModal();
  },

  changeCompareSlot(slotIndex, newRoomId) {
    if (!newRoomId) return;
    const existingIdx = this.compareList.indexOf(newRoomId);
    if (existingIdx > -1 && existingIdx !== slotIndex) {
      // Hoán đổi vị trí nếu chọn phòng đã có ở cột khác
      const oldId = this.compareList[slotIndex];
      this.compareList[slotIndex] = newRoomId;
      this.compareList[existingIdx] = oldId;
    } else {
      this.compareList[slotIndex] = newRoomId;
    }
    this.saveCompareState();
    this.syncCompareButtonsUI();
    this.renderCompareModalTable();
  },

  addCompareSlot() {
    const rooms = this.getRooms();
    if (this.compareList.length >= 4 || this.compareList.length >= rooms.length) return;
    const nextRoom = rooms.find(r => !this.compareList.includes(r.id));
    if (nextRoom) {
      this.compareList.push(nextRoom.id);
      this.saveCompareState();
      this.syncCompareButtonsUI();
      this.renderCompareModalTable();
    }
  },

  syncCompareButtonsUI() {
    const count = this.compareList.length;
    const rooms = this.getRooms();

    // 1. Cập nhật trạng thái tất cả các nút [data-compare-room] trên trang
    document.querySelectorAll('[data-compare-room]').forEach(btn => {
      const rId = btn.getAttribute('data-compare-room');
      const isSelected = this.compareList.includes(rId);
      const labelEl = btn.querySelector('.dl-compare-btn-label');
      if (isSelected) {
        btn.classList.add('is-active');
        if (labelEl) labelEl.textContent = 'Đã chọn';
      } else {
        btn.classList.remove('is-active');
        if (labelEl) labelEl.textContent = 'So sánh';
      }
    });

    // 2. Cập nhật số đếm trên nút Header
    document.querySelectorAll('[data-compare-count]').forEach(badge => {
      if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    });

    // 3. Cập nhật Thanh So Sánh Nổi (#room-compare-bar)
    const bar = document.getElementById('room-compare-bar');
    const slotsCont = document.getElementById('compare-bar-slots');
    const subtitle = document.getElementById('compare-bar-subtitle');
    const nowCount = document.getElementById('compare-now-count');

    if (!bar || !slotsCont) return;

    if (count === 0) {
      bar.classList.add('hidden');
      return;
    }

    bar.classList.remove('hidden');
    if (subtitle) subtitle.textContent = `Đã chọn ${count}/4 phòng`;
    if (nowCount) nowCount.textContent = count;

    const selectedRooms = this.compareList
      .map(id => rooms.find(r => r.id === id))
      .filter(Boolean);

    let html = selectedRooms.map(r => `
      <div class="dl-compare-bar-chip">
        <img src="${r.image}" alt="${this.decodeHTML(r.name)}" class="dl-compare-bar-thumb cursor-pointer" onclick="App.openRoomDetail('${r.id}')" title="Xem nhanh ${this.decodeHTML(r.name)}">
        <div class="dl-compare-bar-chip-info cursor-pointer" onclick="App.openRoomDetail('${r.id}')" title="Xem nhanh ${this.decodeHTML(r.name)}">
          <div class="dl-compare-bar-chip-name hover:text-gold transition-colors">${this.decodeHTML(r.name)}</div>
          <div class="dl-compare-bar-chip-price">${this.formatVND(r.price)}</div>
        </div>
        <button type="button" onclick="App.removeCompareRoom('${r.id}')" class="dl-compare-bar-chip-remove" aria-label="Bỏ phòng này">&times;</button>
      </div>
    `).join('');

    if (count < 2) {
      html += `
        <button type="button" onclick="App.openCompareModal()" class="dl-compare-bar-empty-slot">
          <span>+ Chọn thêm phòng để so sánh</span>
        </button>
      `;
    }

    slotsCont.innerHTML = html;
  },

  parseRoomArea(sizeStr) {
    if (!sizeStr) return 0;
    const nums = String(sizeStr).match(/\d+(\.\d+)?/g);
    if (!nums || !nums.length) return 0;
    return Math.max(...nums.map(Number));
  },

  openCompareModal() {
    const rooms = this.getRooms();
    if (!rooms.length) return;

    // Nếu khách bấm mở bảng so sánh khi chưa chọn đủ 2 phòng, tự động điền 3 hạng phòng tiêu biểu
    if (this.compareList.length < 2) {
      for (let i = 0; i < rooms.length; i++) {
        if (!this.compareList.includes(rooms[i].id)) {
          this.compareList.push(rooms[i].id);
          if (this.compareList.length >= Math.min(3, rooms.length)) break;
        }
      }
      this.saveCompareState();
      this.syncCompareButtonsUI();
    }

    this.renderCompareModalTable();
    const modal = document.getElementById('room-compare-modal');
    if (modal) {
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
  },

  closeCompareModal() {
    const modal = document.getElementById('room-compare-modal');
    if (modal) {
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  },

  renderCompareModalTable() {
    const container = document.getElementById('compare-modal-body');
    const addSlotBtn = document.getElementById('compare-add-slot-btn');
    if (!container) return;

    const allRooms = this.getRooms();
    const selectedRooms = this.compareList
      .map(id => allRooms.find(r => r.id === id))
      .filter(Boolean);

    if (addSlotBtn) {
      if (selectedRooms.length >= 4 || selectedRooms.length >= allRooms.length) {
        addSlotBtn.classList.add('hidden');
      } else {
        addSlotBtn.classList.remove('hidden');
      }
    }

    if (!selectedRooms.length) {
      container.innerHTML = '<div class="p-8 text-center text-slate-500">Chưa có hạng phòng nào được chọn để so sánh.</div>';
      return;
    }

    // Tìm phòng giá tốt nhất, diện tích lớn nhất, đánh giá cao nhất để gắn nhãn gợi ý
    const prices = selectedRooms.map(r => Number(r.price) || 999999999);
    const minPrice = Math.min(...prices);

    const areas = selectedRooms.map(r => this.parseRoomArea(r.size));
    const maxArea = Math.max(...areas);

    const ratings = selectedRooms.map(r => parseFloat(r.rating) || 0);
    const maxRating = Math.max(...ratings);

    // Kiểm tra sự khác biệt giữa các phòng cho chế độ "Chỉ hiện điểm khác biệt"
    const isDiff = (vals) => {
      const normalized = vals.map(v => String(v || '').trim().toLowerCase());
      return new Set(normalized).size > 1;
    };

    const diffPrice = isDiff(selectedRooms.map(r => r.price));
    const diffSize  = isDiff(selectedRooms.map(r => r.size));
    const diffBed   = isDiff(selectedRooms.map(r => r.bed));
    const diffOcc   = isDiff(selectedRooms.map(r => r.occupancy));
    const diffView  = isDiff(selectedRooms.map(r => r.view));
    const diffRate  = isDiff(selectedRooms.map(r => r.rating));

    // Tập hợp toàn bộ Tiện Nghi Phòng Cao Cấp từ các phòng đang so sánh
    const amenitiesMap = new Map();
    selectedRooms.forEach(r => {
      (r.amenities || []).forEach(a => {
        const key = (a.text || a.icon || '').trim();
        if (key && !amenitiesMap.has(key)) {
          amenitiesMap.set(key, { icon: a.icon || 'sparkles', text: a.text || key });
        }
      });
    });
    const allAmenities = Array.from(amenitiesMap.values());

    const rowClass = (hasDiff) => {
      if (this.compareDiffOnly && !hasDiff) return 'dl-compare-row-hidden';
      return hasDiff ? 'dl-compare-row-diff' : '';
    };

    let html = `
      <div class="dl-compare-table-scroll">
        <table class="dl-compare-table">
          <thead>
            <tr>
              <th class="dl-compare-sticky-col dl-compare-corner-th">
                <div class="dl-compare-corner-box">
                  <span class="text-[11px] font-bold uppercase tracking-wider text-gold block">Tiêu chí so sánh</span>
                  <span class="text-sm font-bold text-navy-deep block mt-1">Đang so sánh ${selectedRooms.length} hạng phòng</span>
                  <p class="text-[11px] text-slate-500 font-normal mt-1">Bạn có thể đổi hạng phòng ở menu thả xuống trên mỗi cột.</p>
                </div>
              </th>
              ${selectedRooms.map((room, idx) => {
                const isBestPrice = selectedRooms.length > 1 && Number(room.price) === minPrice;
                const isLargest   = selectedRooms.length > 1 && this.parseRoomArea(room.size) === maxArea && maxArea > 0;
                const permalink   = room.permalink || (`/phong-nghi/${room.id}/`);
                return `
                  <th class="dl-compare-room-th">
                    <div class="dl-compare-room-card-head">
                      <!-- Thanh đổi hạng phòng & nút xoá cột -->
                      <div class="dl-compare-switcher-bar">
                        <select onchange="App.changeCompareSlot(${idx}, this.value)" class="dl-compare-select" aria-label="Đổi hạng phòng cột ${idx + 1}">
                          ${allRooms.map(opt => `
                            <option value="${opt.id}" ${opt.id === room.id ? 'selected' : ''}>
                              ${this.decodeHTML(opt.name)}
                            </option>
                          `).join('')}
                        </select>
                        ${selectedRooms.length > 2 ? `
                          <button type="button" onclick="App.removeCompareRoom('${room.id}')" class="dl-compare-col-remove" title="Bỏ cột này">&times;</button>
                        ` : ''}
                      </div>

                      <!-- Ảnh & Nhãn gợi ý thông minh -->
                      <div class="dl-compare-img-wrap">
                        <a href="${permalink}">
                          <img src="${room.image}" alt="${this.decodeHTML(room.name)}" class="dl-compare-room-img">
                        </a>
                        <div class="dl-compare-smart-badges">
                          ${isBestPrice ? `<span class="dl-smart-badge dl-smart-badge-price inline-flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg><span>Giá tốt nhất</span></span>` : ''}
                          ${isLargest ? `<span class="dl-smart-badge dl-smart-badge-size inline-flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg><span>Rộng nhất</span></span>` : ''}
                          ${(!isBestPrice && !isLargest && room.badge) ? `<span class="dl-smart-badge dl-smart-badge-gold">${room.badge}</span>` : ''}
                        </div>
                      </div>

                      <h4 class="dl-compare-room-title">
                        <a href="${permalink}">${this.decodeHTML(room.name)}</a>
                      </h4>
                      <p class="dl-compare-room-sub">${this.decodeHTML(room.subtitle || '')}</p>
                    </div>
                  </th>
                `;
              }).join('')}
            </tr>
          </thead>
          <tbody>
            <!-- 1. Giá phòng / Đêm -->
            <tr class="${rowClass(diffPrice)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2" stroke-width="1.8"/><circle cx="12" cy="12" r="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 12h.01M18 12h.01"/></svg></span>
                <span>Giá phòng / Đêm</span>
              </td>
              ${selectedRooms.map(room => {
                const isBestPrice = selectedRooms.length > 1 && Number(room.price) === minPrice;
                const discountPct = (room.originalPrice && room.originalPrice > room.price)
                  ? Math.round((1 - room.price / room.originalPrice) * 100)
                  : 0;
                return `
                  <td class="dl-compare-val-td ${isBestPrice ? 'dl-compare-highlight-cell' : ''}">
                    ${room.originalPrice ? `<span class="text-xs line-through text-slate-400 mr-1.5">${this.formatVND(room.originalPrice)}</span>` : ''}
                    ${discountPct > 0 ? `<span class="dl-compare-save-pill">Tiết kiệm ${discountPct}%</span>` : ''}
                    <div class="text-lg font-extrabold text-amber-700 mt-0.5">${this.formatVND(room.price)}</div>
                    <span class="text-[11px] text-slate-500">Đã bao gồm Buffet sáng & Hồ bơi</span>
                  </td>
                `;
              }).join('')}
            </tr>

            <!-- 2. Diện tích phòng -->
            <tr class="${rowClass(diffSize)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></span>
                <span>Diện tích không gian</span>
              </td>
              ${selectedRooms.map(room => {
                const isLargest = selectedRooms.length > 1 && this.parseRoomArea(room.size) === maxArea && maxArea > 0;
                return `
                  <td class="dl-compare-val-td ${isLargest ? 'dl-compare-highlight-cell' : ''}">
                    <span class="font-bold text-navy-deep text-sm">${room.size || '28 - 32 m²'}</span>
                    ${isLargest ? `<span class="dl-compare-inline-tag">Không gian rộng nhất</span>` : ''}
                  </td>
                `;
              }).join('')}
            </tr>

            <!-- 3. Loại giường -->
            <tr class="${rowClass(diffBed)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7v11m0-4h18m0 4v-8a2 2 0 00-2-2H5a2 2 0 00-2 2m4-3h4a1 1 0 011 1v2H6V8a1 1 0 011-1zm8 0h4a1 1 0 011 1v2h-6V8a1 1 0 011-1z"></path></svg></span>
                <span>Cấu hình giường ngủ</span>
              </td>
              ${selectedRooms.map(room => `
                <td class="dl-compare-val-td font-semibold text-slate-800">${room.bed || '1 Giường đôi King'}</td>
              `).join('')}
            </tr>

            <!-- 4. Sức chứa tiêu chuẩn -->
            <tr class="${rowClass(diffOcc)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></span>
                <span>Sức chứa tối đa</span>
              </td>
              ${selectedRooms.map(room => `
                <td class="dl-compare-val-td font-semibold text-slate-700">${room.occupancy || '2 Người lớn + 1 Trẻ em'}</td>
              `).join('')}
            </tr>

            <!-- 5. Hướng nhìn (View) -->
            <tr class="${rowClass(diffView)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg></span>
                <span>Tầm nhìn (View)</span>
              </td>
              ${selectedRooms.map(room => `
                <td class="dl-compare-val-td">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-200/70 text-xs font-semibold">
                    ${room.view || 'Hướng thành phố'}
                  </span>
                </td>
              `).join('')}
            </tr>

            <!-- 6. Điểm đánh giá -->
            <tr class="${rowClass(diffRate)}">
              <td class="dl-compare-sticky-col dl-compare-label-td">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg></span>
                <span>Đánh giá khách lưu trú</span>
              </td>
              ${selectedRooms.map(room => {
                const isTopRated = selectedRooms.length > 1 && (parseFloat(room.rating) || 0) === maxRating;
                return `
                  <td class="dl-compare-val-td">
                    <span class="font-bold text-amber-600">★ ${room.rating || '4.8'}/5</span>
                    <span class="text-xs text-slate-500 ml-1">(${room.reviewsCount || 120} đánh giá)</span>
                    ${isTopRated ? `<span class="dl-compare-inline-tag">Được yêu thích nhất</span>` : ''}
                  </td>
                `;
              }).join('')}
            </tr>

            <!-- PHẦN TIỆN NGHI PHÒNG CAO CẤP -->
            <tr class="dl-compare-section-divider">
              <td colspan="${selectedRooms.length + 1}" class="dl-compare-section-td">
                <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg><span>So Sánh Tiện Nghi Phòng Cao Cấp</span></span>
              </td>
            </tr>

            ${allAmenities.map(am => {
              const presence = selectedRooms.map(r =>
                (r.amenities || []).some(ra => (ra.text || '').trim() === am.text)
              );
              const amHasDiff = new Set(presence).size > 1;
              return `
                <tr class="${rowClass(amHasDiff)}">
                  <td class="dl-compare-sticky-col dl-compare-label-td">
                    <span class="dl-compare-am-icon">${this.getAmenitySvg(am.icon, "w-4 h-4 text-gold")}</span>
                    <span>${am.text}</span>
                  </td>
                  ${presence.map(hasIt => `
                    <td class="dl-compare-val-td text-center">
                      ${hasIt
                        ? `<span class="dl-compare-yes">✓ Có sẵn</span>`
                        : `<span class="dl-compare-no">—</span>`
                      }
                    </td>
                  `).join('')}
                </tr>
              `;
            }).join('')}

            <!-- DÒNG HÀNH ĐỘNG ĐẶT PHÒNG -->
            <tr class="dl-compare-cta-row">
              <td class="dl-compare-sticky-col dl-compare-label-td font-bold text-navy-deep">
                <span class="dl-compare-label-icon text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                <span>Lựa chọn của bạn</span>
              </td>
              ${selectedRooms.map(room => {
                const permalink = room.permalink || (`/phong-nghi/${room.id}/`);
                return `
                  <td class="dl-compare-val-td">
                    <div class="dl-compare-cta-group">
                      <button type="button"
                              onclick="App.closeCompareModal(); window.BookingEngine && window.BookingEngine.openBookingModal('${room.id}')"
                              class="dl-compare-book-btn gold-shimmer-btn">
                        Đặt phòng này ngay
                      </button>
                      <a href="${permalink}" class="dl-compare-detail-link">
                        Xem chi tiết phòng &rarr;
                      </a>
                    </div>
                  </td>
                `;
              }).join('')}
            </tr>
          </tbody>
        </table>
      </div>
    `;

    container.innerHTML = html;
  },

  openRoomDetail(roomId) {
    this.openRoomDetailModal(roomId);
  },

  openRoomDetailModal(roomId) {
    const rooms = this.getRooms();
    const room = rooms.find(r => r.id === roomId) || rooms[0];
    if (!room) return;

    this.currentModalRoomId = room.id;

    const modal = document.getElementById('room-detail-modal');
    const img = document.getElementById('detail-room-img');
    const badge = document.getElementById('detail-room-badge');
    const name = document.getElementById('detail-room-name');
    const view = document.getElementById('detail-room-view');
    const origPrice = document.getElementById('detail-room-orig-price');
    const price = document.getElementById('detail-room-price');
    const size = document.getElementById('detail-room-size');
    const bed = document.getElementById('detail-room-bed');
    const occ = document.getElementById('detail-room-occ');
    const desc = document.getElementById('detail-room-desc');
    const amenitiesCont = document.getElementById('detail-room-amenities');

    if (img) img.src = room.image;
    if (badge) {
      if (room.badge) {
        badge.textContent = room.badge;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    }
    if (name) name.textContent = this.decodeHTML(room.name);
    if (view) view.textContent = room.view;
    if (origPrice) origPrice.textContent = room.originalPrice ? this.formatVND(room.originalPrice) : '';
    if (price) price.textContent = this.formatVND(room.price);
    if (size) size.textContent = room.size;
    if (bed) bed.textContent = room.bed;
    if (occ) occ.textContent = room.occupancy;
    if (desc) desc.textContent = this.decodeHTML(room.description);

    const viewPageBtn = document.getElementById('detail-view-page-btn');
    if (viewPageBtn) {
      viewPageBtn.href = room.permalink || (`/phong-nghi/${room.id}/`);
    }

    if (amenitiesCont && room.amenities) {
      amenitiesCont.innerHTML = room.amenities.map(a => `
        <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
          <div class="text-gold flex-shrink-0">${this.getAmenitySvg(a.icon, "w-4 h-4 text-gold")}</div>
          <span class="truncate">${a.text}</span>
        </div>
      `).join('');
    }

    if (modal) {
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
  },

  setupVRModal() {
    const modal = document.getElementById('vr-modal');
    const closeBtn = document.getElementById('close-vr-btn');
    const vrIframe = document.getElementById('vr-iframe');
    const vrLoader = document.getElementById('vr-loader');

    window.openVRModal = () => {
      const targetModal = document.getElementById('vr-modal');
      const targetIframe = document.getElementById('vr-iframe');
      const targetLoader = document.getElementById('vr-loader');
      const vrUrl = (targetModal && targetModal.dataset.vrUrl) || "https://dragonlegendhotel.com/vr/360.html";

      // Đóng drawer điều hướng mobile nếu đang mở
      const drawer = document.getElementById('mobile-drawer');
      const overlay = document.getElementById('drawer-overlay');
      if (drawer && !drawer.classList.contains('-translate-x-full')) {
        drawer.classList.add('-translate-x-full');
        if (overlay) overlay.classList.add('hidden');
      }

      if (targetModal) {
        targetModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      if (targetIframe) {
        if (targetLoader) targetLoader.classList.remove('hidden');

        // Nạp đúng URL VR 360 nếu chưa nạp
        if (targetIframe.getAttribute('src') !== vrUrl) {
          targetIframe.src = vrUrl;
          targetIframe.onload = () => {
            if (targetLoader) targetLoader.classList.add('hidden');
          };
          // Timeout dự phòng nếu trình duyệt chặn onload do cross-origin
          setTimeout(() => {
            if (targetLoader) targetLoader.classList.add('hidden');
          }, 3000);
        } else {
          if (targetLoader) targetLoader.classList.add('hidden');
        }
      }
    };

    window.closeVRModal = () => {
      const targetModal = document.getElementById('vr-modal');
      const targetIframe = document.getElementById('vr-iframe');
      const targetLoader = document.getElementById('vr-loader');

      if (targetModal) {
        targetModal.classList.add('hidden');
        document.body.style.overflow = '';
      }

      // Tạm dừng tải iframe ngầm để tiết kiệm pin/RAM khi đóng
      if (targetIframe) {
        targetIframe.src = 'about:blank';
      }
      if (targetLoader) {
        targetLoader.classList.remove('hidden');
      }
    };

    // Bắt sự kiện click toàn cục cho tất cả nút có class .open-vr-btn (web và mobile)
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.open-vr-btn');
      if (btn) {
        e.preventDefault();
        window.openVRModal();
      }
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.closeVRModal();
      });
    }

    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          window.closeVRModal();
        }
      });
    }

    // Đóng bằng phím ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
        window.closeVRModal();
      }
    });
  },

  initNavbar() {
    const navbar = document.getElementById('main-navbar');
    if (navbar) {
      navbar.classList.remove('bg-transparent', 'py-4');
    }

    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const closeDrawerBtn = document.getElementById('close-drawer-btn');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const drawerLinks = document.querySelectorAll('.mobile-nav-link');

    const handleScroll = () => {
      if (navbar) {
        navbar.classList.remove('bg-transparent', 'py-4');
      }
      if (window.scrollY > 40) {
        navbar?.classList.add('shadow-xl', 'py-2');
        navbar?.classList.remove('py-3');
      } else {
        navbar?.classList.remove('shadow-xl', 'py-2');
        navbar?.classList.add('py-3');
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    const toggleDrawer = (open) => {
      if (open) {
        mobileDrawer?.classList.remove('-translate-x-full');
        drawerOverlay?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      } else {
        mobileDrawer?.classList.add('-translate-x-full');
        drawerOverlay?.classList.add('hidden');
        document.body.style.overflow = '';
      }
    };

    mobileMenuBtn?.addEventListener('click', () => toggleDrawer(true));
    closeDrawerBtn?.addEventListener('click', () => toggleDrawer(false));
    drawerOverlay?.addEventListener('click', () => toggleDrawer(false));
    drawerLinks.forEach(link => link.addEventListener('click', () => toggleDrawer(false)));
  },

  setupBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
      if (window.scrollY > 400) {
        btn.classList.remove('opacity-0', 'invisible');
        btn.classList.add('opacity-100', 'visible');
      } else {
        btn.classList.add('opacity-0', 'invisible');
        btn.classList.remove('opacity-100', 'visible');
      }
    });

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  },

  setupStatsCounter() {
    const counters = document.querySelectorAll('.stats-counter');
    let animated = false;

    const runCount = () => {
      counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const isDecimal = counter.getAttribute('data-decimal') === 'true';
        let current = 0;
        const duration = 1800;
        const step = target / (duration / 25);

        const timer = setInterval(() => {
          current += step;
          if (current >= target) {
            counter.textContent = isDecimal ? target.toFixed(1) : Math.round(target);
            clearInterval(timer);
          } else {
            counter.textContent = isDecimal ? current.toFixed(1) : Math.round(current);
          }
        }, 25);
      });
    };

    const section = document.getElementById('stats-section');
    if (!section) return;

    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && !animated) {
        animated = true;
        runCount();
      }
    }, { threshold: 0.3 });

    observer.observe(section);
  },

  initSwiperCarousels() {
    if (!document.querySelector('.rooms-swiper, .offers-swiper, .testimonials-swiper')) return;
    if (typeof Swiper === 'undefined') {
      setTimeout(() => this.initSwiperCarousels(), 120);
      return;
    }

    // Rooms Swiper
    this.initRoomsSwiper();

    // Offers Swiper
    if (document.querySelector('.offers-swiper')) {
      new Swiper('.offers-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: false,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.offers-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.offers-next',
          prevEl: '.offers-prev',
        },
        breakpoints: {
          640: { slidesPerView: 2 },
          1024: { slidesPerView: 3 },
        }
      });
    }

    // Testimonials Swiper
    if (document.querySelector('.testimonials-swiper')) {
      new Swiper('.testimonials-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        autoplay: {
          delay: 6000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.testimonials-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.testimonials-next',
          prevEl: '.testimonials-prev',
        },
        breakpoints: {
          768: { slidesPerView: 2 },
          1024: { slidesPerView: 3 },
        }
      });
    }
  },

  initAOS() {
    if (window.innerWidth < 768) {
      document.querySelectorAll('[data-aos]').forEach(el => {
        el.removeAttribute('data-aos');
        el.removeAttribute('data-aos-delay');
      });
      return;
    }
    if (!document.querySelector('[data-aos]')) return;
    if (typeof AOS === 'undefined') {
      setTimeout(() => this.initAOS(), 120);
      return;
    }
    AOS.init({
      once: true,
      duration: 700,
      offset: 40,
      easing: 'ease-out-cubic',
      disable: function() {
        return window.innerWidth < 768;
      }
    });
  },

  setupNewsletterForm() {
    const form = document.getElementById('footer-newsletter-form');
    if (!form) return;

    const emailInput = document.getElementById('footer-newsletter-email');
    const submitBtn  = document.getElementById('footer-newsletter-btn');
    const btnText    = document.getElementById('footer-newsletter-btn-text');
    const msgBox     = document.getElementById('footer-newsletter-msg');

    const showMsg = (text, type = 'success') => {
      if (!msgBox) return;
      msgBox.className = 'text-xs py-2 px-3 rounded-lg leading-relaxed mt-2 block border ';
      if (type === 'success') {
        msgBox.className += 'bg-emerald-500/20 text-emerald-200 border-emerald-500/40';
      } else if (type === 'warning') {
        msgBox.className += 'bg-amber-500/20 text-amber-200 border-amber-500/40';
      } else {
        msgBox.className += 'bg-rose-500/20 text-rose-200 border-rose-500/40';
      }
      msgBox.innerHTML = text;
    };

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = emailInput ? emailInput.value.trim() : '';

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) {
        showMsg('Vui lòng nhập địa chỉ email hợp lệ (ví dụ: email@domain.com).', 'error');
        if (emailInput) emailInput.focus();
        return;
      }

      const originalText = btnText ? btnText.innerText : 'Đăng ký ngay';
      if (submitBtn) submitBtn.disabled = true;
      if (btnText) btnText.innerText = 'Đang gửi...';

      try {
        const ajaxUrl = (window.DragonLegendAjax && window.DragonLegendAjax.ajaxurl) ? window.DragonLegendAjax.ajaxurl : '/wp-admin/admin-ajax.php';
        const nonce   = (window.DragonLegendAjax && window.DragonLegendAjax.nonce) ? window.DragonLegendAjax.nonce : '';

        const formData = new URLSearchParams();
        formData.append('action', 'dl_subscribe_newsletter');
        formData.append('email', email);
        if (nonce) formData.append('nonce', nonce);

        const response = await fetch(ajaxUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: formData.toString()
        });

        const data = await response.json();

        if (data.success) {
          if (data.data && data.data.already_subscribed) {
            showMsg(data.data.message || 'Email này đã đăng ký trước đó. Mã ưu đãi của bạn đã sẵn sàng sử dụng.', 'warning');
          } else {
            showMsg(data.data.message || 'Đăng ký thành công! Mã ưu đãi đã được gửi tới email của bạn.', 'success');
            if (emailInput) emailInput.value = '';
          }
        } else {
          showMsg((data.data && data.data.message) ? data.data.message : 'Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        }
      } catch (err) {
        showMsg('Không thể kết nối máy chủ. Vui lòng thử lại sau.', 'error');
      } finally {
        if (submitBtn) submitBtn.disabled = false;
        if (btnText) btnText.innerText = originalText;
      }
    });
  }
};

window.App = App;
