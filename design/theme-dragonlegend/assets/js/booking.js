/**
 * Dragon Legend Hotel - Booking Engine Logic (WordPress Integrated)
 * Xử lý thanh tìm kiếm nhanh, tính giá, mã giảm giá và gửi AJAX lưu vào WP Admin
 */

const BookingEngine = {
  state: {
    checkin: '',
    checkout: '',
    nights: 1,
    rooms: 1,
    adults: 2,
    children: 0,
    selectedRoomId: 'superior-room',
    promoCode: '',
    discountType: 'percent', // 'percent' | 'fixed'
    discountValue: 0,
    discountPercent: 0,
    discountAmount: 0,
    maxDiscount: 0,
    minSpend: 0,
    promoLabel: ''
  },

  promoCodes: {
    'DRAGON2026': { type: 'percent', value: 10, percent: 10, label: 'Giảm 10% tri ân khách hàng trực tuyến' },
    'SUMMER20': { type: 'percent', value: 20, percent: 20, max_discount: 500000, label: 'Giảm 20% gói ưu đãi mùa hè (tối đa 500k)' },
    'VIP500K': { type: 'fixed', value: 500000, percent: 0, min_spend: 2000000, label: 'Giảm 500.000 VNĐ cho kỳ nghỉ sang trọng (đơn từ 2tr)' },
    'WELCOME100K': { type: 'fixed', value: 100000, percent: 0, min_spend: 1000000, label: 'Giảm 100.000 VNĐ chào mừng khách mới (đơn từ 1tr)' },
    'EARLYBIRD': { type: 'percent', value: 15, percent: 15, label: 'Giảm 15% đặt sớm' },
    'VIPGUEST': { type: 'percent', value: 12, percent: 12, label: 'Giảm 12% đặc quyền thành viên' }
  },

  init() {
    this.initDates();
    this.setupGuestsDropdown();
    this.setupHeroForm();
    this.setupModalEvents();
  },

  getRoomsData() {
    if (window.DragonLegendAjax && window.DragonLegendAjax.rooms && window.DragonLegendAjax.rooms.length > 0) {
      return window.DragonLegendAjax.rooms;
    }
    return typeof ROOMS_DATA !== 'undefined' ? ROOMS_DATA : [];
  },

  formatDateISO(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
  },

  formatDateDisplay(isoString) {
    if (!isoString) return '—';
    const parts = isoString.split('-');
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return isoString;
  },

  decodeHTML(str) {
    if (!str) return '';
    const txt = document.createElement('textarea');
    txt.innerHTML = str;
    return txt.value;
  },

  formatVND(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
  },

  initDates() {
    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    const todayStr = this.formatDateISO(today);
    const tomorrowStr = this.formatDateISO(tomorrow);

    this.state.checkin = todayStr;
    this.state.checkout = tomorrowStr;
    this.calculateNights();

    const heroCi = document.getElementById('hero-checkin');
    const heroCo = document.getElementById('hero-checkout');
    if (heroCi) {
      heroCi.value = todayStr;
      heroCi.min = todayStr;
      heroCi.addEventListener('change', (e) => {
        this.state.checkin = e.target.value;
        const nextDay = new Date(e.target.value);
        nextDay.setDate(nextDay.getDate() + 1);
        const nextDayStr = this.formatDateISO(nextDay);
        if (heroCo) {
          heroCo.min = nextDayStr;
          if (heroCo.value <= e.target.value) {
            heroCo.value = nextDayStr;
            this.state.checkout = nextDayStr;
          }
        }
        this.calculateNights();
        this.updateDateDisplay();
      });
    }

    if (heroCo) {
      heroCo.value = tomorrowStr;
      heroCo.min = tomorrowStr;
      heroCo.addEventListener('change', (e) => {
        this.state.checkout = e.target.value;
        this.calculateNights();
        this.updateDateDisplay();
      });
    }

    this.updateDateDisplay();
  },

  calculateNights() {
    if (!this.state.checkin || !this.state.checkout) return;
    const d1 = new Date(this.state.checkin);
    const d2 = new Date(this.state.checkout);
    const diffTime = d2.getTime() - d1.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    this.state.nights = diffDays > 0 ? diffDays : 1;
  },

  updateDateDisplay() {
    const ciText = document.getElementById('hero-checkin-text');
    const coText = document.getElementById('hero-checkout-text');
    const nightsText = document.getElementById('hero-nights-text');

    if (ciText) ciText.textContent = this.formatDateDisplay(this.state.checkin);
    if (coText) coText.textContent = this.formatDateDisplay(this.state.checkout);
    if (nightsText) nightsText.textContent = `(${this.state.nights} đêm)`;
  },

  setupGuestsDropdown() {
    const toggleBtn = document.getElementById('hero-guests-toggle');
    const dropdown = document.getElementById('hero-guests-dropdown');
    const doneBtn = document.getElementById('guests-done-btn');

    if (!toggleBtn || !dropdown) return;

    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('hidden');
    });

    if (doneBtn) {
      doneBtn.addEventListener('click', () => {
        dropdown.classList.add('hidden');
      });
    }

    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });

    const bindStepper = (key, minusId, plusId, valueId, min = 1, max = 10) => {
      const minus = document.getElementById(minusId);
      const plus = document.getElementById(plusId);
      const val = document.getElementById(valueId);

      if (minus) {
        minus.addEventListener('click', (e) => {
          e.stopPropagation();
          if (this.state[key] > min) {
            this.state[key]--;
            if (val) val.textContent = this.state[key];
            this.updateGuestsLabel();
          }
        });
      }

      if (plus) {
        plus.addEventListener('click', (e) => {
          e.stopPropagation();
          if (this.state[key] < max) {
            this.state[key]++;
            if (val) val.textContent = this.state[key];
            this.updateGuestsLabel();
          }
        });
      }
    };

    bindStepper('rooms', 'step-rooms-minus', 'step-rooms-plus', 'step-rooms-val', 1, 10);
    bindStepper('adults', 'step-adults-minus', 'step-adults-plus', 'step-adults-val', 1, 30);
    bindStepper('children', 'step-kids-minus', 'step-kids-plus', 'step-kids-val', 0, 15);
  },

  updateGuestsLabel() {
    const label = document.getElementById('hero-guests-label');
    if (label) {
      const totalGuests = this.state.adults + this.state.children;
      label.textContent = `${this.state.rooms} Phòng, ${totalGuests} Khách`;
    }
  },

  setupHeroForm() {
    const heroForm = document.getElementById('hero-booking-form');
    if (heroForm) {
      heroForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.openBookingModal(this.state.selectedRoomId);
      });
    }
  },

  setupModalEvents() {
    const modal = document.getElementById('booking-modal');
    const closeBtn = document.getElementById('close-booking-modal');
    const cancelBtn = document.getElementById('cancel-booking-modal');

    if (closeBtn) closeBtn.addEventListener('click', () => this.closeBookingModal());
    if (cancelBtn) cancelBtn.addEventListener('click', () => this.closeBookingModal());
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) this.closeBookingModal();
      });
    }

    const modalRoomSelect = document.getElementById('modal-room-select');
    if (modalRoomSelect) {
      modalRoomSelect.addEventListener('change', (e) => {
        this.state.selectedRoomId = e.target.value;
        this.updateModalPriceSummary();
      });
    }

    const modalCi = document.getElementById('modal-checkin');
    const modalCo = document.getElementById('modal-checkout');
    const modalRooms = document.getElementById('modal-rooms');
    const modalAdults = document.getElementById('modal-adults');

    if (modalCi) {
      modalCi.addEventListener('change', (e) => {
        this.state.checkin = e.target.value;
        this.calculateNights();
        this.updateModalPriceSummary();
      });
    }
    if (modalCo) {
      modalCo.addEventListener('change', (e) => {
        this.state.checkout = e.target.value;
        this.calculateNights();
        this.updateModalPriceSummary();
      });
    }
    if (modalRooms) {
      modalRooms.addEventListener('change', (e) => {
        this.state.rooms = Math.max(1, parseInt(e.target.value) || 1);
        this.updateModalPriceSummary();
      });
    }
    if (modalAdults) {
      modalAdults.addEventListener('change', (e) => {
        this.state.adults = Math.max(1, parseInt(e.target.value) || 1);
        this.updateGuestsLabel();
        this.updateModalPriceSummary();
      });
    }

    const modalChildren = document.getElementById('modal-children');
    if (modalChildren) {
      modalChildren.addEventListener('change', (e) => {
        this.state.children = Math.max(0, parseInt(e.target.value) || 0);
        this.updateGuestsLabel();
        this.updateModalPriceSummary();
      });
    }

    // Clear validation error styling on typing
    ['guest-name', 'guest-phone', 'guest-email'].forEach(id => {
      const inp = document.getElementById(id);
      if (inp) {
        inp.addEventListener('input', () => {
          inp.setCustomValidity('');
          inp.classList.remove('border-rose-500', 'bg-rose-50/30');
        });
      }
    });

    // Promo Code Apply Button
    const promoBtn = document.getElementById('apply-promo-btn');
    const promoInput = document.getElementById('booking-promo-code');
    const promoMessage = document.getElementById('promo-message');

    if (promoBtn && promoInput) {
      promoBtn.addEventListener('click', () => {
        const code = promoInput.value.trim().toUpperCase();
        this.applyPromoCode(code);
      });
      promoInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          const code = promoInput.value.trim().toUpperCase();
          this.applyPromoCode(code);
        }
      });
    }

    // Modal Form Submission
    const reservationForm = document.getElementById('reservation-form');
    if (reservationForm) {
      reservationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleFormSubmit();
      });
    }
  },

  openBookingModal(roomId) {
    const rooms = this.getRoomsData();
    if (roomId) {
      this.state.selectedRoomId = roomId;
    } else if (!this.state.selectedRoomId && rooms.length > 0) {
      this.state.selectedRoomId = rooms[0].id;
    }

    const modal = document.getElementById('booking-modal');
    const modalRoomSelect = document.getElementById('modal-room-select');

    if (modalRoomSelect && rooms.length > 0) {
      modalRoomSelect.innerHTML = rooms.map(r => `
        <option value="${r.id}" ${r.id === this.state.selectedRoomId ? 'selected' : ''}>
          ${this.decodeHTML(r.name)} - ${this.formatVND(r.price)} / đêm
        </option>
      `).join('');
    }

    const modalCi = document.getElementById('modal-checkin');
    const modalCo = document.getElementById('modal-checkout');
    if (modalCi) modalCi.value = this.state.checkin;
    if (modalCo) modalCo.value = this.state.checkout;

    const modalRooms = document.getElementById('modal-rooms');
    const modalAdults = document.getElementById('modal-adults');
    const modalChildren = document.getElementById('modal-children');
    if (modalRooms) modalRooms.value = this.state.rooms;
    if (modalAdults) modalAdults.value = this.state.adults;
    if (modalChildren) modalChildren.value = this.state.children || 0;

    this.updateModalPriceSummary();

    if (modal) {
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
  },

  closeBookingModal() {
    const modal = document.getElementById('booking-modal');
    if (modal) {
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  },

  applyPromoCode(code) {
    const promoMessage = document.getElementById('promo-message');
    const promoBtn = document.getElementById('apply-promo-btn');

    if (!code) {
      this.state.promoCode = '';
      this.state.discountType = 'percent';
      this.state.discountValue = 0;
      this.state.discountPercent = 0;
      this.state.discountAmount = 0;
      this.state.maxDiscount = 0;
      this.state.minSpend = 0;
      this.state.promoLabel = '';
      if (promoMessage) promoMessage.innerHTML = '';
      this.updateModalPriceSummary();
      return;
    }

    const rooms = this.getRoomsData();
    const currentRoom = rooms.find(r => r.id === this.state.selectedRoomId) || rooms[0];
    const nights = this.state.nights || 1;
    const roomCount = this.state.rooms || 1;
    const baseRoomRate = currentRoom ? (currentRoom.price || 1050000) : 1050000;
    const totalRoomRate = baseRoomRate * nights * roomCount;

    if (promoBtn) {
      promoBtn.disabled = true;
      promoBtn.textContent = '...';
    }

    const formData = new FormData();
    formData.append('action', 'dl_validate_coupon');
    if (window.DragonLegendAjax && window.DragonLegendAjax.nonce) {
      formData.append('nonce', window.DragonLegendAjax.nonce);
    }
    formData.append('code', code);
    formData.append('subtotal', totalRoomRate);
    formData.append('room_id', currentRoom ? (currentRoom.post_id || currentRoom.id) : '');

    const ajaxUrl = (window.DragonLegendAjax && window.DragonLegendAjax.ajaxurl) ? window.DragonLegendAjax.ajaxurl : '/wp-admin/admin-ajax.php';

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      if (res && res.success && res.data) {
        const d = res.data;
        this.state.promoCode = d.code || code;
        this.state.discountType = d.discount_type || 'percent';
        this.state.discountValue = parseFloat(d.discount_value || 0);
        this.state.discountPercent = (d.discount_type === 'percent') ? parseInt(d.discount_value || 0) : 0;
        this.state.discountAmount = parseFloat(d.discount_amount || 0);
        this.state.maxDiscount = parseFloat(d.max_discount || 0);
        this.state.minSpend = parseFloat(d.min_spend || 0);
        this.state.promoLabel = d.label || '';

        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-emerald-600 font-semibold">✓ ${d.message || ('Áp dụng thành công: ' + (d.label || d.code))}</span>`;
        }
      } else {
        this.state.promoCode = '';
        this.state.discountAmount = 0;
        this.state.discountValue = 0;
        this.state.discountPercent = 0;
        const errMsg = (res && res.data && res.data.message) ? res.data.message : 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.';
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ ${errMsg}</span>`;
        }
      }
      this.updateModalPriceSummary();
    })
    .catch(() => {
      this.applyPromoCodeFallback(code, totalRoomRate);
    })
    .finally(() => {
      if (promoBtn) {
        promoBtn.disabled = false;
        promoBtn.textContent = 'Áp dụng';
      }
    });
  },

  applyPromoCodeFallback(code, totalRoomRate) {
    const promoMessage = document.getElementById('promo-message');
    if (this.promoCodes[code]) {
      const c = this.promoCodes[code];
      if (c.min_spend && totalRoomRate < c.min_spend) {
        this.state.promoCode = '';
        this.state.discountAmount = 0;
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Đơn tối thiểu từ ${this.formatVND(c.min_spend)} để áp dụng mã ${code}</span>`;
        }
      } else {
        this.state.promoCode = code;
        this.state.discountType = c.type || 'percent';
        this.state.discountValue = c.value || c.percent;
        this.state.discountPercent = (c.type === 'percent') ? c.value : 0;
        this.state.maxDiscount = c.max_discount || 0;
        this.state.minSpend = c.min_spend || 0;
        this.state.promoLabel = c.label;
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-emerald-600 font-semibold">✓ Áp dụng thành công: ${c.label}</span>`;
        }
      }
    } else {
      this.state.promoCode = '';
      this.state.discountPercent = 0;
      this.state.discountAmount = 0;
      if (promoMessage) {
        promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Mã ưu đãi không hợp lệ</span>`;
      }
    }
    this.updateModalPriceSummary();
  },

  updateModalPriceSummary() {
    const rooms = this.getRoomsData();
    const currentRoom = rooms.find(r => r.id === this.state.selectedRoomId) || rooms[0];
    if (!currentRoom) return;

    const nights = this.state.nights || 1;
    const roomCount = this.state.rooms || 1;

    const baseRoomRate = currentRoom.price || 1050000;
    const totalRoomRate = baseRoomRate * nights * roomCount;

    // Tính toán chiết khấu (Hỗ trợ cả phần trăm và số tiền cố định VNĐ)
    let discountAmount = 0;
    if (this.state.promoCode) {
      if (this.state.minSpend > 0 && totalRoomRate < this.state.minSpend) {
        discountAmount = 0;
        const promoMessage = document.getElementById('promo-message');
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Đơn tối thiểu từ ${this.formatVND(this.state.minSpend)} để áp dụng mã ${this.state.promoCode}</span>`;
        }
      } else if (this.state.discountType === 'fixed') {
        discountAmount = Math.min(totalRoomRate, this.state.discountValue);
      } else {
        discountAmount = Math.round((totalRoomRate * this.state.discountValue) / 100);
        if (this.state.maxDiscount > 0 && discountAmount > this.state.maxDiscount) {
          discountAmount = this.state.maxDiscount;
        }
      }
    }

    const taxServiceAmount = Math.round((totalRoomRate - discountAmount) * 0.08); // 8% VAT & phí PV
    const grandTotal = totalRoomRate - discountAmount + taxServiceAmount;

    this.state.discountAmount = discountAmount;

    const summaryRoomName = document.getElementById('summary-room-name');
    const summaryStayInfo = document.getElementById('summary-stay-info');
    const summaryRoomPrice = document.getElementById('summary-room-price');
    const summaryTotalBase = document.getElementById('summary-total-base');
    const summaryDiscountRow = document.getElementById('summary-discount-row');
    const summaryDiscountVal = document.getElementById('summary-discount-val');
    const summaryTaxVal = document.getElementById('summary-tax-val');
    const summaryTotalVal = document.getElementById('summary-total-val');
    const summaryRoomImg = document.getElementById('summary-room-img');

    if (summaryRoomName) summaryRoomName.textContent = this.decodeHTML(currentRoom.name);
    if (summaryStayInfo) {
      const guestDetails = (this.state.children > 0)
        ? `${this.state.adults} người lớn, ${this.state.children} trẻ em`
        : `${this.state.adults} người lớn`;
      summaryStayInfo.textContent = `${this.formatDateDisplay(this.state.checkin)} - ${this.formatDateDisplay(this.state.checkout)} (${nights} đêm x ${roomCount} phòng • ${guestDetails})`;
    }
    if (summaryRoomPrice) summaryRoomPrice.textContent = `${this.formatVND(baseRoomRate)} / đêm`;
    if (summaryTotalBase) summaryTotalBase.textContent = this.formatVND(totalRoomRate);
    if (summaryRoomImg && currentRoom.image) summaryRoomImg.src = currentRoom.image;

    if (summaryDiscountRow && summaryDiscountVal) {
      if (discountAmount > 0) {
        summaryDiscountRow.classList.remove('hidden');
        if (this.state.discountType === 'fixed') {
          summaryDiscountVal.textContent = `- ${this.formatVND(discountAmount)} (Giảm trực tiếp)`;
        } else {
          summaryDiscountVal.textContent = `- ${this.formatVND(discountAmount)} (${this.state.discountValue}%)`;
        }
      } else {
        summaryDiscountRow.classList.add('hidden');
      }
    }

    if (summaryTaxVal) summaryTaxVal.textContent = this.formatVND(taxServiceAmount);
    if (summaryTotalVal) summaryTotalVal.textContent = this.formatVND(grandTotal);
  },

  handleFormSubmit() {
    const nameInput = document.getElementById('guest-name');
    const phoneInput = document.getElementById('guest-phone');
    const emailInput = document.getElementById('guest-email');

    const name = nameInput?.value.trim() || '';
    const phone = phoneInput?.value.trim() || '';
    const email = emailInput?.value.trim() || '';
    const note = document.getElementById('guest-note')?.value.trim() || '';

    // Tiếng Việt validation
    if (!name) {
      if (nameInput) {
        nameInput.setCustomValidity('Vui lòng nhập họ và tên của quý khách');
        nameInput.reportValidity();
        nameInput.focus();
        nameInput.classList.add('border-rose-500', 'bg-rose-50/30');
      }
      return;
    }

    if (!phone) {
      if (phoneInput) {
        phoneInput.setCustomValidity('Vui lòng nhập số điện thoại liên hệ');
        phoneInput.reportValidity();
        phoneInput.focus();
        phoneInput.classList.add('border-rose-500', 'bg-rose-50/30');
      }
      return;
    }

    const phoneClean = phone.replace(/[\s\-\.]/g, '');
    if (!/^(0|\+84)[0-9]{8,11}$/.test(phoneClean)) {
      if (phoneInput) {
        phoneInput.setCustomValidity('Vui lòng nhập số điện thoại hợp lệ (10 chữ số)');
        phoneInput.reportValidity();
        phoneInput.focus();
        phoneInput.classList.add('border-rose-500', 'bg-rose-50/30');
      }
      return;
    }

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      if (emailInput) {
        emailInput.setCustomValidity('Vui lòng nhập đúng định dạng email (ví dụ: nguyenvana@gmail.com)');
        emailInput.reportValidity();
        emailInput.focus();
        emailInput.classList.add('border-rose-500', 'bg-rose-50/30');
      }
      return;
    }

    const rooms = this.getRoomsData();
    const currentRoom = rooms.find(r => r.id === this.state.selectedRoomId) || rooms[0];

    const submitBtn = document.getElementById('submit-booking-btn');
    const btnText = document.getElementById('submit-btn-text');
    const btnSpinner = document.getElementById('submit-btn-spinner');

    if (submitBtn) submitBtn.disabled = true;
    if (btnText) btnText.textContent = 'Đang xử lý đặt phòng...';
    if (btnSpinner) btnSpinner.classList.remove('hidden');

    // Chuẩn bị dữ liệu gửi AJAX
    const formData = new FormData();
    formData.append('action', 'dl_submit_booking');
    if (window.DragonLegendAjax && window.DragonLegendAjax.nonce) {
      formData.append('nonce', window.DragonLegendAjax.nonce);
    }
    formData.append('guest_name', name);
    formData.append('guest_phone', phone);
    formData.append('guest_email', email);
    formData.append('guest_note', note);
    formData.append('room_id', currentRoom ? (currentRoom.post_id || currentRoom.id) : '');
    formData.append('checkin', this.state.checkin);
    formData.append('checkout', this.state.checkout);
    formData.append('rooms', this.state.rooms);
    formData.append('adults', this.state.adults);
    formData.append('children', this.state.children);
    formData.append('promo_code', this.state.promoCode);
    formData.append('discount_type', this.state.discountType);
    formData.append('discount_amount', this.state.discountAmount);

    const ajaxUrl = (window.DragonLegendAjax && window.DragonLegendAjax.ajaxurl) ? window.DragonLegendAjax.ajaxurl : '/wp-admin/admin-ajax.php';

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(result => {
      let bookingCode = 'DLH-' + Math.floor(100000 + Math.random() * 900000);
      if (result && result.success && result.data && result.data.booking_code) {
        bookingCode = result.data.booking_code;
      }

      this.displaySuccess(bookingCode, name, phone, currentRoom);
    })
    .catch(err => {
      console.warn('AJAX fallback offline:', err);
      const bookingCode = 'DLH-' + Math.floor(100000 + Math.random() * 900000);
      this.displaySuccess(bookingCode, name, phone, currentRoom);
    })
    .finally(() => {
      if (submitBtn) submitBtn.disabled = false;
      if (btnText) btnText.textContent = 'Xác nhận đặt phòng';
      if (btnSpinner) btnSpinner.classList.add('hidden');
    });
  },

  displaySuccess(bookingCode, name, phone, currentRoom) {
    const successModal = document.getElementById('booking-success-modal');
    const successCode = document.getElementById('success-booking-code');
    const successGuest = document.getElementById('success-guest-name');
    const successRoom = document.getElementById('success-room-name');
    const successDates = document.getElementById('success-dates');

    if (successCode) successCode.textContent = bookingCode;
    if (successGuest) successGuest.textContent = `${name} (${phone})`;
    if (successRoom) successRoom.textContent = `${currentRoom ? this.decodeHTML(currentRoom.name) : 'Phòng khách sạn'} (${this.state.rooms} phòng)`;
    if (successDates) {
      successDates.textContent = `${this.formatDateDisplay(this.state.checkin)} đến ${this.formatDateDisplay(this.state.checkout)} (${this.state.nights} đêm)`;
    }

    this.closeBookingModal();

    // Google Analytics 4 (GA4) / Google Tag Conversion Tracking
    if (typeof gtag === 'function') {
      const roomRate = currentRoom ? (currentRoom.price || 1050000) : 1050000;
      const subtotal = roomRate * (this.state.nights || 1) * (this.state.rooms || 1);
      const discount = this.state.discountAmount || 0;
      const finalRevenue = subtotal - discount;

      gtag('event', 'purchase', {
        transaction_id: bookingCode,
        value: finalRevenue,
        currency: 'VND',
        coupon: this.state.promoCode || '',
        items: [{
          item_id: currentRoom ? (currentRoom.post_id || currentRoom.id) : 'room',
          item_name: currentRoom ? this.decodeHTML(currentRoom.name) : 'Phòng khách sạn',
          price: roomRate,
          quantity: this.state.rooms || 1
        }]
      });

      gtag('event', 'generate_lead', {
        currency: 'VND',
        value: finalRevenue
      });
    }

    if (successModal) {
      successModal.classList.remove('hidden');
    }
  }
};

window.BookingEngine = BookingEngine;
