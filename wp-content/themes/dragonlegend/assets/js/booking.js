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
    selectedRoomId: 'superior-twin-room',
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
    'DRAGON2026': { type: 'percent', value: 10, percent: 10, label: 'Giảm 10% tri ân khách hàng đặt phòng trực tuyến' },
    'SUMMER20': { type: 'percent', value: 20, percent: 20, max_discount: 500000, min_spend: 1000000, label: 'Giảm 20% gói ưu đãi mùa hè (tối đa 500.000đ)' },
    'VIP500K': { type: 'fixed', value: 500000, percent: 0, min_spend: 2000000, label: 'Giảm trực tiếp 500.000 VNĐ cho kỳ nghỉ sang trọng' },
    'WELCOME100K': { type: 'fixed', value: 100000, percent: 0, min_spend: 1000000, label: 'Giảm ngay 100.000 VNĐ cho lần đặt phòng đầu tiên' },
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

  findRoom(roomId) {
    const rooms = this.getRoomsData();
    if (!rooms || rooms.length === 0) return null;
    if (roomId) {
      const target = String(roomId).trim();
      const matched = rooms.find(r => r.id === target || String(r.post_id) === target);
      if (matched) return matched;
    }
    return rooms.find(r => r.id === 'superior-twin-room') || rooms[0];
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

  formatVND(amount, unit = 'VNĐ') {
    const val = Math.round(Number(amount) || 0);
    return new Intl.NumberFormat('vi-VN').format(val) + ' ' + unit;
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
        if (this.state.promoCode) {
          this.applyPromoCode(this.state.promoCode);
        } else {
          this.updateModalPriceSummary();
        }
      });
    }

    const modalCi = document.getElementById('modal-checkin');
    const modalCo = document.getElementById('modal-checkout');
    const modalRooms = document.getElementById('modal-rooms');
    const modalAdults = document.getElementById('modal-adults');

    if (modalCi) {
      modalCi.addEventListener('change', (e) => {
        this.state.checkin = e.target.value;
        const ciDate = new Date(this.state.checkin);
        const coDate = new Date(this.state.checkout);
        if (coDate <= ciDate) {
          const nextDay = new Date(ciDate);
          nextDay.setDate(nextDay.getDate() + 1);
          this.state.checkout = this.formatDateInput(nextDay);
          if (modalCo) modalCo.value = this.state.checkout;
        }
        if (modalCo) {
          const minCo = new Date(ciDate);
          minCo.setDate(minCo.getDate() + 1);
          modalCo.min = this.formatDateInput(minCo);
        }
        const heroCi = document.getElementById('hero-checkin');
        const heroCo = document.getElementById('hero-checkout');
        if (heroCi) heroCi.value = this.state.checkin;
        if (heroCo) heroCo.value = this.state.checkout;
        this.calculateNights();
        this.updateModalPriceSummary();
      });
    }
    if (modalCo) {
      modalCo.addEventListener('change', (e) => {
        this.state.checkout = e.target.value;
        const ciDate = new Date(this.state.checkin);
        const coDate = new Date(this.state.checkout);
        if (coDate <= ciDate) {
          const nextDay = new Date(ciDate);
          nextDay.setDate(nextDay.getDate() + 1);
          this.state.checkout = this.formatDateInput(nextDay);
          modalCo.value = this.state.checkout;
        }
        const heroCo = document.getElementById('hero-checkout');
        if (heroCo) heroCo.value = this.state.checkout;
        this.calculateNights();
        this.updateModalPriceSummary();
      });
    }
    if (modalRooms) {
      ['input', 'change'].forEach(evt => {
        modalRooms.addEventListener(evt, (e) => {
          this.state.rooms = Math.max(1, parseInt(e.target.value, 10) || 1);
          this.updateGuestsLabel();
          this.updateModalPriceSummary();
        });
      });
    }
    if (modalAdults) {
      ['input', 'change'].forEach(evt => {
        modalAdults.addEventListener(evt, (e) => {
          this.state.adults = Math.max(1, parseInt(e.target.value, 10) || 1);
          this.updateGuestsLabel();
          this.updateModalPriceSummary();
        });
      });
    }

    const modalChildren = document.getElementById('modal-children');
    if (modalChildren) {
      ['input', 'change'].forEach(evt => {
        modalChildren.addEventListener(evt, (e) => {
          this.state.children = Math.max(0, parseInt(e.target.value, 10) || 0);
          this.updateGuestsLabel();
          this.updateModalPriceSummary();
        });
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

    // Promo Code Apply Button & Input Sync
    const promoBtn = document.getElementById('apply-promo-btn');
    const promoInput = document.getElementById('booking-promo-code');

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
      promoInput.addEventListener('input', () => {
        const currentVal = promoInput.value.trim().toUpperCase();
        if (!currentVal) {
          this.clearPromoCode(false);
        } else if (this.state.promoCode && currentVal !== this.state.promoCode) {
          this.state.promoCode = '';
          this.state.discountValue = 0;
          this.state.discountPercent = 0;
          this.state.discountAmount = 0;
          const promoMessage = document.getElementById('promo-message');
          if (promoMessage) promoMessage.innerHTML = '';
          this.updateModalPriceSummary();
        }
      });
      promoInput.addEventListener('change', () => {
        const currentVal = promoInput.value.trim().toUpperCase();
        if (!currentVal) {
          this.clearPromoCode(false);
        } else if (currentVal !== this.state.promoCode) {
          this.applyPromoCode(currentVal);
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

  clearPromoCode(clearInput = true) {
    this.state.promoCode = '';
    this.state.discountType = 'percent';
    this.state.discountValue = 0;
    this.state.discountPercent = 0;
    this.state.discountAmount = 0;
    this.state.maxDiscount = 0;
    this.state.minSpend = 0;
    this.state.promoLabel = '';

    const promoInput = document.getElementById('booking-promo-code');
    if (clearInput && promoInput) {
      promoInput.value = '';
    }
    const promoMessage = document.getElementById('promo-message');
    if (promoMessage) {
      promoMessage.innerHTML = '';
    }
    this.updateModalPriceSummary();
  },

  renderPromoSuccessMessage(text) {
    const promoMessage = document.getElementById('promo-message');
    if (!promoMessage) return;
    promoMessage.innerHTML = `
      <span class="inline-flex items-center gap-2 text-emerald-600 font-semibold">
        <span>✓ ${text}</span>
        <button type="button" onclick="BookingEngine.clearPromoCode(true)" class="text-xs text-rose-500 hover:text-rose-700 underline font-medium ml-1">[Bỏ mã]</button>
      </span>
    `;
  },

  openBookingModal(roomId) {
    const rooms = this.getRoomsData();
    const matchedRoom = this.findRoom(roomId || this.state.selectedRoomId);
    if (matchedRoom) {
      this.state.selectedRoomId = matchedRoom.id;
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

    // Đồng bộ trạng thái ô mã voucher với state
    const promoInput = document.getElementById('booking-promo-code');
    if (promoInput) {
      const inputCode = promoInput.value.trim().toUpperCase();
      if (!inputCode && this.state.promoCode) {
        this.clearPromoCode(false);
      } else if (inputCode && inputCode !== this.state.promoCode) {
        this.applyPromoCode(inputCode);
      }
    }

    this.calculateNights();
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
    const cleanCode = (code || '').trim().toUpperCase();

    if (!cleanCode) {
      this.clearPromoCode(false);
      return;
    }

    const currentRoom = this.findRoom(this.state.selectedRoomId);
    const nights = Math.max(1, parseInt(this.state.nights, 10) || 1);
    const roomCount = Math.max(1, parseInt(this.state.rooms, 10) || 1);
    const baseRoomRate = currentRoom ? (Number(currentRoom.price) || 1050000) : 1050000;
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
    formData.append('code', cleanCode);
    formData.append('subtotal', totalRoomRate);
    formData.append('room_id', currentRoom ? (currentRoom.post_id || currentRoom.id) : '');

    const ajaxUrl = (window.DragonLegendAjax && window.DragonLegendAjax.ajaxurl) ? window.DragonLegendAjax.ajaxurl : '/wp-admin/admin-ajax.php';

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      // Nếu nonce cache trả về -1 hoặc phản hồi không đúng cấu trúc JSON object, chuyển sang fallback
      if (res === -1 || res === '-1' || typeof res !== 'object' || res === null) {
        this.applyPromoCodeFallback(cleanCode, totalRoomRate);
        return;
      }

      if (res.success && res.data) {
        const d = res.data;
        this.state.promoCode = d.code || cleanCode;
        this.state.discountType = d.discount_type || 'percent';
        this.state.discountValue = parseFloat(d.discount_value || 0);
        this.state.discountPercent = (d.discount_type === 'percent') ? parseFloat(d.discount_value || 0) : 0;
        this.state.discountAmount = parseFloat(d.discount_amount || 0);
        this.state.maxDiscount = parseFloat(d.max_discount || 0);
        this.state.minSpend = parseFloat(d.min_spend || 0);
        this.state.promoLabel = d.label || '';

        this.renderPromoSuccessMessage(d.message || ('Áp dụng thành công: ' + (d.label || d.code)));
      } else {
        this.state.promoCode = '';
        this.state.discountAmount = 0;
        this.state.discountValue = 0;
        this.state.discountPercent = 0;
        const errMsg = (res.data && res.data.message) ? res.data.message : 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.';
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ ${errMsg}</span>`;
        }
      }
      this.updateModalPriceSummary();
    })
    .catch(() => {
      this.applyPromoCodeFallback(cleanCode, totalRoomRate);
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
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Đơn tối thiểu từ ${this.formatVND(c.min_spend, 'VNĐ')} để áp dụng mã ${code}</span>`;
        }
      } else {
        this.state.promoCode = code;
        this.state.discountType = c.type || 'percent';
        this.state.discountValue = parseFloat(c.value || c.percent || 0);
        this.state.discountPercent = (c.type === 'percent') ? parseFloat(c.value || c.percent || 0) : 0;
        this.state.maxDiscount = parseFloat(c.max_discount || 0);
        this.state.minSpend = parseFloat(c.min_spend || 0);
        this.state.promoLabel = c.label || code;
        this.renderPromoSuccessMessage(`Áp dụng thành công: ${c.label || code}`);
      }
    } else {
      this.state.promoCode = '';
      this.state.discountValue = 0;
      this.state.discountPercent = 0;
      this.state.discountAmount = 0;
      if (promoMessage) {
        promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Mã ưu đãi không hợp lệ hoặc đã hết hạn.</span>`;
      }
    }
    this.updateModalPriceSummary();
  },

  updateModalPriceSummary() {
    const currentRoom = this.findRoom(this.state.selectedRoomId);
    if (!currentRoom) return;

    const nights = Math.max(1, parseInt(this.state.nights, 10) || 1);
    const roomCount = Math.max(1, parseInt(this.state.rooms, 10) || 1);

    // 1. Tiền phòng tạm tính = Giá phòng/đêm × Số đêm × Số phòng
    const baseRoomRate = Number(currentRoom.price) || 1050000;
    const totalRoomRate = baseRoomRate * nights * roomCount;

    // 2. Chiết khấu voucher (khi có voucher hợp lệ)
    let discountAmount = 0;
    if (this.state.promoCode && this.state.discountValue > 0) {
      if (this.state.minSpend > 0 && totalRoomRate < this.state.minSpend) {
        discountAmount = 0;
        const promoMessage = document.getElementById('promo-message');
        if (promoMessage) {
          promoMessage.innerHTML = `<span class="text-rose-500 font-semibold">✕ Đơn tối thiểu từ ${this.formatVND(this.state.minSpend, 'VNĐ')} để áp dụng mã ${this.state.promoCode}</span>`;
        }
      } else {
        if (this.state.discountType === 'fixed') {
          discountAmount = Math.min(totalRoomRate, this.state.discountValue);
        } else {
          discountAmount = Math.round((totalRoomRate * this.state.discountValue) / 100);
          if (this.state.maxDiscount > 0 && discountAmount > this.state.maxDiscount) {
            discountAmount = this.state.maxDiscount;
          }
        }
        discountAmount = Math.min(totalRoomRate, Math.max(0, discountAmount));
        // Khôi phục thông báo thành công nếu trước đó bị cảnh báo min_spend
        const promoMessage = document.getElementById('promo-message');
        if (promoMessage && promoMessage.textContent.includes('Đơn tối thiểu')) {
          this.renderPromoSuccessMessage(`Áp dụng thành công: ${this.state.promoLabel || this.state.promoCode}`);
        }
      }
    }

    // 3. Tiền phòng sau chiết khấu & Thuế VAT + Phí dịch vụ (8%)
    // - Khi chưa có voucher: afterDiscount = totalRoomRate -> Tổng = Tiền phòng tạm tính + 8% VAT
    // - Khi có voucher: afterDiscount = totalRoomRate - discountAmount -> Tổng = (Tiền phòng tạm tính - Voucher) + 8% VAT
    const afterDiscount = Math.max(0, totalRoomRate - discountAmount);
    const taxServiceAmount = Math.round(afterDiscount * 0.08);
    const grandTotal = afterDiscount + taxServiceAmount;

    this.state.discountAmount = discountAmount;

    const summaryRoomName = document.getElementById('summary-room-name');
    const summaryStayInfo = document.getElementById('summary-stay-info');
    const summaryRoomPrice = document.getElementById('summary-room-price');
    const summaryBaseLabel = document.getElementById('summary-base-label');
    const summaryTotalBase = document.getElementById('summary-total-base');
    const summaryDiscountRow = document.getElementById('summary-discount-row');
    const summaryDiscountLabel = document.getElementById('summary-discount-label');
    const summaryDiscountVal = document.getElementById('summary-discount-val');
    const summaryAfterDiscountRow = document.getElementById('summary-after-discount-row');
    const summaryAfterDiscountVal = document.getElementById('summary-after-discount-val');
    const summaryTaxVal = document.getElementById('summary-tax-val');
    const summaryTotalVal = document.getElementById('summary-total-val');
    const summaryRoomImg = document.getElementById('summary-room-img');

    if (summaryRoomName) summaryRoomName.textContent = this.decodeHTML(currentRoom.name);
    if (summaryStayInfo) {
      const guestDetails = (this.state.children > 0)
        ? `${this.state.adults} người lớn, ${this.state.children} trẻ em`
        : `${this.state.adults} người lớn`;
      summaryStayInfo.textContent = `${this.formatDateDisplay(this.state.checkin)} - ${this.formatDateDisplay(this.state.checkout)} (${nights} đêm × ${roomCount} phòng • ${guestDetails})`;
    }
    if (summaryRoomPrice) summaryRoomPrice.textContent = `${this.formatVND(baseRoomRate, 'VNĐ')} / đêm`;
    if (summaryBaseLabel) summaryBaseLabel.textContent = `Tiền phòng tạm tính (${nights} đêm × ${roomCount} phòng):`;
    if (summaryTotalBase) summaryTotalBase.textContent = this.formatVND(totalRoomRate, 'VNĐ');
    if (summaryRoomImg && currentRoom.image) summaryRoomImg.src = currentRoom.image;

    if (summaryDiscountRow && summaryDiscountVal) {
      if (discountAmount > 0 && this.state.promoCode) {
        summaryDiscountRow.classList.remove('hidden');
        if (summaryDiscountLabel) {
          summaryDiscountLabel.textContent = this.state.discountType === 'fixed'
            ? `Voucher giảm giá (${this.state.promoCode}):`
            : `Voucher giảm giá (${this.state.promoCode} - ${this.state.discountValue}%):`;
        }
        summaryDiscountVal.textContent = `- ${this.formatVND(discountAmount, 'VNĐ')}`;

        if (summaryAfterDiscountRow && summaryAfterDiscountVal) {
          summaryAfterDiscountRow.classList.remove('hidden');
          summaryAfterDiscountVal.textContent = this.formatVND(afterDiscount, 'VNĐ');
        }
      } else {
        summaryDiscountRow.classList.add('hidden');
        if (summaryAfterDiscountRow) {
          summaryAfterDiscountRow.classList.add('hidden');
        }
      }
    }

    if (summaryTaxVal) summaryTaxVal.textContent = this.formatVND(taxServiceAmount, 'VNĐ');
    if (summaryTotalVal) summaryTotalVal.textContent = this.formatVND(grandTotal, 'VNĐ');
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

    const currentRoom = this.findRoom(this.state.selectedRoomId);

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
      if (result && typeof result === 'object' && result.success && result.data && result.data.booking_code) {
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
      const roomRate = currentRoom ? (Number(currentRoom.price) || 1050000) : 1050000;
      const subtotal = roomRate * (this.state.nights || 1) * (this.state.rooms || 1);
      const discount = this.state.discountAmount || 0;
      const afterDiscount = Math.max(0, subtotal - discount);
      const finalRevenue = afterDiscount + Math.round(afterDiscount * 0.08);

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
