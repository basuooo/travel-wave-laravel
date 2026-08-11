<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? ($setting->maintenance_title_ar ?: 'الموقع تحت الصيانة') : ($setting->maintenance_title_en ?: 'Site Under Maintenance') }} - {{ app()->getLocale() == 'ar' ? $setting->site_name_ar : $setting->site_name_en }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @php
        $template = $setting->maintenance_template ?? 'glassmorphism';
        $title = app()->getLocale() == 'ar' ? ($setting->maintenance_title_ar ?: 'الموقع تحت الصيانة والتطوير') : ($setting->maintenance_title_en ?: 'We will be back soon!');
        $message = app()->getLocale() == 'ar' ? ($setting->maintenance_message_ar ?: 'نقوم حالياً بإجراء تحديثات وتحسينات على النظام لتقديم أفضل خدمة لكم. نسعد بتواصلكم معنا فوراً.') : ($setting->maintenance_message_en ?: 'We are currently performing scheduled maintenance to serve you better. Feel free to contact us.');
        $whatsapp = !empty($setting->whatsapp_number) ? preg_replace('/[^0-9]/', '', $setting->whatsapp_number) : null;
        $phone = $setting->phone ?? null;
        $endTime = $setting->maintenance_end_time ? \Carbon\Carbon::parse($setting->maintenance_end_time)->toISOString() : null;
    @endphp

    <style>
        :root {
            --primary-color: {{ $setting->primary_color ?: '#12395b' }};
            --secondary-color: {{ $setting->secondary_color ?: '#ff8c32' }};
            --accent-color: {{ $setting->accent_color ?: '#ff8c32' }};
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }}; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; overflow-x: hidden; position: relative; }

        /* Template 1: Glassmorphism */
        body.tpl-glassmorphism { background: linear-gradient(135deg, #0b1e36 0%, #12395b 50%, #0d2843 100%); color: #fff; }
        .tpl-glassmorphism .card-wrapper { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 28px; padding: 50px 40px; max-width: 680px; width: 100%; text-align: center; box-shadow: 0 30px 60px rgba(0, 0, 0, 0.35); position: relative; z-index: 10; }
        .tpl-glassmorphism .ambient-glow { position: absolute; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255, 140, 50, 0.25) 0%, rgba(18, 57, 91, 0) 70%); border-radius: 50%; top: 20%; left: 50%; transform: translate(-50%, -50%); animation: pulseGlow 8s infinite alternate ease-in-out; pointer-events: none; }
        @keyframes pulseGlow { 0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.5; } 100% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.9; } }
        .tpl-glassmorphism .icon-badge { width: 84px; height: 84px; background: linear-gradient(135deg, var(--secondary-color), #ef5c00); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 38px; color: #ffffff; margin-bottom: 25px; box-shadow: 0 12px 30px rgba(255, 140, 50, 0.4); animation: gearRotate 12s linear infinite; }
        @keyframes gearRotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* Template 2: Minimal Countdown */
        body.tpl-minimal_countdown { background: #0f172a; color: #f8fafc; }
        .tpl-minimal_countdown .card-wrapper { max-width: 720px; width: 100%; text-align: center; padding: 40px 20px; }
        .countdown-box { display: flex; justify-content: center; gap: 15px; margin: 30px 0; flex-wrap: wrap; }
        .countdown-item { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 18px 24px; min-width: 100px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .countdown-value { font-size: 2.2rem; font-weight: 800; color: var(--secondary-color); display: block; line-height: 1; }
        .countdown-label { font-size: 0.85rem; color: #94a3b8; margin-top: 6px; display: block; text-transform: uppercase; font-weight: 600; }

        /* Template 3: Travel Agency Hero */
        body.tpl-agency_hero { background: linear-gradient(rgba(18, 57, 91, 0.92), rgba(11, 30, 54, 0.95)), url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; color: #fff; }
        .tpl-agency_hero .card-wrapper { max-width: 750px; width: 100%; text-align: center; background: rgba(11, 30, 54, 0.65); backdrop-filter: blur(12px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); padding: 50px 35px; box-shadow: 0 25px 50px rgba(0,0,0,0.4); }
        .agency-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 140, 50, 0.15); border: 1px solid var(--secondary-color); color: var(--secondary-color); padding: 8px 18px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; margin-bottom: 20px; }

        /* Common Elements */
        .brand-logo { max-height: 80px; width: auto; margin-bottom: 25px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3)); }
        .title { font-size: 2rem; font-weight: 800; margin-bottom: 15px; line-height: 1.3; }
        .message { font-size: 1.1rem; opacity: 0.9; line-height: 1.7; margin-bottom: 35px; }
        .contact-buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 35px; }
        .btn-custom { padding: 14px 28px; border-radius: 50px; font-weight: 700; font-size: 1rem; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s ease; }
        .btn-whatsapp { background-color: #25D366; color: #ffffff; box-shadow: 0 8px 20px rgba(37, 211, 102, 0.35); }
        .btn-whatsapp:hover { background-color: #1da851; transform: translateY(-3px); color: #ffffff; }
        .btn-phone { background-color: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25); }
        .btn-phone:hover { background-color: rgba(255, 255, 255, 0.25); transform: translateY(-3px); color: #ffffff; }
        .social-links { display: flex; justify-content: center; gap: 15px; padding-top: 25px; border-top: 1px solid rgba(255, 255, 255, 0.12); }
        .social-link { width: 44px; height: 44px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; transition: all 0.3s ease; }
        .social-link:hover { background: var(--secondary-color); transform: translateY(-3px); color: #ffffff; }

        @media (max-width: 576px) { .title { font-size: 1.6rem; } .message { font-size: 1rem; } }
    </style>
</head>
<body class="tpl-{{ $template }}">

    @if($template === 'glassmorphism')
        <div class="ambient-glow"></div>
    @endif

    <div class="card-wrapper">
        <!-- Logo -->
        @if(!empty($setting->header_logo_path) || !empty($setting->logo_path))
            <img src="{{ asset('storage/' . ($setting->header_logo_path ?: $setting->logo_path)) }}" alt="{{ $setting->site_name_ar }}" class="brand-logo">
        @endif

        @if($template === 'glassmorphism')
            <div class="icon-badge">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
        @elseif($template === 'agency_hero')
            <div class="agency-badge">
                <i class="bi bi-airplane-fill"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'تحديث وتطوير نظام الحجوزات' : 'System Upgrade & Maintenance' }}</span>
            </div>
        @endif

        <!-- Maintenance Title -->
        <h1 class="title">{{ $title }}</h1>

        <!-- Maintenance Message -->
        <p class="message">{{ $message }}</p>

        <!-- Countdown for Template 2 (minimal_countdown) or if end time is set -->
        @if(($template === 'minimal_countdown' || $endTime) && $endTime)
            <div class="countdown-box" id="countdownBox">
                <div class="countdown-item"><span class="countdown-value" id="cd-days">00</span><span class="countdown-label">{{ app()->getLocale() == 'ar' ? 'يوم' : 'Days' }}</span></div>
                <div class="countdown-item"><span class="countdown-value" id="cd-hours">00</span><span class="countdown-label">{{ app()->getLocale() == 'ar' ? 'ساعة' : 'Hours' }}</span></div>
                <div class="countdown-item"><span class="countdown-value" id="cd-minutes">00</span><span class="countdown-label">{{ app()->getLocale() == 'ar' ? 'دقيقة' : 'Mins' }}</span></div>
                <div class="countdown-item"><span class="countdown-value" id="cd-seconds">00</span><span class="countdown-label">{{ app()->getLocale() == 'ar' ? 'ثانية' : 'Secs' }}</span></div>
            </div>
        @endif

        <!-- Direct Contact Buttons -->
        <div class="contact-buttons">
            @if($whatsapp)
                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="btn-custom btn-whatsapp">
                    <i class="bi bi-whatsapp fs-5"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'تواصل عبر الواتساب' : 'WhatsApp Us' }}</span>
                </a>
            @endif

            @if($phone)
                <a href="tel:{{ $phone }}" class="btn-custom btn-phone">
                    <i class="bi bi-telephone-fill fs-5"></i>
                    <span>{{ $phone }}</span>
                </a>
            @endif
        </div>

        <!-- Social Links -->
        <div class="social-links">
            @if(!empty($setting->facebook_url)) <a href="{{ $setting->facebook_url }}" target="_blank" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a> @endif
            @if(!empty($setting->instagram_url)) <a href="{{ $setting->instagram_url }}" target="_blank" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a> @endif
            @if(!empty($setting->twitter_url)) <a href="{{ $setting->twitter_url }}" target="_blank" class="social-link" title="X / Twitter"><i class="bi bi-twitter-x"></i></a> @endif
            @if(!empty($setting->youtube_url)) <a href="{{ $setting->youtube_url }}" target="_blank" class="social-link" title="YouTube"><i class="bi bi-youtube"></i></a> @endif
            @if(!empty($setting->tiktok_url)) <a href="{{ $setting->tiktok_url }}" target="_blank" class="social-link" title="TikTok"><i class="bi bi-tiktok"></i></a> @endif
        </div>
    </div>

    @if($endTime)
    <script>
        const targetDate = new Date("{{ $endTime }}").getTime();
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                document.getElementById('cd-days').innerText = '00';
                document.getElementById('cd-hours').innerText = '00';
                document.getElementById('cd-minutes').innerText = '00';
                document.getElementById('cd-seconds').innerText = '00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('cd-days').innerText = String(days).padStart(2, '0');
            document.getElementById('cd-hours').innerText = String(hours).padStart(2, '0');
            document.getElementById('cd-minutes').innerText = String(minutes).padStart(2, '0');
            document.getElementById('cd-seconds').innerText = String(seconds).padStart(2, '0');
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
    @endif

</body>
</html>
