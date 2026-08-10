<!-- 🎯 Popup Manager Front-end Runtime Engine -->
<div id="popupRuntimeContainer" style="position: fixed; z-index: 99999999; pointer-events: none; inset: 0; display: none;"></div>

<style>
    .tw-popup-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center; z-index: 99999999; pointer-events: auto;
        opacity: 0; transition: opacity 0.3s ease; padding: 1rem;
    }
    .tw-popup-overlay.active { opacity: 1; }
    .tw-popup-box {
        background: #ffffff; color: #111827; border-radius: 20px; width: 100%; max-width: 520px;
        position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .tw-popup-overlay.active .tw-popup-box { transform: scale(1); }
    .tw-popup-close-btn {
        position: absolute; top: 12px; right: 12px; width: 34px; height: 34px; border-radius: 50%;
        background: rgba(0,0,0,0.1); border: none; font-size: 18px; font-weight: bold; cursor: pointer;
        display: flex; align-items: center; justify-content: center; color: #374151; z-index: 100;
        transition: all 0.2s;
    }
    .tw-popup-close-btn:hover { background: rgba(0,0,0,0.2); color: #000; }
</style>

<script>
(function() {
    const currentUrl = window.location.href;
    const isMobile = window.innerWidth < 768;
    const deviceType = isMobile ? 'mobile' : 'desktop';

    const apiUrl = `/api/v1/popups/runtime?url=${encodeURIComponent(currentUrl)}&device=${deviceType}`;

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.popups || data.popups.length === 0) return;

            const popup = data.popups[0];
            if (!popup) return;

            const freqMode = popup.frequency ? popup.frequency.mode : 'once_per_session';
            if (freqMode === 'once_per_session' && sessionStorage.getItem('tw_popup_' + popup.id)) return;
            if (freqMode === 'once_ever' && localStorage.getItem('tw_popup_' + popup.id)) return;

            const trigger = popup.trigger || {};
            const mode = trigger.mode || 'random_time';

            let delayMs = 0;
            if (mode === 'random_time') {
                const minSec = parseInt(trigger.min_delay_seconds) || 20;
                const maxSec = parseInt(trigger.max_delay_seconds) || 60;
                const selectedSec = Math.floor(Math.random() * (maxSec - minSec + 1)) + minSec;
                delayMs = selectedSec * 1000;
            } else if (mode === 'delay') {
                delayMs = (parseInt(trigger.delay_seconds) || 10) * 1000;
            } else if (mode === 'immediately') {
                delayMs = 100;
            }

            if (mode === 'exit_intent') {
                document.addEventListener('mouseleave', function onExit(e) {
                    if (e.clientY <= 0) {
                        document.removeEventListener('mouseleave', onExit);
                        renderAndShowPopup(popup);
                    }
                });
            } else {
                setTimeout(function() {
                    renderAndShowPopup(popup);
                }, delayMs);
            }
        })
        .catch(err => console.error('Popup Manager Error:', err));

    function renderAndShowPopup(popup) {
        let container = document.getElementById('popupRuntimeContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'popupRuntimeContainer';
            container.style.cssText = 'position: fixed; z-index: 99999999; pointer-events: none; inset: 0;';
            document.body.appendChild(container);
        }

        container.style.display = 'block';

        const overlay = document.createElement('div');
        overlay.className = 'tw-popup-overlay';
        overlay.id = 'twPopupOverlay_' + popup.id;

        const box = document.createElement('div');
        box.className = 'tw-popup-box';

        const closeBtn = document.createElement('button');
        closeBtn.className = 'tw-popup-close-btn';
        closeBtn.innerHTML = '&times;';
        closeBtn.onclick = function(e) {
            e.stopPropagation();
            handleCloseAttempt(overlay, popup, box);
        };

        const content = document.createElement('div');
        content.innerHTML = popup.html || '';

        box.appendChild(closeBtn);
        box.appendChild(content);
        overlay.appendChild(box);
        container.appendChild(overlay);

        overlay.onclick = function(e) {
            if (e.target === overlay && popup.overlay && popup.overlay.close_on_click !== false) {
                handleCloseAttempt(overlay, popup, box);
            }
        };

        document.addEventListener('keydown', function escClose(e) {
            if (e.key === 'Escape') {
                document.removeEventListener('keydown', escClose);
                handleCloseAttempt(overlay, popup, box);
            }
        });

        requestAnimationFrame(() => {
            overlay.classList.add('active');
        });

        sessionStorage.setItem('tw_popup_' + popup.id, '1');
        localStorage.setItem('tw_popup_' + popup.id, '1');

        trackPopupEvent(popup.id, 'impression');

        // Click actions & URL redirects
        box.addEventListener('click', function(e) {
            const btn = e.target.closest('a, button');
            if (btn && btn !== closeBtn) {
                trackPopupEvent(popup.id, 'click');
                const href = btn.getAttribute('href');
                if (href && href !== '#' && !href.startsWith('javascript:')) {
                    if (href.startsWith('#')) {
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth' });
                            closePopupImmediately(overlay, popup.id);
                        }
                    } else {
                        window.open(href, btn.target || '_self');
                        closePopupImmediately(overlay, popup.id);
                    }
                }
            }
        });

        const form = box.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                trackPopupEvent(popup.id, 'conversion');
            });
        }
    }

    function handleCloseAttempt(overlay, popup, box) {
        const exitWarning = popup.exit_warning || {};
        if (exitWarning.enable && !box.dataset.warningShown) {
            box.dataset.warningShown = "true";

            box.innerHTML = `
                <div class="p-4 text-center">
                    <div class="display-3 mb-2">⚠️</div>
                    <h3 class="fw-bold text-danger mb-2">${exitWarning.title || 'هل أنت متأكد من الإلغاء؟'}</h3>
                    <p class="text-muted mb-4">${exitWarning.msg || 'ستخسر الخصم والعرض المتاح حالياً بمجرد الإغلاق!'}</p>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-success btn-lg rounded-pill fw-bold" id="stayClaimBtn">الاستمرار والاستفادة بالخصم 🎁</button>
                        <button type="button" class="btn btn-link text-muted" id="confirmLeaveBtn">إلغاء وخسارة العرض الآن</button>
                    </div>
                </div>
            `;

            const stayBtn = box.querySelector('#stayClaimBtn');
            const leaveBtn = box.querySelector('#confirmLeaveBtn');

            if (stayBtn) {
                stayBtn.onclick = function() {
                    const btnUrl = popup.structure ? (popup.structure.btn_url || '#lead-form') : '#lead-form';
                    if (btnUrl.startsWith('#')) {
                        const target = document.querySelector(btnUrl);
                        if (target) target.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        window.open(btnUrl, '_self');
                    }
                    closePopupImmediately(overlay, popup.id);
                };
            }

            if (leaveBtn) {
                leaveBtn.onclick = function() {
                    closePopupImmediately(overlay, popup.id);
                };
            }

            return;
        }

        closePopupImmediately(overlay, popup.id);
    }

    function closePopupImmediately(overlay, popupId) {
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.remove();
        }, 300);
        trackPopupEvent(popupId, 'close');
    }

    function trackPopupEvent(popupId, eventType) {
        fetch('/api/v1/popups/track', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                popup_id: popupId,
                event_type: eventType,
                page_url: window.location.href,
                device: isMobile ? 'mobile' : 'desktop'
            })
        }).catch(() => {});
    }
})();
</script>
