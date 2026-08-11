<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? ($setting->maintenance_title_ar ?: 'الموقع تحت الصيانة') : ($setting->maintenance_title_en ?: 'Site Under Maintenance') }} - {{ app()->getLocale() == 'ar' ? $setting->site_name_ar : $setting->site_name_en }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: {{ $setting->primary_color ?: '#12395b' }};
            --secondary-color: {{ $setting->secondary_color ?: '#ff8c32' }};
            --accent-color: {{ $setting->accent_color ?: '#ff8c32' }};
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background: linear-gradient(135deg, #0b1e36 0%, #12395b 50%, #0d2843 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Background Animation */
        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 140, 50, 0.25) 0%, rgba(18, 57, 91, 0) 70%);
            border-radius: 50%;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulseGlow 8s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes pulseGlow {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.9; }
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 28px;
            padding: 50px 40px;
            max-width: 680px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.35);
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
            max-height: 80px;
            width: auto;
            margin-bottom: 25px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }

        .icon-badge {
            width: 84px;
            height: 84px;
            background: linear-gradient(135deg, var(--secondary-color), #ef5c00);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #ffffff;
            margin-bottom: 25px;
            box-shadow: 0 12px 30px rgba(255, 140, 50, 0.4);
            animation: gearRotate 12s linear infinite;
        }

        @keyframes gearRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: #ffffff;
            line-height: 1.3;
        }

        .message {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            margin-bottom: 35px;
        }

        .contact-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 35px;
        }

        .btn-custom {
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.35);
        }

        .btn-whatsapp:hover {
            background-color: #1da851;
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(37, 211, 102, 0.45);
            color: #ffffff;
        }

        .btn-phone {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .btn-phone:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
            color: #ffffff;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .social-link {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            color: #ffffff;
        }

        @media (max-width: 576px) {
            .maintenance-card {
                padding: 35px 20px;
            }
            .title {
                font-size: 1.6rem;
            }
            .message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="maintenance-card">
        <!-- Logo -->
        @if(!empty($setting->header_logo_path) || !empty($setting->logo_path))
            <img src="{{ asset('storage/' . ($setting->header_logo_path ?: $setting->logo_path)) }}" alt="{{ $setting->site_name_ar }}" class="brand-logo">
        @endif

        <!-- Animated Icon -->
        <div>
            <div class="icon-badge">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
        </div>

        <!-- Maintenance Title -->
        <h1 class="title">
            @if(app()->getLocale() == 'ar')
                {{ $setting->maintenance_title_ar ?: 'الموقع تحت الصيانة والتطوير' }}
            @else
                {{ $setting->maintenance_title_en ?: 'We will be back soon!' }}
            @endif
        </h1>

        <!-- Maintenance Message -->
        <p class="message">
            @if(app()->getLocale() == 'ar')
                {{ $setting->maintenance_message_ar ?: 'نقوم حالياً بإجراء تحديثات وتحسينات على النظام لتقديم أفضل خدمة لكم. نسعد بتواصلكم معنا فوراً.' }}
            @else
                {{ $setting->maintenance_message_en ?: 'We are currently performing scheduled maintenance to serve you better. Feel free to contact us.' }}
            @endif
        </p>

        <!-- Direct Contact Buttons -->
        <div class="contact-buttons">
            @if(!empty($setting->whatsapp_number))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->whatsapp_number) }}" target="_blank" class="btn-custom btn-whatsapp">
                    <i class="bi bi-whatsapp fs-5"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'تواصل عبر الواتساب' : 'WhatsApp Us' }}</span>
                </a>
            @endif

            @if(!empty($setting->phone))
                <a href="tel:{{ $setting->phone }}" class="btn-custom btn-phone">
                    <i class="bi bi-telephone-fill fs-5"></i>
                    <span>{{ $setting->phone }}</span>
                </a>
            @endif
        </div>

        <!-- Social Links -->
        <div class="social-links">
            @if(!empty($setting->facebook_url))
                <a href="{{ $setting->facebook_url }}" target="_blank" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
            @endif
            @if(!empty($setting->instagram_url))
                <a href="{{ $setting->instagram_url }}" target="_blank" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
            @endif
            @if(!empty($setting->twitter_url))
                <a href="{{ $setting->twitter_url }}" target="_blank" class="social-link" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
            @endif
            @if(!empty($setting->youtube_url))
                <a href="{{ $setting->youtube_url }}" target="_blank" class="social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
            @endif
            @if(!empty($setting->tiktok_url))
                <a href="{{ $setting->tiktok_url }}" target="_blank" class="social-link" title="TikTok"><i class="bi bi-tiktok"></i></a>
            @endif
        </div>
    </div>

</body>
</html>
