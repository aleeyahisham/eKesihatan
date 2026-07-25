<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eKesihatan</title>
    @php
        $faviconPath = 'images/eksalogo.png';
        if (!file_exists(public_path($faviconPath))) {
            if (file_exists(public_path('images/eksa.png'))) {
                $faviconPath = 'images/eksa.png';
            } elseif (file_exists(public_path('image/eksa.png'))) {
                $faviconPath = 'image/eksa.png';
            } elseif (file_exists(public_path('favicon.ico'))) {
                $faviconPath = 'favicon.ico';
            }
        }
        $faviconAbsolutePath = public_path($faviconPath);
        $faviconVersion = file_exists($faviconAbsolutePath) ? (string) filemtime($faviconAbsolutePath) : '1';
        $faviconUrl = asset($faviconPath) . '?v=' . $faviconVersion;
        $faviconType = str_ends_with($faviconPath, '.ico') ? 'image/x-icon' : 'image/png';
    @endphp
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}" sizes="32x32">
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}" sizes="192x192">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <script>
        (function () {
            const source = @json($faviconUrl);
            const links = Array.from(document.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"], link[rel="apple-touch-icon"]'));
            if (!source || links.length === 0) {
                return;
            }

            const img = new Image();
            img.decoding = 'async';
            img.onload = function () {
                try {
                    const scanCanvas = document.createElement('canvas');
                    scanCanvas.width = img.naturalWidth;
                    scanCanvas.height = img.naturalHeight;
                    const scanContext = scanCanvas.getContext('2d');
                    if (!scanContext) {
                        return;
                    }

                    scanContext.drawImage(img, 0, 0);
                    const { data, width, height } = scanContext.getImageData(0, 0, scanCanvas.width, scanCanvas.height);

                    let minX = width;
                    let minY = height;
                    let maxX = -1;
                    let maxY = -1;

                    for (let y = 0; y < height; y += 1) {
                        for (let x = 0; x < width; x += 1) {
                            const alpha = data[(y * width + x) * 4 + 3];
                            if (alpha > 14) {
                                if (x < minX) minX = x;
                                if (y < minY) minY = y;
                                if (x > maxX) maxX = x;
                                if (y > maxY) maxY = y;
                            }
                        }
                    }

                    if (maxX < minX || maxY < minY) {
                        return;
                    }

                    const cropWidth = maxX - minX + 1;
                    const cropHeight = maxY - minY + 1;
                    const padX = Math.max(2, Math.round(cropWidth * 0.06));
                    const padY = Math.max(2, Math.round(cropHeight * 0.06));
                    const sx = Math.max(0, minX - padX);
                    const sy = Math.max(0, minY - padY);
                    const sw = Math.min(width - sx, cropWidth + (padX * 2));
                    const sh = Math.min(height - sy, cropHeight + (padY * 2));

                    const outputCanvas = document.createElement('canvas');
                    outputCanvas.width = 64;
                    outputCanvas.height = 64;
                    const outputContext = outputCanvas.getContext('2d');
                    if (!outputContext) {
                        return;
                    }

                    outputContext.clearRect(0, 0, 64, 64);
                    outputContext.drawImage(img, sx, sy, sw, sh, 0, 0, 64, 64);
                    const enhancedIcon = outputCanvas.toDataURL('image/png');
                    links.forEach((link) => {
                        link.href = enhancedIcon;
                    });
                } catch (error) {
                    // Keep original favicon when optimization fails.
                }
            };
            img.src = source;
        })();
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/app.js'])
    @endif

    @php
        $publicCssPath = public_path('css/app.css');
        $publicCssVersion = file_exists($publicCssPath)
            ? filemtime($publicCssPath)
            : time();
    @endphp

    <link rel="stylesheet"
          href="{{ asset('css/app.css') }}?v={{ $publicCssVersion }}">

    <style>
        /*
         * FINAL SHARED LAYOUT PROTECTION
         * Applies to Patient, Doctor and Staff/Admin dashboards.
         * Loaded after all external stylesheets so it cannot be overridden.
         */

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            margin: 0;
            overflow-x: hidden;
        }

        .app-shell {
            width: 100%;
            min-height: calc(100vh - var(--app-header-offset));
        }

        @media (min-width: 769px) {
            body.role-patient .app-shell,
            body.role-doctor .app-shell,
            body.role-staff .app-shell {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
            }

            body.role-patient .sidebar,
            body.role-doctor .sidebar,
            body.role-staff .sidebar {
                position: sticky !important;
                top: 0 !important;
                align-self: flex-start !important;
                flex: 0 0 290px !important;
                width: 290px !important;
                min-width: 290px !important;
                max-width: 290px !important;
                margin: 0 !important;
                transform: none !important;
                z-index: 10 !important;
            }

            body.role-patient .sidebar--patient {
                flex-basis: 300px !important;
                width: 300px !important;
                min-width: 300px !important;
                max-width: 300px !important;
            }

            body.role-patient .main-content,
            body.role-doctor .main-content,
            body.role-staff .main-content {
                position: relative !important;
                flex: 1 1 auto !important;
                width: auto !important;
                min-width: 0 !important;
                max-width: 1200px !important;
                margin: 0 auto !important;
                padding: 2rem !important;
                inset: auto !important;
                transform: none !important;
                z-index: 1 !important;
            }
        }

        @media (max-width: 768px) {
            body.role-patient .app-shell,
            body.role-doctor .app-shell,
            body.role-staff .app-shell {
                display: block !important;
                width: 100% !important;
            }

            body.role-patient .sidebar,
            body.role-doctor .sidebar,
            body.role-staff .sidebar,
            body.role-patient .sidebar--patient {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                bottom: auto !important;
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                height: auto !important;
                max-height: none !important;
                margin: 0 !important;
                overflow: visible !important;
                transform: none !important;
                z-index: 1 !important;
            }

            body.role-patient .main-content,
            body.role-doctor .main-content,
            body.role-staff .main-content {
                position: relative !important;
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1.25rem 1rem 2rem !important;
                inset: auto !important;
                transform: none !important;
                z-index: 1 !important;
            }
        }
    </style>


    <style>
        [hidden] { display: none !important; }
        .doctor-notification-overlay { position: fixed !important; inset: 0 !important; z-index: 1990 !important; }
        .doctor-notification-drawer {
            position: fixed !important; top: 0 !important; right: 0 !important; bottom: 0 !important; left: auto !important;
            z-index: 2000 !important; display: flex !important; flex-direction: column !important;
            width: min(460px, 92vw) !important; max-width: 92vw !important; height: 100dvh !important; max-height: 100dvh !important;
            margin: 0 !important; background: #f8fafc !important; border-left: 1px solid #cbd5e1 !important; border-radius: 0 !important;
            box-shadow: -18px 0 48px rgba(15, 23, 42, 0.2) !important; overflow: hidden !important;
            transform: translateX(102%); visibility: hidden; transition: transform 240ms ease, visibility 240ms ease;
        }
        .doctor-notification-drawer.is-open { transform: translateX(0); visibility: visible; }
        .doctor-notification-drawer__body { flex: 1 1 auto !important; min-height: 0 !important; overflow-y: auto !important; }
        body.doctor-notification-drawer-open { overflow: hidden !important; }
        @media (max-width: 768px) { .doctor-notification-drawer { width: 100vw !important; max-width: 100vw !important; } }
    </style>


    <!-- NOTIFICATION_UI_V4_INLINE -->
    <style>
        /* Final inline notification UI. Loaded inside Blade to bypass stale CSS caches. */

        body.role-doctor .sidebar-notification-trigger {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            width: 100% !important;
            min-height: 3rem !important;
            padding: 0.72rem 1rem !important;
            color: #0f172a !important;
            background: transparent !important;
            border: 0 !important;
            border-radius: 0.65rem !important;
            box-shadow: none !important;
            font: inherit !important;
            font-weight: 650 !important;
            text-align: left !important;
            cursor: pointer !important;
        }

        body.role-doctor .sidebar-notification-trigger:hover,
        body.role-doctor .sidebar-notification-trigger[aria-expanded="true"] {
            color: #1d4ed8 !important;
            background: #eff6ff !important;
        }

        body.role-doctor .sidebar-notification-trigger .icon,
        body.role-doctor .sidebar-notification-trigger .icon svg {
            display: block !important;
            width: 1.18rem !important;
            height: 1.18rem !important;
            min-width: 1.18rem !important;
            max-width: 1.18rem !important;
            min-height: 1.18rem !important;
            max-height: 1.18rem !important;
            flex: 0 0 1.18rem !important;
        }

        body.role-doctor .sidebar-notification-trigger__label {
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }

        body.role-doctor .sidebar-notification-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 1.55rem !important;
            height: 1.55rem !important;
            padding: 0 0.4rem !important;
            color: #ffffff !important;
            background: #dc2626 !important;
            border: 2px solid #ffffff !important;
            border-radius: 999px !important;
            box-shadow: 0 3px 10px rgba(220, 38, 38, 0.35) !important;
            font-size: 0.72rem !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            flex: 0 0 auto !important;
        }

        body.role-doctor .doctor-notification-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: auto !important;
            z-index: 2000 !important;
            display: flex !important;
            flex-direction: column !important;
            width: min(440px, 92vw) !important;
            max-width: 92vw !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            margin: 0 !important;
            color: #0f172a !important;
            background: #f8fafc !important;
            border-left: 1px solid #cbd5e1 !important;
            border-radius: 0 !important;
            box-shadow: -18px 0 48px rgba(15, 23, 42, 0.22) !important;
            overflow: hidden !important;
        }

        body.role-doctor .doctor-notification-drawer__header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 1rem !important;
            padding: 1.2rem !important;
            color: #0f172a !important;
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05) !important;
        }

        body.role-doctor .doctor-notification-drawer__header h2 {
            margin: 0 !important;
            color: #0f172a !important;
            font-size: 1.18rem !important;
            font-weight: 800 !important;
            line-height: 1.3 !important;
        }

        body.role-doctor .doctor-notification-drawer__header p {
            margin: 0.35rem 0 0 !important;
            color: #64748b !important;
            font-size: 0.84rem !important;
            line-height: 1.5 !important;
        }

        body.role-doctor .doctor-notification-drawer__eyebrow {
            display: inline-flex !important;
            margin: 0 0 0.35rem !important;
            padding: 0.22rem 0.52rem !important;
            color: #1d4ed8 !important;
            background: #eff6ff !important;
            border-radius: 999px !important;
            font-size: 0.66rem !important;
            font-weight: 850 !important;
            letter-spacing: 0.07em !important;
            text-transform: uppercase !important;
        }

        body.role-doctor .doctor-notification-drawer__close {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 2.4rem !important;
            height: 2.4rem !important;
            min-width: 2.4rem !important;
            max-width: 2.4rem !important;
            min-height: 2.4rem !important;
            max-height: 2.4rem !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #ffffff !important;
            background: #dc2626 !important;
            border: 1px solid #b91c1c !important;
            border-radius: 0.65rem !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28) !important;
            cursor: pointer !important;
            flex: 0 0 2.4rem !important;
        }

        body.role-doctor .doctor-notification-drawer__close:hover {
            background: #b91c1c !important;
            border-color: #991b1b !important;
        }

        body.role-doctor .doctor-notification-drawer__close svg {
            display: block !important;
            width: 1rem !important;
            height: 1rem !important;
            min-width: 1rem !important;
            max-width: 1rem !important;
            min-height: 1rem !important;
            max-height: 1rem !important;
            color: #ffffff !important;
            stroke: currentColor !important;
        }

        body.role-doctor .doctor-notification-drawer__summary {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 1rem !important;
            padding: 0.72rem 1.2rem !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        body.role-doctor .doctor-notification-drawer__summary span {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.42rem !important;
            color: #991b1b !important;
            font-weight: 800 !important;
        }

        body.role-doctor .doctor-notification-drawer__summary span::before {
            content: "" !important;
            display: block !important;
            width: 0.48rem !important;
            height: 0.48rem !important;
            background: #dc2626 !important;
            border-radius: 50% !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12) !important;
        }

        body.role-doctor .doctor-notification-drawer__body {
            display: grid !important;
            align-content: start !important;
            gap: 0.85rem !important;
            flex: 1 1 auto !important;
            min-height: 0 !important;
            padding: 0.9rem !important;
            background: #f8fafc !important;
            overflow-y: auto !important;
        }

        body.role-doctor .drawer-notification-card {
            padding: 0.95rem !important;
            background: #ffffff !important;
            border: 1px solid #dbeafe !important;
            border-left: 4px solid #2563eb !important;
            border-radius: 0.8rem !important;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06) !important;
        }

        body.role-doctor .drawer-notification-card__top {
            display: flex !important;
            align-items: flex-start !important;
            gap: 0.72rem !important;
        }

        body.role-doctor .drawer-notification-card__icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 2rem !important;
            height: 2rem !important;
            min-width: 2rem !important;
            max-width: 2rem !important;
            min-height: 2rem !important;
            max-height: 2rem !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #1d4ed8 !important;
            background: #dbeafe !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 0.58rem !important;
            overflow: hidden !important;
            flex: 0 0 2rem !important;
        }

        body.role-doctor .drawer-notification-card__icon svg {
            display: block !important;
            width: 0.95rem !important;
            height: 0.95rem !important;
            min-width: 0.95rem !important;
            max-width: 0.95rem !important;
            min-height: 0.95rem !important;
            max-height: 0.95rem !important;
            color: #1d4ed8 !important;
            stroke: currentColor !important;
            transform: none !important;
        }

        body.role-doctor .drawer-notification-card__top h3 {
            margin: 0 !important;
            color: #0f172a !important;
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            line-height: 1.4 !important;
        }

        body.role-doctor .drawer-notification-card__top time {
            display: block !important;
            margin-top: 0.12rem !important;
            color: #64748b !important;
            font-size: 0.72rem !important;
        }

        body.role-doctor .drawer-notification-card__message {
            margin: 0.72rem 0 !important;
            color: #475569 !important;
            font-size: 0.83rem !important;
            line-height: 1.55 !important;
        }

        body.role-doctor .drawer-notification-details {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.5rem !important;
            margin: 0 0 0.82rem !important;
        }

        body.role-doctor .drawer-notification-details > div {
            min-width: 0 !important;
            padding: 0.56rem 0.6rem !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.48rem !important;
        }

        body.role-doctor .drawer-notification-details dt {
            color: #64748b !important;
            font-size: 0.63rem !important;
            font-weight: 850 !important;
            text-transform: uppercase !important;
        }

        body.role-doctor .drawer-notification-details dd {
            margin: 0.14rem 0 0 !important;
            color: #1e293b !important;
            font-size: 0.78rem !important;
            font-weight: 650 !important;
            line-height: 1.4 !important;
        }

        @media (max-width: 768px) {
            body.role-doctor .doctor-notification-drawer {
                width: 100vw !important;
                max-width: 100vw !important;
            }

            body.role-doctor .drawer-notification-details {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

</head>
@php
    $isAuthPage = request()->routeIs('login', 'staff.login', 'register');
    $bodyClasses = [];

    $doctorUnreadNotifications = collect();

    if (auth()->check() && auth()->user()->isDoctor()) {
        $doctorUnreadNotifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->limit(20)
            ->get();
    }
    if ($isAuthPage) {
        $bodyClasses[] = 'auth-page';
    }
    if (auth()->check()) {
        if (auth()->user()->isPatient()) {
            $bodyClasses[] = 'role-patient';
        } elseif (auth()->user()->isDoctor()) {
            $bodyClasses[] = 'role-doctor';
        } elseif (auth()->user()->isStaff()) {
            $bodyClasses[] = 'role-staff';
        }
    }
@endphp
<body id="top" class="{{ implode(' ', $bodyClasses) }}">
    <a class="skip-link" href="#main-content" data-i18n="Skip to content">Skip to content</a>
    @php
        $headerLogoPath = null;
        if (file_exists(public_path('images/eksalogo.png'))) {
            $headerLogoPath = 'images/eksalogo.png';
        } elseif (file_exists(public_path('images/eksa.png'))) {
            $headerLogoPath = 'images/eksa.png';
        } elseif (file_exists(public_path('image/eksa.png'))) {
            $headerLogoPath = 'image/eksa.png';
        }
    @endphp
    <header>
        <div class="brand">
            @if ($headerLogoPath)
                <img
                    class="brand-logo"
                    src="{{ asset($headerLogoPath) }}"
                    alt="Unit Kesihatan UiTM Perlis logo"
                    width="80"
                    height="80"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('images/eksa.png') }}';"
                >
            @endif
            <div class="brand-text">
                <h1><a href="{{ route('landing') }}">eKesihatan</a></h1>
                <span class="brand-subtitle" data-i18n="Unit Kesihatan UiTM Perlis">Unit Kesihatan UiTM Perlis</span>
            </div>
        </div>
        <div class="header-actions">
            <div class="header-controls">
                <div class="font-controls" role="group" aria-label="Font size">
                    <span class="font-label" data-i18n="Font size">Font size</span>
                    <div class="font-size-control">
                        <button type="button" class="font-size-btn" data-font-size-action="decrease" aria-label="Decrease font size">A−</button>
                        <input id="font-size-slider" type="range" min="12" max="20" step="1" value="16" aria-label="Font size" />
                        <button type="button" class="font-size-btn" data-font-size-action="increase" aria-label="Increase font size">A+</button>
                        <span id="font-size-value" class="font-size-value">16px</span>
                    </div>
                </div>
            </div>
            <nav class="top-nav">
                @guest
                    <a href="{{ route('landing') }}" data-i18n="Home">Home</a>
                    <a href="{{ route('login') }}" data-i18n="Login">Login</a>
                    <a href="{{ route('register') }}" data-i18n="Register">Register</a>
                    <a
                        href="{{ route('staff.login') }}"
                        class="top-nav-icon-link"
                        aria-label="Staff and Doctor Login"
                    >
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/>
                            <path d="M10 17l5-5-5-5"/>
                            <path d="M15 12H3"/>
                        </svg>
                    </a>
                @endguest
            </nav>
        </div>
    </header>
 
    <div class="app-shell">
        @auth
            @unless (request()->routeIs('landing', 'login', 'staff.login', 'register'))
            <aside class="sidebar {{ auth()->user()->isPatient() ? 'sidebar--patient' : '' }}">
                <div class="sidebar-title" data-i18n="Navigation">Navigation</div>
                <nav class="sidebar-nav">
                    <div class="sidebar-menu">
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('dashboard') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-4v-6H8v6H4a1 1 0 0 1-1-1z"/></svg></span>
                                <span data-i18n="Staff Dashboard">Staff Dashboard</span>
                            </a>
                            <a href="{{ route('admin.services.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M8 6V4m8 2V4m-8 6h8"/></svg></span>
                                <span data-i18n="Health Services">Health Services</span>
                            </a>
                            <a href="{{ route('admin.doctors.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>
                                <span data-i18n="Doctors">Doctors</span>
                            </a>
                            <a href="{{ route('admin.slots.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span>
                                <span data-i18n="Appointment Slots">Appointment Slots</span>
                            </a>
                            <a href="{{ route('admin.appointments.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/><rect x="4" y="4" width="16" height="16" rx="2"/></svg></span>
                                <span data-i18n="Appointments">Appointments</span>
                            </a>
                            <a href="{{ route('admin.bulletins.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5Z"/><path d="M7 9h8M7 13h5"/></svg></span>
                                <span data-i18n="Clinic Bulletins">Clinic Bulletins</span>
                            </a>
                            <a href="{{ route('admin.forms.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5"/></svg></span>
                                <span data-i18n="Forms & Downloads">Forms & Downloads</span>
                            </a>
                            <a href="{{ route('staff.patients.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M5 20a4 4 0 0 1 8 0"/><path d="M15 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path d="M18 20a3 3 0 0 0-3-3"/></svg></span>
                                <span data-i18n="Patient Directory">Patient Directory</span>
                            </a>
                        @elseif (auth()->user()->isDoctor())
                            <a href="{{ route('dashboard') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-4v-6H8v6H4a1 1 0 0 1-1-1z"/></svg></span>
                                <span data-i18n="Doctor Dashboard">Doctor Dashboard</span>
                            </a>
                            <a href="{{ route('doctor.appointments.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span>
                                <span data-i18n="Daily Appointments">Daily Appointments</span>
                            </a>

                            <button
                                type="button"
                                class="sidebar-notification-trigger"
                                id="doctor-notification-trigger"
                                aria-controls="doctor-notification-drawer"
                                aria-expanded="false"
                            >
                                <span class="icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>
                                </span>

                                <span class="sidebar-notification-trigger__label">
                                    Notifications
                                </span>

                                @if($doctorUnreadNotifications->isNotEmpty())
                                    <span
                                        class="sidebar-notification-badge"
                                        aria-label="{{ $doctorUnreadNotifications->count() }} unread notifications"
                                    >
                                        {{ $doctorUnreadNotifications->count() > 99 ? '99+' : $doctorUnreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <a href="{{ route('staff.patients.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M5 20a4 4 0 0 1 8 0"/><path d="M15 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path d="M18 20a3 3 0 0 0-3-3"/></svg></span>
                                <span data-i18n="Patient Directory">Patient Directory</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-4v-6H8v6H4a1 1 0 0 1-1-1z"/></svg></span>
                                <span data-i18n="Patient Dashboard">Patient Dashboard</span>
                            </a>
                            <a href="{{ route('patient.services.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M8 6V4m8 2V4m-8 6h8"/></svg></span>
                                <span data-i18n="Health Services">Health Services</span>
                            </a>
                            <a href="{{ route('patient.appointments.create') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></span>
                                <span data-i18n="Book Appointment">Book Appointment</span>
                            </a>
                            <a href="{{ route('patient.appointments.index') }}">
                                <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/><rect x="4" y="4" width="16" height="16" rx="2"/></svg></span>
                                <span data-i18n="My Appointments">My Appointments</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}">
                            <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>
                            <span data-i18n="Profile">Profile</span>
                        </a>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="sidebar-logout">
                        @csrf
                        <button type="submit">
                            <span class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 7V5a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5a2 2 0 0 1-2-2v-2"/><path d="M13 12H3"/><path d="m7 8-4 4 4 4"/></svg></span>
                            <span data-i18n="Logout">Logout</span>
                        </button>
                    </form>
                </nav>
            </aside>
            @endunless
        @endauth
 
        <main id="main-content" class="main-content">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>

    @auth
        @if(auth()->user()->isDoctor())
            <div
                class="doctor-notification-overlay"
                id="doctor-notification-overlay"
                hidden
            ></div>

            <aside
                class="doctor-notification-drawer"
                id="doctor-notification-drawer"
                aria-labelledby="doctor-notification-drawer-title"
                aria-hidden="true"
                hidden
            >
                <div class="doctor-notification-drawer__header">
                    <div>
                        <p class="doctor-notification-drawer__eyebrow">Doctor Inbox</p>
                        <h2 id="doctor-notification-drawer-title">Appointment Notifications</h2>
                        <p>Review newly reassigned consultations without leaving the current page.</p>
                    </div>

                    <button
                        type="button"
                        class="doctor-notification-drawer__close"
                        id="doctor-notification-close"
                        aria-label="Close notifications"
                        title="Close notifications"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="doctor-notification-drawer__summary">
                    <span>{{ $doctorUnreadNotifications->count() }} unread</span>
                </div>

                <div class="doctor-notification-drawer__body">
                    @forelse($doctorUnreadNotifications as $notification)
                        @php
                            $notificationData = is_array($notification->data)
                                ? $notification->data
                                : [];

                            $appointmentId = $notificationData['appointment_id'] ?? null;

                            $appointmentUrl = $notificationData['url']
                                ?? (
                                    $appointmentId
                                        ? route('doctor.appointments.show', $appointmentId)
                                        : null
                                );
                        @endphp

                        <article class="drawer-notification-card">
                            <div class="drawer-notification-card__top">
                                <span class="drawer-notification-card__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M8 7h8"/>
                                        <path d="M8 11h8"/>
                                        <path d="M8 15h5"/>
                                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                                    </svg>
                                </span>

                                <div>
                                    <h3>{{ $notificationData['title'] ?? 'Appointment Reassigned' }}</h3>
                                    <time datetime="{{ $notification->created_at->toIso8601String() }}">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </time>
                                </div>
                            </div>

                            <p class="drawer-notification-card__message">
                                {{ $notificationData['message'] ?? 'A new appointment has been assigned to your schedule.' }}
                            </p>

                            <dl class="drawer-notification-details">
                                <div>
                                    <dt>Patient</dt>
                                    <dd>{{ $notificationData['patient_name'] ?? 'Patient' }}</dd>
                                </div>

                                <div>
                                    <dt>Service</dt>
                                    <dd>{{ $notificationData['service_name'] ?? 'General Consultation' }}</dd>
                                </div>

                                <div>
                                    <dt>Previous Doctor</dt>
                                    <dd>{{ $notificationData['previous_doctor'] ?? '-' }}</dd>
                                </div>

                                <div>
                                    <dt>New Schedule</dt>
                                    <dd>{{ $notificationData['new_schedule'] ?? $notificationData['scheduled_at'] ?? '-' }}</dd>
                                </div>

                                <div class="drawer-notification-details__wide">
                                    <dt>Reason</dt>
                                    <dd>{{ $notificationData['reason'] ?? 'Emergency schedule change' }}</dd>
                                </div>
                            </dl>

                            <div class="drawer-notification-card__actions">
                                @if($appointmentUrl)
                                    <a class="drawer-notification-primary" href="{{ $appointmentUrl }}">
                                        Review Appointment
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('doctor.notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="drawer-notification-secondary">
                                        Mark as Read
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="doctor-notification-empty">
                            <span class="doctor-notification-empty__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>

                            <h3>You are all caught up</h3>
                            <p>There are no unread appointment notifications.</p>
                        </div>
                    @endforelse
                </div>
            </aside>
        @endif
    @endauth

 
    @if (request()->routeIs('landing'))
        <style>
            .brand {
                display: flex;
                align-items: center;
                gap: 0.7rem;
            }

            .brand-text {
                display: flex;
                flex-direction: column;
                gap: 0.1rem;
            }

            .brand-logo {
                width: var(--brand-logo-size, 96px);
                height: var(--brand-logo-size, 96px);
                object-fit: contain;
                flex-shrink: 0;
            }

            /* Footer fallback styles to match UiTM Perlis layout even with stale asset cache */
            #site-footer {
                margin-top: 1rem;
                color: #e8e8e8;
                background: #24155a;
                text-align: left !important;
                font-family: "Segoe UI", Arial, sans-serif;
            }

            #site-footer * {
                box-sizing: border-box;
            }

            #site-footer .uitm-footer__top {
                padding: 2.55rem 0 2.35rem;
                background-image:
                    linear-gradient(rgba(38, 22, 95, 0.9), rgba(28, 16, 72, 0.94)),
                    url("https://perlis.uitm.edu.my/images/gambar/BottomBackground.jpg");
                background-size: cover;
                background-position: center;
            }

            #site-footer .uitm-footer__inner {
                width: min(1200px, calc(100% - 2.8rem));
                margin: 0 auto;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1.75rem;
                align-items: start;
            }

            #site-footer .uitm-footer__column h3 {
                margin: 0 0 0.95rem;
                color: #ffffff;
                font-size: 1rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.01em;
            }

            #site-footer .uitm-footer__news,
            #site-footer .uitm-footer__links,
            #site-footer .uitm-footer__contact-list {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            #site-footer .uitm-footer__news {
                display: grid;
                gap: 0.75rem;
            }

            #site-footer .uitm-footer__news li {
                border-bottom: 1px solid rgba(225, 223, 250, 0.36);
                padding-bottom: 0.74rem;
            }

            #site-footer .uitm-footer__news li:last-child {
                border-bottom: none;
            }

            #site-footer .uitm-footer__news time {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                margin-bottom: 0.35rem;
                color: #e4e3f2;
                font-size: 0.92rem;
            }

            #site-footer .uitm-footer__news time span {
                font-size: 0.86rem;
                line-height: 1;
                opacity: 0.95;
            }

            #site-footer .uitm-footer__news a,
            #site-footer .uitm-footer__links a,
            #site-footer .uitm-footer__contact-list a,
            #site-footer .uitm-footer__policy-links a {
                color: #a1a1a1;
                text-decoration: none;
                transition: color 160ms ease, opacity 160ms ease, background-color 160ms ease;
            }

            #site-footer .uitm-footer__news a:hover,
            #site-footer .uitm-footer__links a:hover,
            #site-footer .uitm-footer__contact-list a:hover,
            #site-footer .uitm-footer__policy-links a:hover {
                color: #ffffff;
                text-decoration: none;
            }

            #site-footer .uitm-footer__news a {
                display: inline-block;
                line-height: 1.5;
            }

            #site-footer .uitm-footer__links {
                display: grid;
                gap: 0.4rem;
            }

            #site-footer .uitm-footer__contact-block {
                margin-bottom: 0.75rem;
                font-size: 0.95rem;
                line-height: 1.5;
                color: #ececf7;
            }

            #site-footer .uitm-footer__contact-block p {
                margin: 0;
            }

            #site-footer .uitm-footer__contact-block .uitm-footer__org {
                font-weight: 700;
            }

            #site-footer .uitm-footer__contact-list {
                display: grid;
                gap: 0.3rem;
                margin-bottom: 0.85rem;
            }

            #site-footer .uitm-footer__contact-list li {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
            }

            #site-footer .uitm-footer__contact-list li span,
            #site-footer .uitm-footer__contact-list li img {
                color: #f0effa;
                width: 1rem;
                text-align: center;
                font-size: 0.88rem;
            }

            #site-footer .uitm-footer__contact-list li img {
                height: 1rem;
                object-fit: contain;
            }

            #site-footer .uitm-footer__brand-logo {
                display: inline-flex;
                margin-bottom: 0.7rem;
            }

            #site-footer .uitm-footer__brand-logo img {
                width: min(235px, 100%);
                height: auto;
                display: block;
            }

            #site-footer .uitm-footer__socials {
                display: flex;
                align-items: center;
                gap: 0.8rem;
            }

            #site-footer .uitm-footer__socials a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                opacity: 0.96;
            }

            #site-footer .uitm-footer__socials a img {
                width: 1.85rem;
                height: 1.85rem;
                display: block;
            }

            #site-footer .uitm-footer__socials a:hover {
                opacity: 1;
            }

            #site-footer .uitm-footer__bottom {
                background-image:
                    linear-gradient(rgba(23, 12, 56, 0.92), rgba(23, 12, 56, 0.92)),
                    url("https://perlis.uitm.edu.my/images/gambar/FooterBackground.jpg");
                background-position: center;
                background-repeat: no-repeat;
                border-top: 1px solid #b68c2f;
                padding: 0.92rem 0;
            }

            #site-footer .uitm-footer__inner--bottom {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.9rem;
            }

            #site-footer .uitm-footer__inner--bottom p {
                margin: 0;
                color: #f2f1fa;
                font-size: 0.82rem;
                letter-spacing: 0.02em;
                text-transform: uppercase;
            }

            #site-footer .uitm-footer__policy-links {
                display: inline-flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.4rem;
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.01em;
            }

            #site-footer .uitm-footer__policy-links span {
                color: #dad8eb;
            }

            #site-footer .uitm-footer__to-top {
                width: 1.85rem;
                height: 1.85rem;
                border-radius: 0.25rem;
                border: 1px solid rgba(255, 255, 255, 0.38);
                color: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                background: rgba(255, 255, 255, 0.05);
                font-size: 0.95rem;
            }

            #site-footer .uitm-footer__to-top:hover {
                background: rgba(255, 255, 255, 0.18);
                text-decoration: none;
            }

            @media (max-width: 1024px) {
                #site-footer .uitm-footer__inner {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 1.35rem;
                }

                #site-footer .uitm-footer__inner--bottom {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            @media (max-width: 768px) {
                #site-footer .uitm-footer__top {
                    padding: 2rem 0 1.75rem;
                }

                #site-footer .uitm-footer__inner {
                    width: min(1200px, calc(100% - 1.75rem));
                    grid-template-columns: 1fr;
                    gap: 1.2rem;
                }

                #site-footer .uitm-footer__column h3 {
                    margin-bottom: 0.75rem;
                }

                #site-footer .uitm-footer__policy-links {
                    font-size: 0.82rem;
                    line-height: 1.5;
                }

                #site-footer .uitm-footer__to-top {
                    align-self: flex-end;
                }
            }
        </style>

        <footer class="uitm-footer" id="site-footer">
            <div class="uitm-footer__top">
                <div class="uitm-footer__inner">
                    <div class="uitm-footer__column" aria-labelledby="uitm-footer-student-info">
                        <h3 id="uitm-footer-student-info">STUDENT INFORMATION</h3>
                        <ul class="uitm-footer__news">
                            <li>
                                <time datetime="2026-01-18"><span aria-hidden="true">◷</span> 18 January 2026</time>
                                <a href="https://perlis.uitm.edu.my/index.php/component/content/article/298-jadual-waktu-peperiksaan-akhir-julai-2025?catid=47&amp;Itemid=209" target="_blank" rel="noopener noreferrer">
                                    March 2026 Special Examination Information (MyExamHub)
                                </a>
                            </li>
                            <li>
                                <time datetime="2025-09-18"><span aria-hidden="true">◷</span> 18 September 2025</time>
                                <a href="https://perlis.uitm.edu.my/index.php/component/content/article/305-maklumat-peperiksaan-khas?catid=47&amp;Itemid=209" target="_blank" rel="noopener noreferrer">
                                    September 2025 Special Examination Information
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="uitm-footer__column" aria-labelledby="uitm-footer-quick-links">
                        <h3 id="uitm-footer-quick-links">QUICK LINKS</h3>
                        <ul class="uitm-footer__links">
                            <li><a href="https://hea.uitm.edu.my/v4/index.php/calendars/academic-calendar" target="_blank" rel="noopener noreferrer">Academic Calendar</a></li>
                            <li><a href="https://pengambilan.uitm.edu.my/kalendar-pengambilan" target="_blank" rel="noopener noreferrer">Intake Calendar</a></li>
                            <li><a href="https://simsweb.uitm.edu.my/sportal_app/graduat/" target="_blank" rel="noopener noreferrer">Graduate Quick Search</a></li>
                            <li><a href="https://news.uitm.edu.my/" target="_blank" rel="noopener noreferrer">News</a></li>
                            <li><a href="https://uitmholdings.com/" target="_blank" rel="noopener noreferrer">UiTM Holding</a></li>
                            <li><a href="https://hoteluitm.com/" target="_blank" rel="noopener noreferrer">Hotel UiTM</a></li>
                            <li><a href="https://www.facebook.com/uitmfclions/" target="_blank" rel="noopener noreferrer">UiTM FC</a></li>
                            <li><a href="https://aduan.uitm.edu.my" target="_blank" rel="noopener noreferrer">e-Aduan</a></li>
                            <li><a href="https://www.uitm.edu.my/index.php/en/frequently-asked-questions" target="_blank" rel="noopener noreferrer">FAQ</a></li>
                            <li><a href="https://wifi.uitm.edu.my/wifi/" target="_blank" rel="noopener noreferrer">UiTM WiFi</a></li>
                        </ul>
                    </div>

                    <div class="uitm-footer__column" aria-labelledby="uitm-footer-contact">
                        <h3 id="uitm-footer-contact">CONTACT US</h3>
                        <div class="uitm-footer__contact-block">
                            <p class="uitm-footer__org">Health Unit,</p>
                            <p>(Beringin College Complex),</p>
                            <p>Student Affairs Division,</p>
                            <p>Universiti Teknologi MARA, Perlis Branch,</p>
                            <p>Arau Campus,</p>
                            <p>02600 Arau,</p>
                            <p>Perlis.</p>
                        </div>
                        <ul class="uitm-footer__contact-list">
                            <li><span aria-hidden="true">☎</span><a href="tel:+6049882075">+604-9882075</a></li>
                            <li>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="" aria-hidden="true" loading="lazy">
                                <a href="https://wa.me/6049882075" target="_blank" rel="noopener noreferrer">+6049882075</a>
                            </li>
                        </ul>
                        <a class="uitm-footer__brand-logo" href="https://www.uitm.edu.my/" target="_blank" rel="noopener noreferrer" aria-label="UiTM official website">
                            <img src="https://perlis.uitm.edu.my/images/logo/uitmdihatiku-footer.png" alt="UiTM dihatiku footer logo" loading="lazy">
                        </a>
                        <div class="uitm-footer__socials">
                            <a href="https://www.google.com/maps/dir/6.4421888,100.2831872/Unit+Kesihatan+Klinik+UiTM,+UiTM+Cawangan+Perlis,+Uit" target="_blank" rel="noopener noreferrer" aria-label="Unit Kesihatan UiTM Arau direction on Google Maps">
                                <img src="https://perlis.uitm.edu.my/images/gambar/google-maps-uitm.png" alt="" loading="lazy">
                            </a>
                            <a href="https://www.waze.com/ul?q=Unit%20Kesihatan%20UiTM%20Arau&amp;navigate=yes" target="_blank" rel="noopener noreferrer" aria-label="Unit Kesihatan UiTM Arau location on Waze">
                                <img src="https://perlis.uitm.edu.my/images/gambar/waze.png" alt="" loading="lazy">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uitm-footer__bottom">
                <div class="uitm-footer__inner uitm-footer__inner--bottom">
                    <p>© COPYRIGHT UNIT KESIHATAN UiTM PERLIS BRANCH 2026. ALL RIGHTS RESERVED.</p>
                    <nav class="uitm-footer__policy-links" aria-label="Footer policy links">
                        <a href="https://www.uitm.edu.my/index.php/en/disclaimer-copyright" target="_blank" rel="noopener noreferrer">DISCLAIMER &amp; COPYRIGHT</a>
                        <span>|</span>
                        <a href="https://www.uitm.edu.my/index.php/en/privacy-statement" target="_blank" rel="noopener noreferrer">PRIVACY STATEMENT</a>
                        <span>|</span>
                        <a href="https://ppii.uitm.edu.my/images/pekeliling/universiti/Dasar/DasarKeselamatanICTv2.pdf" target="_blank" rel="noopener noreferrer">ICT SECURITY POLICY</a>
                    </nav>
                    <a class="uitm-footer__to-top" href="#top" aria-label="Back to top">↑</a>
                </div>
            </div>
        </footer>
    @else
        <div class="page-copyright" style="width: 100%; text-align: center; display: block; margin: 0 auto;">
            © COPYRIGHT UNIT KESIHATAN UiTM PERLIS BRANCH 2026. ALL RIGHTS RESERVED.
        </div>
    @endif
 
    <script>
        (function () {
            const buttons = document.querySelectorAll('[data-font-size-action]');
            const slider = document.getElementById('font-size-slider');
            const valueLabel = document.getElementById('font-size-value');
            const root = document.documentElement;
            const storageKey = 'ekesihatan-font-size';
            const minSize = 12;
            const maxSize = 20;

            const clampSize = (size) => Math.min(maxSize, Math.max(minSize, Number(size)));
 
            const applySize = (size) => {
                const normalized = clampSize(size);
                root.style.setProperty('--base-font-size', normalized + 'px');
                if (slider) {
                    slider.value = normalized;
                }
                if (valueLabel) {
                    valueLabel.textContent = normalized + 'px';
                }
                buttons.forEach((button) => {
                    button.classList.toggle('active', false);
                    button.setAttribute('aria-pressed', 'false');
                });
            };
 
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                applySize(stored);
            } else {
                applySize('16');
            }
 
            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const currentSize = Number(localStorage.getItem(storageKey) || slider?.value || 16);
                    const step = button.dataset.fontSizeAction === 'decrease' ? -1 : 1;
                    const nextSize = clampSize(currentSize + step);
                    localStorage.setItem(storageKey, nextSize);
                    applySize(nextSize);
                });
            });

            if (slider) {
                slider.addEventListener('input', () => {
                    const size = slider.value;
                    localStorage.setItem(storageKey, size);
                    applySize(size);
                });
            }
        })();
    </script>

    <script>
        (function () {
            document.addEventListener('submit', function (event) {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const methodOverride = form.querySelector('input[name="_method"]');
                const isDeleteAction = methodOverride && String(methodOverride.value).toUpperCase() === 'DELETE';
                const explicitConfirm = form.hasAttribute('data-confirm-kind');

                if (!isDeleteAction && !explicitConfirm) {
                    return;
                }

                const message = form.getAttribute('data-confirm-message')
                    || 'Are you sure you want to continue? This action cannot be undone.';

                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });
        })();
    </script>
    @auth
        @if(auth()->user()->isDoctor())
            <script>
                (function () {
                    const trigger = document.getElementById('doctor-notification-trigger');
                    const drawer = document.getElementById('doctor-notification-drawer');
                    const overlay = document.getElementById('doctor-notification-overlay');
                    const closeButton = document.getElementById('doctor-notification-close');
                    if (!trigger || !drawer || !overlay || !closeButton) return;
                    let closeTimer = null;
                    const openDrawer = function () {
                        if (closeTimer) { window.clearTimeout(closeTimer); closeTimer = null; }
                        drawer.hidden = false; overlay.hidden = false;
                        window.requestAnimationFrame(function () { drawer.classList.add('is-open'); overlay.classList.add('is-visible'); });
                        drawer.setAttribute('aria-hidden','false'); trigger.setAttribute('aria-expanded','true');
                        document.body.classList.add('doctor-notification-drawer-open'); closeButton.focus();
                    };
                    const closeDrawer = function () {
                        drawer.classList.remove('is-open'); overlay.classList.remove('is-visible');
                        drawer.setAttribute('aria-hidden','true'); trigger.setAttribute('aria-expanded','false');
                        document.body.classList.remove('doctor-notification-drawer-open');
                        closeTimer = window.setTimeout(function () { drawer.hidden = true; overlay.hidden = true; closeTimer = null; }, 240);
                        trigger.focus();
                    };
                    trigger.addEventListener('click', function () { drawer.classList.contains('is-open') ? closeDrawer() : openDrawer(); });
                    closeButton.addEventListener('click', closeDrawer); overlay.addEventListener('click', closeDrawer);
                    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && drawer.classList.contains('is-open')) closeDrawer(); });
                })();
            </script>
        @endif
    @endauth


    @stack('scripts')
</body>
</html>