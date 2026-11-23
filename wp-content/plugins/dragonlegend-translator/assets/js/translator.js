/**
 * Dragon Legend Auto Translator - JavaScript Engine
 * Xử lý dịch tự động, lưu giữ phiên người dùng và đồng bộ nút bấm ngôn ngữ
 */
(function() {
    'use strict';

    var STORAGE_KEY = 'dl_session_lang';
    var COOKIE_KEY = 'googtrans';

    var LANG_LABELS = {
        'vi': {
            'vi': 'Tiếng Việt',
            'en': 'Tiếng Anh',
            'zh-CN': 'Tiếng Trung'
        },
        'en': {
            'vi': 'Vietnamese',
            'en': 'English',
            'zh-CN': 'Chinese'
        },
        'zh-CN': {
            'vi': '越南语',
            'en': '英语',
            'zh-CN': '中文'
        }
    };

    // 1. Helper: Đọc giá trị Cookie
    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    // 2. Helper: Xóa Cookie đa cấp độ (Host, Domain, Root path)
    function clearTranslateCookies() {
        var domains = [
            '',
            window.location.hostname,
            '.' + window.location.hostname,
            window.location.hostname.replace(/^www\./, ''),
            '.' + window.location.hostname.replace(/^www\./, '')
        ];
        var paths = ['/', '/wp-admin', ''];

        domains.forEach(function(dom) {
            paths.forEach(function(p) {
                var dStr = dom ? '; domain=' + dom : '';
                var pStr = p ? '; path=' + p : '; path=/';
                document.cookie = COOKIE_KEY + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC' + pStr + dStr;
                document.cookie = 'dl_session_lang=; expires=Thu, 01 Jan 1970 00:00:00 UTC' + pStr + dStr;
            });
        });

        try {
            sessionStorage.removeItem(STORAGE_KEY);
        } catch (e) {}
    }

    // 3. Helper: Thiết lập Cookie phiên (Session Cookie - tự hủy khi tắt trình duyệt)
    function setSessionCookie(lang) {
        var transVal = '/vi/' + lang;
        // Session cookie không có tham số 'expires' -> hết hạn khi đóng trình duyệt
        document.cookie = COOKIE_KEY + '=' + transVal + '; path=/; SameSite=Lax';
        document.cookie = COOKIE_KEY + '=' + transVal + '; domain=' + window.location.hostname + '; path=/; SameSite=Lax';
        document.cookie = 'dl_session_lang=' + lang + '; path=/; SameSite=Lax';
        try {
            sessionStorage.setItem(STORAGE_KEY, lang);
        } catch (e) {}
    }

    // 4. Lấy ngôn ngữ hiện tại
    function getCurrentLang() {
        // Kiểm tra sessionStorage trước
        var sLang = null;
        try {
            sLang = sessionStorage.getItem(STORAGE_KEY);
        } catch (e) {}
        if (sLang) return sLang;

        // Kiểm tra cookie googtrans (/vi/en, /vi/zh-CN)
        var cookie = getCookie(COOKIE_KEY);
        if (cookie) {
            var parts = cookie.split('/');
            if (parts.length >= 3 && parts[2]) {
                return parts[2];
            }
        }

        // Mặc định là Tiếng Việt
        return 'vi';
    }

    // 5. Cập nhật trạng thái Active và tên ngôn ngữ hiển thị trên các nút
    function updateActiveButtons(lang) {
        var activeLang = LANG_LABELS[lang] ? lang : 'vi';
        var labels = LANG_LABELS[activeLang];
        var btns = document.querySelectorAll('.dl-lang-btn');
        btns.forEach(function(btn) {
            var btnLang = btn.getAttribute('data-lang');
            if (btnLang === activeLang) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
            if (btnLang && labels[btnLang]) {
                var labelEl = btn.querySelector('.dl-lang-label') || btn.querySelector('span:not(.flag-emoji)');
                if (labelEl) {
                    labelEl.textContent = labels[btnLang];
                }
                btn.setAttribute('title', labels[btnLang]);
                btn.setAttribute('aria-label', labels[btnLang]);
            }
        });
    }

    var gtLoading = false;

    // 5b. Chỉ nạp Google Translate API khi người dùng cần dịch hoặc di chuột vào vùng chuyển ngôn ngữ
    function ensureGoogleTranslateLoaded() {
        if (gtLoading || (window.google && window.google.translate)) {
            return;
        }
        gtLoading = true;
        var s = document.createElement('script');
        s.src = 'https://translate.google.com/translate_a/element.js?cb=dlGoogleTranslateInit';
        s.async = true;
        s.defer = true;
        document.head.appendChild(s);
    }

    // 6. Kích hoạt dịch qua Google Translate
    function triggerGoogleTranslate(lang) {
        if (lang === 'vi') {
            clearTranslateCookies();
            updateActiveButtons('vi');
            // Để khôi phục nguyên bản Tiếng Việt không lỗi DOM, reload trang
            window.location.reload();
            return;
        }

        setSessionCookie(lang);
        updateActiveButtons(lang);
        ensureGoogleTranslateLoaded();

        var select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = lang;
            select.dispatchEvent(new Event('change'));
        } else {
            // Nếu widget chưa mount xong, thử lại sau 250ms hoặc tải lại trang với cookie đã đặt
            var attempts = 0;
            var interval = setInterval(function() {
                attempts++;
                var s = document.querySelector('.goog-te-combo');
                if (s) {
                    clearInterval(interval);
                    s.value = lang;
                    s.dispatchEvent(new Event('change'));
                } else if (attempts > 12) {
                    clearInterval(interval);
                    window.location.reload();
                }
            }, 250);
        }
    }

    // 7. Gắn sự kiện click và hover nạp sớm vào các nút ngôn ngữ
    function initButtons() {
        document.addEventListener('mouseover', function(e) {
            if (e.target && e.target.closest && e.target.closest('.dl-lang-btn, .dl-lang-switcher-wrap, .dl-lang-switcher-mobile')) {
                ensureGoogleTranslateLoaded();
            }
        }, { passive: true });

        document.addEventListener('touchstart', function(e) {
            if (e.target && e.target.closest && e.target.closest('.dl-lang-btn, .dl-lang-switcher-wrap, .dl-lang-switcher-mobile')) {
                ensureGoogleTranslateLoaded();
            }
        }, { passive: true });

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.dl-lang-btn');
            if (!btn) return;

            e.preventDefault();
            var targetLang = btn.getAttribute('data-lang');
            var curLang = getCurrentLang();

            if (targetLang === curLang && targetLang !== 'vi') {
                updateActiveButtons(targetLang);
                return;
            }

            triggerGoogleTranslate(targetLang);
        });
    }

    // 8. Tự động tiêm vị trí nếu theme chưa gắn (Phía sau Tour VR 360°)
    function ensurePlacement() {
        var existing = document.querySelector('.dl-lang-switcher-wrap');
        if (existing) return;

        // Tìm link Tour VR 360 trong topbar
        var allLinks = document.querySelectorAll('aside a');
        var vrLink = null;
        allLinks.forEach(function(a) {
            if ((a.textContent && a.textContent.indexOf('Tour VR') !== -1) || (a.getAttribute('href') && a.getAttribute('href').indexOf('vr') !== -1)) {
                vrLink = a;
            }
        });

        if (vrLink && vrLink.parentNode) {
            var wrap = document.createElement('div');
            wrap.className = 'dl-lang-switcher-wrap inline-flex items-center gap-1.5 ml-2 notranslate';
            wrap.setAttribute('translate', 'no');
            wrap.innerHTML = '<span class="text-slate-600 hidden lg:inline">|</span>' +
                '<div class="dl-lang-switcher">' +
                '<button type="button" data-lang="vi" class="dl-lang-btn active" title="Tiếng Việt"><span class="flag-emoji"><svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#DA251D"/><path d="M12 3.6L13.347 7.746H17.706L14.18 10.308L15.527 14.454L12 11.892L8.473 14.454L9.82 10.308L6.294 7.746H10.653L12 3.6Z" fill="#FFFF00"/></svg></span><span class="dl-lang-label">Tiếng Việt</span></button>' +
                '<span class="dl-lang-divider">|</span>' +
                '<button type="button" data-lang="en" class="dl-lang-btn" title="Tiếng Anh"><span class="flag-emoji"><svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#012169"/><path d="M0 0L24 18M24 0L0 18" stroke="#FFFFFF" stroke-width="3.2"/><path d="M0 0L24 18M24 0L0 18" stroke="#C8102E" stroke-width="1.6"/><path d="M12 0V18M0 9H24" stroke="#FFFFFF" stroke-width="5.2"/><path d="M12 0V18M0 9H24" stroke="#C8102E" stroke-width="3.2"/></svg></span><span class="dl-lang-label">Tiếng Anh</span></button>' +
                '<span class="dl-lang-divider">|</span>' +
                '<button type="button" data-lang="zh-CN" class="dl-lang-btn" title="Tiếng Trung"><span class="flag-emoji"><svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#EE1C25"/><path d="M5.4 2.7L6.18 5.1H8.7L6.66 6.58L7.44 8.98L5.4 7.5L3.36 8.98L4.14 6.58L2.1 5.1H4.62L5.4 2.7Z" fill="#FFFF00"/><circle cx="10.2" cy="2.4" r="0.75" fill="#FFFF00"/><circle cx="11.8" cy="4.2" r="0.75" fill="#FFFF00"/><circle cx="11.8" cy="6.8" r="0.75" fill="#FFFF00"/><circle cx="10.2" cy="8.6" r="0.75" fill="#FFFF00"/></svg></span><span class="dl-lang-label">Tiếng Trung</span></button>' +
                '</div>';
            vrLink.parentNode.insertBefore(wrap, vrLink.nextSibling);
        }
    }

    // 9. Callback khởi tạo Google Translate Element
    window.dlGoogleTranslateInit = function() {
        if (!window.google || !window.google.translate || !window.google.translate.TranslateElement) {
            return;
        }

        new window.google.translate.TranslateElement({
            pageLanguage: 'vi',
            includedLanguages: 'vi,en,zh-CN',
            autoDisplay: false,
            multilanguagePage: true
        }, 'dl_google_translate_element');

        // Đồng bộ trạng thái active với ngôn ngữ phiên
        var current = getCurrentLang();
        updateActiveButtons(current);

        // Nếu phiên trước đó đang là tiếng Anh hoặc tiếng Trung, kích hoạt combo
        if (current && current !== 'vi') {
            setTimeout(function() {
                var select = document.querySelector('.goog-te-combo');
                if (select && select.value !== current) {
                    select.value = current;
                    select.dispatchEvent(new Event('change'));
                }
            }, 300);
        }
    };

    // Khởi chạy khi DOM sẵn sàng
    function bootstrapTranslator() {
        ensurePlacement();
        initButtons();
        var cur = getCurrentLang();
        updateActiveButtons(cur);
        if (cur && cur !== 'vi') {
            ensureGoogleTranslateLoaded();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrapTranslator);
    } else {
        bootstrapTranslator();
    }
})();
