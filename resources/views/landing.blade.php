<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AureusERP — The Modern Business Operating System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script>
        // Apply dark mode immediately to prevent flash
        (function() {
            const stored = localStorage.getItem('theme');
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['DM Serif Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',
                            400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',
                            800:'#5b21b6',900:'#4c1d95'
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-up': 'fadeUp 0.7s ease-out forwards',
                    },
                    keyframes: {
                        float: { '0%,100%':{transform:'translateY(0)'}, '50%':{transform:'translateY(-10px)'} },
                        fadeUp: { from:{opacity:'0',transform:'translateY(24px)'}, to:{opacity:'1',transform:'translateY(0)'} },
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #ffffff; color: #111111; }

        /* ─── Dynamic Island Navbar ─── */
        /* ── Default state: white pill with dark text ── */
        .dynamic-island {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 100px;
            padding: 10px 40px;
            display: flex;
            align-items: center;
            gap: 52px;
            width: 90vw;
            max-width: 1100px;
            transition: all 0.4s ease;
            box-shadow: 0 12px 56px rgba(0,0,0,0.14), 0 2px 16px rgba(0,0,0,0.07);
        }
        .dynamic-island .island-logo-text { color: #111; transition: color 0.4s; }
        .dynamic-island .nav-link         { color: #555; transition: color 0.4s; }
        .dynamic-island .nav-link:hover   { color: #111; }
        .dynamic-island .island-login     { color: #777; transition: color 0.4s; }
        .dynamic-island .island-login:hover { color: #111; }
        .dynamic-island .island-signup    { background: #111; color: #fff; border: 1px solid transparent; transition: background 0.4s, color 0.4s; }
        .dynamic-island .island-signup:hover { background: #333; }
        .dynamic-island .island-divider   { background: rgba(0,0,0,0.10) !important; }

        /* ── Scrolled state: white pill, dark text ── */
        .dynamic-island.expanded {
            background: rgba(255,255,255,0.98);
            border-color: rgba(0,0,0,0.10);
            box-shadow: 0 12px 56px rgba(0,0,0,0.14), 0 2px 16px rgba(0,0,0,0.07);
            padding: 10px 44px;
        }
        .dynamic-island.expanded .island-logo-text { color: #111; }
        .dynamic-island.expanded .nav-link         { color: #555; }
        .dynamic-island.expanded .nav-link:hover   { color: #111; }
        .dynamic-island.expanded .island-login     { color: #777; }
        .dynamic-island.expanded .island-login:hover { color: #111; }
        .dynamic-island.expanded .island-signup    { background: #111; color: #fff; border-color: transparent; }
        .dynamic-island.expanded .island-signup:hover { background: #333; }
        .dynamic-island.expanded .island-divider   { background: rgba(0,0,0,0.10) !important; }
        /* ── Mobile (≤768px) ── */
        @media (max-width: 768px) {
            .dynamic-island {
                padding: 12px 16px;
                gap: 0;
                width: calc(100vw - 24px);
                border-radius: 18px;
                flex-wrap: wrap;
                align-items: center;
            }
            .dynamic-island.expanded {
                padding: 12px 16px;
                border-radius: 18px;
            }
            .island-nav-links  { display: none !important; }
            .island-divider    { display: none !important; }
            .island-auth       { display: none !important; }
            .island-hamburger  { display: flex !important; }
        }
        @media (min-width: 769px) {
            .island-hamburger  { display: none !important; }
            .mobile-nav-drawer { display: none !important; }
        }

        /* ── Mobile drawer ── */
        .mobile-nav-drawer {
            display: none;
            width: 100%;
            padding-top: 12px;
            margin-top: 8px;
            border-top: 1px solid #f0f0f0;
            flex-direction: column;
            gap: 2px;
            animation: drawerSlide .22s ease-out;
        }
        @keyframes drawerSlide {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dynamic-island.mobile-open { border-radius: 18px; }
        .dynamic-island.mobile-open .mobile-nav-drawer { display: flex; }

        .mobile-nav-link {
            display: block; padding: 11px 16px;
            font-size: 14px; font-weight: 500; color: #444;
            text-decoration: none; border-radius: 12px;
            transition: background .15s, color .15s;
        }
        .mobile-nav-link:hover { background: #f5f5f5; color: #111; }

        .mobile-nav-cta {
            display: flex; gap: 8px;
            margin-top: 10px; padding: 0 4px;
        }
        .mobile-nav-cta a {
            flex: 1; text-align: center; padding: 11px;
            border-radius: 12px; font-size: 13px; font-weight: 600;
            text-decoration: none; transition: background .15s;
        }
        .mobile-nav-cta .m-login  { background: #f2f2f2; color: #111; }
        .mobile-nav-cta .m-login:hover  { background: #e8e8e8; }
        .mobile-nav-cta .m-signup { background: #111; color: #fff; }
        .mobile-nav-cta .m-signup:hover { background: #333; }

        /* ── Hamburger button ── */
        .island-hamburger {
            margin-left: auto;
            background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 8px;
            display: flex; flex-direction: column;
            gap: 5px; transition: opacity .2s;
        }
        .island-hamburger span {
            display: block; width: 22px; height: 2px;
            background: #111; border-radius: 2px;
            transition: transform .3s ease, opacity .3s ease, width .3s ease;
        }
        /* X state */
        .dynamic-island.mobile-open .island-hamburger span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .dynamic-island.mobile-open .island-hamburger span:nth-child(2) { opacity: 0; width: 0; }
        .dynamic-island.mobile-open .island-hamburger span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        /* Logo text — dark always */
        .island-logo-text {
            font-size: 16px; font-weight: 650; color: #111;
            letter-spacing: -0.3px; white-space: nowrap;
            transition: color 0.3s;
        }
        .dynamic-island.expanded .island-logo-text { color: #111; }

        /* Logo icon bg */
        .island-logo-icon {
            background: transparent;
            transition: background 0.3s;
        }
        .dynamic-island.expanded .island-logo-icon { background: transparent; }

        /* Nav links */
        .nav-link {
            font-size: 15px; font-weight: 500;
            color: #555;
            text-decoration: none;
            transition: color .2s;
            white-space: nowrap;
        }
        .nav-link:hover { color: #111; }
        .dynamic-island.expanded .nav-link { color: #555; }
        .dynamic-island.expanded .nav-link:hover { color: #111; }

        /* Sign Up button */
        .island-signup {
            background: #111; color: #fff;
            font-size: 14px; font-weight: 600;
            padding: 9px 20px; border-radius: 100px;
            text-decoration: none; white-space: nowrap;
            transition: background .2s, box-shadow .2s;
            border: none;
        }
        .island-signup:hover { background: #333; box-shadow: 0 2px 12px rgba(0,0,0,0.15); }
        .dynamic-island.expanded .island-signup { background: #111; color: #fff; }
        .dynamic-island.expanded .island-signup:hover { background: #333; }

        /* Login link */
        .island-login {
            font-size: 14px; font-weight: 500; color: #666;
            text-decoration: none; white-space: nowrap; transition: color .2s;
        }
        .island-login:hover { color: #111; }
        .dynamic-island.expanded .island-login { color: #777; }
        .dynamic-island.expanded .island-login:hover { color: #111; }

        /* ─── Products Dropdown ─── */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 14px);
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 24px 72px rgba(0,0,0,0.13);
            width: 640px;
            z-index: 200;
            animation: dropIn 0.2s ease-out forwards;
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateX(-50%) translateY(-8px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        .dropdown-trigger { position: relative; }
        .dropdown-trigger:hover .dropdown-menu { display: block; }

        /* ─── Hero ─── */
        .hero-bg {
            background: #07060f;
            position: relative;
        }
        /* Video background */
        .hero-video-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }
        .hero-video-bg video {
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0.45;
        }
        /* Dark overlay + fade to bottom */
        .hero-video-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to bottom, rgba(7,6,15,0.3) 0%, rgba(7,6,15,0.15) 50%, rgba(7,6,15,0.6) 100%);
            z-index: 1;
        }
        /* Keep wiremesh class so we don't break dark-mode selectors, but hide it */
        .hero-wiremesh { display: none; }
        .tag-highlight {
            background: #f3f0ff; color: #6d28d9;
            padding: 3px 10px; border-radius: 6px;
            font-size: 13px; font-weight: 500;
        }
        /* ─── Hero CTA Group ─── */
        .hero-btn-primary {
            display: inline-flex; align-items: center; gap: 14px;
            background: #7c3aed; color: #fff; border: none;
            padding: 13px 13px 13px 26px; border-radius: 100px;
            font-size: 14px; font-weight: 500; cursor: pointer;
            text-decoration: none; transition: background .2s;
            white-space: nowrap; letter-spacing: -0.1px;
        }
        .hero-btn-primary:hover { background: #6d28d9; }
        .hero-btn-primary .arrow-circle {
            width: 34px; height: 34px; border-radius: 50%;
            background: #fff; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .2s;
        }
        .hero-btn-primary:hover .arrow-circle { background: #f0ebff; }
        .hero-btn-secondary {
            display: inline-flex; align-items: center; gap: 10px;
            background: transparent; color: #e0e0f0; border: none;
            padding: 6px 22px 6px 4px; border-radius: 100px;
            font-size: 14px; font-weight: 500; cursor: pointer;
            text-decoration: none; transition: color .2s;
            white-space: nowrap;
        }
        .hero-btn-secondary .play-circle {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .2s;
        }
        .hero-btn-secondary:hover .play-circle { background: rgba(255,255,255,0.25); }
        @media (max-width: 640px) {
            .hero-btn-primary { width: 100%; justify-content: center; }
        }

        /* ─── Dashboard Mockup ─── */
        .dashboard-mockup {
            background: #fff; border-radius: 4px;
            border: 1px solid #e8e8e8;
            box-shadow: 0 32px 80px rgba(0,0,0,0.10), 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .mockup-titlebar {
            background: #f8f8f8; border-bottom: 1px solid #eee;
            padding: 10px 16px; display: flex; align-items: center; gap: 6px;
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .stat-card {
            background: #fafafa; border: 1px solid #efefef;
            border-radius: 4px; padding: 14px;
        }
        .stat-up { color: #16a34a; font-size: 11px; font-weight: 500; }

        /* ─── Sections ─── */
        .section-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; color: #6d28d9;
            letter-spacing: .08em; text-transform: uppercase;
            margin-bottom: 14px;
        }
        .section-label::before {
            content: ''; width: 8px; height: 8px; border-radius: 50%;
            background: #6d28d9; display: inline-block;
        }
        .section-title {
            font-size: clamp(28px, 4vw, 42px); font-weight: 700;
            color: #111; line-height: 1.2; margin-bottom: 12px;
        }
        .section-title span { color: #aaa; font-weight: 300; }
        .section-sub { font-size: 16px; color: #666; line-height: 1.7; }

        /* ─── Plugin Cards ─── */
        .plugin-card {
            background: #fff; border: 1px solid #ebebeb;
            border-radius: 4px; padding: 20px;
            transition: box-shadow .25s, border-color .25s, transform .25s;
        }
        .plugin-card:hover {
            box-shadow: 0 8px 32px rgba(109,40,217,0.10);
            border-color: #c4b5fd;
            transform: translateY(-3px);
        }
        .plugin-icon {
            width: 40px; height: 40px; border-radius: 4px;
            background: #f3f0ff; display: flex; align-items: center;
            justify-content: center; margin-bottom: 12px;
        }

        /* ─── Pricing ─── */
        .pricing-card {
            background: #fff; border: 1.5px solid #e8e8e8;
            border-radius: 4px; padding: 32px;
            transition: box-shadow .25s, border-color .25s;
        }
        .pricing-card:hover {
            box-shadow: 0 16px 48px rgba(109,40,217,0.12);
            border-color: #a78bfa;
        }
        .pricing-card.featured {
            background: #111; border-color: #111; color: #fff;
        }
        .pricing-card.featured .text-gray-500 { color: #aaa !important; }
        .pricing-card.featured .text-gray-600 { color: #ccc !important; }
        .pricing-card.featured .border-gray-100 { border-color: #333 !important; }
        .pricing-check { color: #6d28d9; font-weight: 600; margin-right: 6px; }
        .pricing-card.featured .pricing-check { color: #a78bfa; }

        /* ─── Feature highlights ─── */
        .feature-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; border: 1px solid #e8e8e8;
            border-radius: 100px; padding: 6px 14px;
            font-size: 13px; font-weight: 500; color: #444;
        }

        /* ─── Trust logos marquee ─── */
        .trust-marquee-track {
            display: flex;
            width: max-content;
            animation: trustScroll 28s linear infinite;
        }
        .trust-marquee-track:hover { animation-play-state: paused; }
        @keyframes trustScroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .trust-marquee-wrap {
            overflow: hidden;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, black 10%, black 90%, transparent 100%);
            mask-image: linear-gradient(90deg, transparent 0%, black 10%, black 90%, transparent 100%);
        }
        .trust-logo {
            opacity: .75;
            transition: opacity .2s;
            flex-shrink: 0;
        }
        .trust-logo:hover { opacity: 1; }

        /* ─── CTA Banner ─── */
        /* ── Sticky CTA banner ── */
        .cta-banner {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
            z-index: 99; background: #111; color: #fff;
            padding: 12px 20px 12px 24px; border-radius: 100px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.28);
            white-space: nowrap;
            transition: opacity .4s, transform .4s;
        }
        .cta-banner.hidden-banner { opacity: 0; pointer-events: none; transform: translate(-50%, 24px); }
        @media (max-width: 560px) {
            .cta-banner {
                left: 12px; right: 12px; bottom: 14px;
                transform: none;
                white-space: normal;
                border-radius: 18px;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 14px 16px 14px 16px;
                position: fixed;
            }
            .cta-banner.hidden-banner { transform: translateY(24px); }
            .cta-banner-text { padding-right: 28px; font-size: 13px; }
            .cta-banner-close { position: absolute; top: 12px; right: 14px; }
            .cta-banner a { display: block; text-align: center; }
        }

        /* ─── Scroll reveal ─── */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ─── Dot grid background ─── */
        .dot-grid {
            background-image: radial-gradient(circle, #d4d4d4 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .island-divider { transition: background 0.3s; }
        .dynamic-island.expanded .island-divider { background: rgba(0,0,0,0.10) !important; }

        /* ─── Footer ─── */
        footer { background: #fafafa; border-top: 1px solid #ebebeb; }

        /* ══════════════════════ DARK MODE ══════════════════════ */
        .dark body { background: #0c0c10; color: #e8e8f0; }

        /* Navbar */
        .dark .dynamic-island {
            background: rgba(18,18,24,0.95);
            border-color: rgba(255,255,255,0.07);
            box-shadow: 0 8px 48px rgba(0,0,0,0.4), 0 2px 12px rgba(0,0,0,0.3);
        }
        .dark .dynamic-island.expanded {
            background: rgba(18,18,24,0.98);
            border-color: rgba(255,255,255,0.09);
            box-shadow: 0 12px 56px rgba(0,0,0,0.5), 0 2px 16px rgba(0,0,0,0.35);
        }
        .dark .island-logo-text { color: #f0f0f5; }
        .dark .island-divider   { background: rgba(255,255,255,0.1) !important; }
        .dark .dynamic-island.expanded .island-divider { background: rgba(255,255,255,0.09) !important; }
        .dark .nav-link         { color: #8b8b9a; }
        .dark .nav-link:hover   { color: #f0f0f5; }
        .dark .island-login     { color: #7a7a88; }
        .dark .island-login:hover { color: #f0f0f5; }
        .dark .island-signup    { background: #7c3aed; color: #fff; }
        .dark .island-signup:hover { background: #6d28d9; }
        .dark .island-hamburger span { background: #e0e0ea; }
        .dark .mobile-nav-drawer { border-top-color: rgba(255,255,255,0.07); }
        .dark .mobile-nav-link  { color: #9898a8; }
        .dark .mobile-nav-link:hover { background: rgba(255,255,255,0.05); color: #f0f0f5; }
        .dark .mobile-nav-cta .m-login  { background: rgba(255,255,255,0.07); color: #e0e0ea; }
        .dark .mobile-nav-cta .m-login:hover { background: rgba(255,255,255,0.12); }
        .dark .mobile-nav-cta .m-signup { background: #7c3aed; color: #fff; }
        .dark .mobile-nav-cta .m-signup:hover { background: #6d28d9; }

        /* Dropdown */
        .dark .dropdown-menu {
            background: #16161e;
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 24px 72px rgba(0,0,0,0.6);
        }
        .dark .dropdown-menu p { color: #555566; }
        .dark .dropdown-menu a:hover { background: rgba(255,255,255,0.05); }
        .dark .dropdown-menu .text-gray-800 { color: #d8d8e8 !important; }
        .dark .dropdown-menu .text-gray-400 { color: #55556a !important; }
        .dark .dropdown-menu .w-7.bg-brand-50 { background: rgba(109,40,217,0.18) !important; }
        .dark .dropdown-menu .border-gray-100 { border-color: rgba(255,255,255,0.07) !important; }
        .dark .dropdown-menu .text-brand-600  { color: #a78bfa !important; }
        .dark .dropdown-menu .text-gray-400:last-child { color: #44445a !important; }

        /* Hero — always dark (video bg), same in light/dark */
        .hero-bg { background: #07060f; }
        .hero-bg .inline-flex.bg-white { background: rgba(255,255,255,0.1) !important; border-color: rgba(255,255,255,0.15) !important; color: #c0c0d0 !important; }
        .hero-bg h1 { color: #f0f0f8; }
        .hero-bg p   { color: #a0a0b8; }
        .hero-cta-group { background: rgba(255,255,255,0.1); }
        .hero-btn-primary { background: #7c3aed; color: #fff; }
        .hero-btn-primary:hover { background: #6d28d9; }
        .hero-btn-primary .arrow-circle { background: #fff; }
        .hero-btn-secondary { color: #c4c4d4; }
        .hero-btn-secondary .play-circle { background: rgba(255,255,255,0.15); }
        .hero-btn-secondary:hover .play-circle { background: rgba(255,255,255,0.25); }
        .hero-bg .bg-brand-100 { background: rgba(109,40,217,0.3) !important; }
        .hero-bg .text-brand-700 { color: #c4b5fd !important; }
        /* Dark mode — hero is already dark, no overrides needed */
        .dark .hero-bg { background: #07060f; }

        /* Dashboard mockup */
        .dark .dashboard-mockup { background: #16161e; border-color: rgba(255,255,255,0.07); box-shadow: 0 32px 80px rgba(0,0,0,0.5); }
        .dark .mockup-titlebar  { background: #0f0f16; border-bottom-color: rgba(255,255,255,0.07); }
        .dark .mockup-titlebar span { color: #44445a !important; }
        .dark .stat-card { background: #1a1a24; border-color: rgba(255,255,255,0.06); }

        /* Sections */
        .dark .section-title { color: #f0f0f5; }
        .dark .section-title span { color: #3a3a50; }
        .dark .section-sub  { color: #6e6e80; }
        .dark .section-label { color: #a78bfa; }
        .dark .section-label::before { background: #a78bfa; }

        /* Plugin cards */
        .dark .plugin-card { background: #16161e; border-color: rgba(255,255,255,0.07); }
        .dark .plugin-card:hover { box-shadow: 0 8px 32px rgba(139,92,246,0.2); border-color: rgba(139,92,246,0.4); }
        .dark .plugin-icon { background: rgba(109,40,217,0.2); }
        .dark .plugin-card .text-gray-900 { color: #e0e0ea !important; }
        .dark .plugin-card .text-gray-500 { color: #55556a !important; }

        /* Pricing */
        .dark .pricing-card { background: #16161e; border-color: rgba(255,255,255,0.07); }
        .dark .pricing-card:hover { box-shadow: 0 16px 48px rgba(139,92,246,0.25); border-color: rgba(139,92,246,0.5); }
        .dark .pricing-card.featured { background: #7c3aed; border-color: #7c3aed; }
        .dark .pricing-card.featured .text-gray-500 { color: rgba(255,255,255,0.6) !important; }
        .dark .pricing-card.featured .text-gray-600 { color: rgba(255,255,255,0.7) !important; }
        .dark .pricing-card.featured .border-gray-100 { border-color: rgba(255,255,255,0.2) !important; }
        .dark .pricing-card .text-gray-900 { color: #f0f0f5 !important; }
        .dark .pricing-card .text-gray-500 { color: #55556a !important; }
        .dark .pricing-card .text-gray-600 { color: #7070808 !important; }
        .dark .pricing-card .border-gray-100 { border-color: rgba(255,255,255,0.06) !important; }
        .dark .pricing-card .bg-brand-600 { background: #7c3aed !important; }

        /* Features */
        .dark .feature-pill { background: #16161e; border-color: rgba(255,255,255,0.07); color: #9898a8; }

        /* Trust bar */
        .dark .trust-logo { filter: none; opacity: 1; }
        .dark .trust-logo:hover { opacity: 1; filter: none; }

        /* Stats bar */
        .dark .bg-gray-50  { background: #111118 !important; }
        .dark .border-gray-100 { border-color: rgba(255,255,255,0.06) !important; }

        /* Inputs / forms */
        .dark input, .dark textarea, .dark select {
            background: #1a1a24 !important;
            border-color: rgba(255,255,255,0.1) !important;
            color: #e0e0ea !important;
        }
        .dark input::placeholder, .dark textarea::placeholder { color: #44445a !important; }

        /* General Tailwind class overrides */
        .dark .bg-white       { background: #16161e !important; }
        .dark .bg-gray-100    { background: #1a1a24 !important; }
        .dark .bg-gray-50     { background: #111118 !important; }
        .dark .text-gray-900  { color: #f0f0f5 !important; }
        .dark .text-gray-800  { color: #d8d8e8 !important; }
        .dark .text-gray-700  { color: #c0c0d0 !important; }
        .dark .text-gray-600  { color: #9090a0 !important; }
        .dark .text-gray-500  { color: #6e6e80 !important; }
        .dark .text-gray-400  { color: #55556a !important; }
        .dark .border-gray-200{ border-color: rgba(255,255,255,0.08) !important; }
        .dark .border-gray-100{ border-color: rgba(255,255,255,0.05) !important; }
        .dark .divide-gray-100 > * + * { border-color: rgba(255,255,255,0.06) !important; }
        .dark .shadow-sm      { box-shadow: 0 2px 8px rgba(0,0,0,0.4) !important; }

        /* CTA Banner */
        .dark .cta-banner     { background: #7c3aed; }
        .dark .cta-banner a   { background: #fff; color: #7c3aed; }
        .dark .cta-banner a:hover { background: #f0f0f5; }

        /* Footer */
        .dark footer { background: #0a0a0e; border-top-color: rgba(255,255,255,0.06); }

        /* Trust bar */
        .dark .trust-bar { background: #0c0c14; border-color: rgba(255,255,255,0.06); }
        .dark .trust-bar > p { color: #f0f0f5 !important; }
        .dark .trust-logo span { color: #ffffff; }
        .dark .trust-logo div  { background: #2a2a3a; opacity: .55; }

        /* Dark mode toggle button */
        .theme-toggle {
            display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%;
            background: transparent; border: 1px solid rgba(0,0,0,0.1);
            cursor: pointer; transition: background .2s, border-color .2s;
            color: #555; flex-shrink: 0;
        }
        .theme-toggle:hover { background: rgba(0,0,0,0.05); color: #111; }
        .dark .theme-toggle   { border-color: rgba(255,255,255,0.1); color: #a78bfa; }
        .dark .theme-toggle:hover { background: rgba(255,255,255,0.07); }
        .theme-toggle .icon-sun  { display: none; }
        .theme-toggle .icon-moon { display: block; }
        .dark .theme-toggle .icon-sun  { display: block; }
        .dark .theme-toggle .icon-moon { display: none; }

        /* ══════════════════════════════════════
           AI SHOWCASE — shared / light mode
           ══════════════════════════════════════ */
        .ai-showcase-section {
            background: #f7f5ff;
            color: #111;
            position: relative;
        }

        /* Animated gradient background — light */
        .ai-bg-gradient {
            position: absolute; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 70% 55% at 15% 50%, rgba(109,40,217,0.07) 0%, transparent 65%),
                radial-gradient(ellipse 55% 45% at 80% 25%, rgba(59,130,246,0.05) 0%, transparent 60%),
                radial-gradient(ellipse 45% 35% at 60% 90%, rgba(16,185,129,0.04) 0%, transparent 55%);
        }
        .ai-grid-overlay {
            position: absolute; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(109,40,217,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(109,40,217,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, black 30%, transparent 80%);
        }
        /* Glowing orbs */
        .ai-orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); z-index: 0;
            animation: aiOrbFloat 8s ease-in-out infinite;
        }
        .ai-orb-1 { width: 300px; height: 300px; background: #7c3aed; opacity: 0.08; top: -80px; left: -60px; animation-delay: 0s; }
        .ai-orb-2 { width: 200px; height: 200px; background: #2563eb; opacity: 0.06; top: 40%; right: -50px; animation-delay: -3s; }
        .ai-orb-3 { width: 250px; height: 250px; background: #059669; opacity: 0.05; bottom: -60px; left: 40%; animation-delay: -5s; }
        @keyframes aiOrbFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(20px,-20px) scale(1.05); }
        }

        /* Section badge */
        .ai-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(109,40,217,0.08); border: 1px solid rgba(109,40,217,0.2);
            color: #6d28d9; font-size: 12px; font-weight: 600;
            padding: 6px 16px; border-radius: 100px;
            letter-spacing: .06em; text-transform: uppercase;
            margin-bottom: 18px;
        }
        .ai-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #7c3aed;
            animation: aiBadgePulse 2s ease-in-out infinite;
        }
        @keyframes aiBadgePulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.4; transform: scale(0.8); }
        }

        /* Headings */
        .ai-title {
            font-size: clamp(32px,5vw,52px); font-weight: 800;
            color: #111; line-height: 1.15; margin-bottom: 16px;
            letter-spacing: -0.5px;
        }
        .ai-gradient-text {
            background: linear-gradient(135deg, #7c3aed 0%, #2563eb 50%, #059669 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .ai-subtitle {
            font-size: 17px; color: #666; line-height: 1.7;
            max-width: 640px; margin: 0 auto;
        }
        .ai-subtitle em { color: #7c3aed; font-style: normal; font-weight: 600; }

        /* Capability cards — light */
        .ai-cap-card {
            display: flex; gap: 16px; align-items: flex-start;
            background: #fff;
            border: 1px solid #e8e4f8;
            border-radius: 4px; padding: 18px 20px;
            transition: background .25s, border-color .25s, transform .25s, box-shadow .25s;
        }
        .ai-cap-card:hover { background: #faf8ff; transform: translateX(4px); box-shadow: 0 4px 20px rgba(109,40,217,0.08); }
        .ai-cap-violet:hover { border-color: rgba(109,40,217,0.35); }
        .ai-cap-blue:hover   { border-color: rgba(37,99,235,0.3); }
        .ai-cap-emerald:hover{ border-color: rgba(5,150,105,0.3); }
        .ai-cap-amber:hover  { border-color: rgba(217,119,6,0.3); }

        .ai-cap-icon {
            width: 42px; height: 42px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ai-cap-icon-violet { background: #f3f0ff; color: #7c3aed; }
        .ai-cap-icon-blue   { background: #eff6ff; color: #2563eb; }
        .ai-cap-icon-emerald{ background: #ecfdf5; color: #059669; }
        .ai-cap-icon-amber  { background: #fffbeb; color: #d97706; }

        .ai-cap-title { font-size: 14px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .ai-cap-body  { font-size: 13px; color: #666; line-height: 1.6; }

        /* ── AI Chat Window — light ── */
        .ai-chat-window {
            background: #fff;
            border: 1px solid rgba(109,40,217,0.2);
            border-radius: 4px; overflow: hidden;
            box-shadow: 0 4px 6px rgba(109,40,217,0.04), 0 20px 50px rgba(109,40,217,0.08);
        }
        .ai-chat-titlebar {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            border-bottom: 1px solid rgba(109,40,217,0.15);
        }
        .ai-chat-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ai-live-dot {
            width: 7px; height: 7px; border-radius: 50%; background: #34d399;
            animation: livePulse 2s ease-in-out infinite;
            box-shadow: 0 0 6px #34d399;
        }
        @keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .ai-chat-body {
            padding: 20px; min-height: 300px; max-height: 340px;
            overflow-y: auto; background: #faf8ff;
        }
        .ai-chat-body::-webkit-scrollbar { width: 4px; }
        .ai-chat-body::-webkit-scrollbar-track { background: transparent; }
        .ai-chat-body::-webkit-scrollbar-thumb { background: rgba(109,40,217,0.2); border-radius: 2px; }

        /* Context pill */
        .ai-context-pill {
            background: #f3f0ff; border: 1px solid #ddd6fe;
            color: #7c3aed; font-size: 11px; padding: 4px 12px; border-radius: 100px;
        }

        /* Message rows */
        .ai-msg-row { display: flex; margin-bottom: 12px; }
        .ai-msg-user { justify-content: flex-end; }
        .ai-msg-aura { justify-content: flex-start; }

        .ai-bubble-user {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff; font-size: 13px; padding: 10px 14px;
            border-radius: 18px 18px 4px 18px; max-width: 75%;
            line-height: 1.5;
        }
        .ai-bubble-aura-wrap { max-width: 88%; }
        .ai-bubble-aura {
            background: #fff; border: 1px solid #e8e4f8;
            color: #333; font-size: 13px; padding: 12px 14px;
            border-radius: 18px 18px 18px 4px; line-height: 1.6;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .ai-bubble-aura strong { color: #111; font-weight: 600; }
        .ai-bubble-aura p:not(:first-child) { margin-top: 6px; }

        /* Stats inside bubble */
        .ai-stat-grid { display: flex; gap: 8px; margin-top: 8px; }
        .ai-stat-item {
            flex: 1; background: #f8f7ff; border: 1px solid #ddd6fe;
            border-radius: 4px; padding: 8px; text-align: center;
        }
        .ai-stat-num { display: block; font-size: 18px; font-weight: 700; line-height: 1.1; }
        .ai-stat-label { font-size: 10px; color: #888; margin-top: 2px; display: block; }
        .ai-c-green  { color: #059669; }
        .ai-c-blue   { color: #2563eb; }
        .ai-c-amber  { color: #d97706; }

        /* Tickets in bubble */
        .ai-ticket-item {
            display: flex; align-items: flex-start; gap: 8px;
            background: #f8f7ff; border: 1px solid #e8e4f8; border-radius: 4px;
            padding: 7px 10px; font-size: 12px; color: #444;
        }
        .ai-ticket-badge { font-size: 11px; white-space: nowrap; flex-shrink: 0; }
        .ai-ticket-urgent { border-left: 2px solid #ef4444; }
        .ai-ticket-high   { border-left: 2px solid #f97316; }

        /* Typing indicator */
        .ai-typing-indicator {
            background: #fff; border: 1px solid #e8e4f8;
            border-radius: 18px 18px 18px 4px;
            padding: 12px 16px; display: inline-flex; gap: 4px; align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .ai-typing-indicator span {
            width: 7px; height: 7px; border-radius: 50%; background: #7c3aed;
            animation: typingBounce 1.2s ease-in-out infinite;
        }
        .ai-typing-indicator span:nth-child(2) { animation-delay: .2s; }
        .ai-typing-indicator span:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce {
            0%,60%,100% { transform: translateY(0); opacity:.4; }
            30%          { transform: translateY(-6px); opacity:1; }
        }

        /* Chat footer */
        .ai-chat-footer {
            padding: 14px 16px;
            border-top: 1px solid #e8e4f8;
            background: #fff;
        }
        .ai-input-bar {
            display: flex; align-items: center; gap: 10px;
            background: #f7f5ff; border: 1px solid #ddd6fe;
            border-radius: 100px; padding: 10px 12px 10px 16px;
            margin-bottom: 10px;
        }
        .ai-input-text { flex: 1; font-size: 13px; color: #999; }
        .ai-input-send {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg,#7c3aed,#4f46e5);
            display: flex; align-items: center; justify-content: center;
            color: #fff; flex-shrink: 0;
        }
        /* Quick chips */
        .ai-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .ai-chip {
            background: #f3f0ff; border: 1px solid #ddd6fe;
            color: #7c3aed; font-size: 11px; padding: 4px 10px; border-radius: 100px;
            cursor: pointer; transition: background .2s;
            white-space: nowrap;
        }
        .ai-chip:hover { background: #ede9fe; color: #6d28d9; }

        /* Bottom stats — light */
        .ai-bottom-stat {
            background: #fff; border: 1px solid #e8e4f8;
            border-radius: 4px; padding: 20px; text-align: center;
            transition: background .25s, border-color .25s, transform .25s, box-shadow .25s;
        }
        .ai-bottom-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(109,40,217,0.1); }
        .ai-bottom-violet:hover { border-color: rgba(109,40,217,0.3); }
        .ai-bottom-blue:hover   { border-color: rgba(37,99,235,0.25); }
        .ai-bottom-emerald:hover{ border-color: rgba(5,150,105,0.25); }
        .ai-bottom-amber:hover  { border-color: rgba(217,119,6,0.25); }

        .ai-bottom-num {
            font-size: 28px; font-weight: 800; letter-spacing: -1px;
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: 6px;
        }
        .ai-bottom-label { font-size: 12px; color: #888; line-height: 1.4; }

        /* Message animation */
        @keyframes aiMsgIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ai-msg-animate { animation: aiMsgIn .35s ease-out forwards; }

        /* ══════════════════════════════════════
           AI SHOWCASE — dark mode overrides
           ══════════════════════════════════════ */
        .dark .ai-showcase-section { background: #07060f; color: #e8e8f4; }
        .dark .ai-bg-gradient {
            background:
                radial-gradient(ellipse 80% 60% at 20% 50%, rgba(109,40,217,0.18) 0%, transparent 65%),
                radial-gradient(ellipse 60% 50% at 80% 30%, rgba(59,130,246,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 60% 85%, rgba(16,185,129,0.08) 0%, transparent 55%);
        }
        .dark .ai-orb-1 { opacity: 0.35; }
        .dark .ai-orb-2 { opacity: 0.25; }
        .dark .ai-orb-3 { opacity: 0.2; }

        .dark .ai-badge { background: rgba(139,92,246,0.15); border-color: rgba(139,92,246,0.3); color: #c4b5fd; }
        .dark .ai-badge-dot { background: #a78bfa; }
        .dark .ai-title { color: #f0f0f8; }
        .dark .ai-gradient-text {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 50%, #34d399 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .dark .ai-subtitle { color: #8080a0; }
        .dark .ai-subtitle em { color: #a78bfa; }

        .dark .ai-cap-card { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); }
        .dark .ai-cap-card:hover { background: rgba(255,255,255,0.07); box-shadow: none; }
        .dark .ai-cap-violet:hover { border-color: rgba(167,139,250,0.35); }
        .dark .ai-cap-blue:hover   { border-color: rgba(96,165,250,0.35); }
        .dark .ai-cap-emerald:hover{ border-color: rgba(52,211,153,0.35); }
        .dark .ai-cap-amber:hover  { border-color: rgba(251,191,36,0.35); }
        .dark .ai-cap-icon-violet { background: rgba(139,92,246,0.2); color: #a78bfa; }
        .dark .ai-cap-icon-blue   { background: rgba(59,130,246,0.2);  color: #60a5fa; }
        .dark .ai-cap-icon-emerald{ background: rgba(16,185,129,0.2);  color: #34d399; }
        .dark .ai-cap-icon-amber  { background: rgba(245,158,11,0.2);  color: #fbbf24; }
        .dark .ai-cap-title { color: #e8e8f4; }
        .dark .ai-cap-body  { color: #70708a; }

        .dark .ai-chat-window { background: rgba(255,255,255,0.04); border-color: rgba(139,92,246,0.25); box-shadow: 0 0 0 1px rgba(139,92,246,0.1), 0 32px 80px rgba(0,0,0,0.5), 0 0 60px rgba(139,92,246,0.1); }
        .dark .ai-chat-titlebar { background: linear-gradient(135deg, rgba(109,40,217,0.5), rgba(37,99,235,0.3)); border-bottom-color: rgba(139,92,246,0.2); }
        .dark .ai-chat-body { background: transparent; }
        .dark .ai-chat-body::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); }
        .dark .ai-context-pill { background: rgba(139,92,246,0.12); border-color: rgba(139,92,246,0.2); color: #9580c8; }

        .dark .ai-bubble-aura { background: rgba(255,255,255,0.07); border-color: rgba(255,255,255,0.1); color: #d0d0e8; box-shadow: none; }
        .dark .ai-bubble-aura strong { color: #e8e8f8; }
        .dark .ai-stat-item { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08); }
        .dark .ai-stat-label { color: #70708a; }
        .dark .ai-c-green { color: #34d399; }
        .dark .ai-c-blue  { color: #60a5fa; }
        .dark .ai-c-amber { color: #fbbf24; }
        .dark .ai-ticket-item { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.08); color: #c0c0d8; }

        .dark .ai-typing-indicator { background: rgba(255,255,255,0.07); border-color: rgba(255,255,255,0.1); box-shadow: none; }
        .dark .ai-chat-footer { background: rgba(0,0,0,0.3); border-top-color: rgba(255,255,255,0.07); }
        .dark .ai-input-bar { background: rgba(255,255,255,0.07); border-color: rgba(139,92,246,0.25); }
        .dark .ai-input-text { color: #60608a; }
        .dark .ai-chip { background: rgba(139,92,246,0.12); border-color: rgba(139,92,246,0.2); color: #9580c8; }
        .dark .ai-chip:hover { background: rgba(139,92,246,0.25); color: #c4b5fd; }

        .dark .ai-bottom-stat { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); }
        .dark .ai-bottom-stat:hover { box-shadow: none; }
        .dark .ai-bottom-violet:hover { border-color: rgba(167,139,250,0.3); }
        .dark .ai-bottom-blue:hover   { border-color: rgba(96,165,250,0.3); }
        .dark .ai-bottom-emerald:hover{ border-color: rgba(52,211,153,0.3); }
        .dark .ai-bottom-amber:hover  { border-color: rgba(251,191,36,0.3); }
        .dark .ai-bottom-num { background: linear-gradient(135deg, #a78bfa, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .dark .ai-bottom-label { color: #70708a; }
    </style>
</head>
<body>

<!-- ══════════════════════ DYNAMIC ISLAND NAVBAR ══════════════════════ -->
<nav class="dynamic-island" id="dynamicIsland">
    <!-- Logo -->
    <a href="/" class="flex items-center flex-shrink-0" style="text-decoration:none;">
        <img src="{{ asset('images/aura.png') }}" alt="Logo" class="island-logo-icon h-7 w-auto object-contain rounded">
    </a>

    <!-- Divider (desktop only) -->
    <div class="island-divider" style="width:1px; height:16px; background:rgba(0,0,0,0.12); flex-shrink:0;"></div>

    <!-- Nav Links (desktop only) -->
    <div class="island-nav-links flex items-center gap-8" style="flex:1; justify-content:center;">
        <a href="#" class="nav-link">Home</a>

        <!-- Products dropdown -->
        <div class="dropdown-trigger">
            <a href="#products" class="nav-link flex items-center gap-1">
                Products
                <svg class="w-2.5 h-2.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </a>
            <div class="dropdown-menu">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">All Modules</p>
                <div class="grid grid-cols-3 gap-1.5">
                    @php
                    $modules = [
                        ['Accounting',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M9 7h6m-6 4h3"/>','Finance'],
                        ['Invoicing',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>','Billing'],
                        ['CRM',          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>','Sales'],
                        ['Inventory',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>','Stock'],
                        ['HR',           '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>','People'],
                        ['Payroll',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>','Payroll'],
                        ['Projects',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>','PM'],
                        ['Purchases',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>','Procurement'],
                        ['Sales',        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>','Sales'],
                        ['Manufacturing','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>','Ops'],
                        ['Helpdesk',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>','Support'],
                        ['Field Service','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>','Service'],
                        ['Timesheets',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>','Time'],
                        ['Recruitment',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>','HR'],
                        ['Leave Mgmt',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>','HR'],
                        ['Website',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>','Web'],
                        ['Analytics',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>','Reports'],
                    ];
                    @endphp
                    @foreach($modules as $m)
                    <a href="/admin" class="flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 rounded transition-colors">
                        <div class="w-7 h-7 rounded bg-brand-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $m[1] !!}</svg>
                        </div>
                        <div>
                            <div class="text-[12px] font-medium text-gray-800 leading-tight">{{ $m[0] }}</div>
                            <div class="text-[10px] text-gray-400">{{ $m[2] }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">17 modules included in all plans</span>
                    <a href="/onboard/register" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Start free →</a>
                </div>
            </div>
        </div>

        <a href="#pricing" class="nav-link">Pricing</a>
        <a href="#ai" class="nav-link" style="color:#a78bfa;font-weight:600;">✦ Aura AI</a>
        <a href="#about" class="nav-link">About</a>
        <a href="#contact" class="nav-link">Contact</a>
    </div>

    <!-- Auth CTA (desktop only) -->
    <div class="island-auth flex items-center gap-3 flex-shrink-0">
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
            <!-- Moon (shown in light mode) -->
            <svg class="icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <!-- Sun (shown in dark mode) -->
            <svg class="icon-sun w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            </svg>
        </button>
        <a href="/admin/login" class="island-login">Log in</a>
        <a href="/onboard/register" class="island-signup">Sign Up</a>
    </div>

    <!-- Hamburger (mobile only) -->
    <button class="island-hamburger" id="mobileMenuBtn" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>

    <!-- Mobile Drawer -->
    <div class="mobile-nav-drawer" id="mobileDrawer">
        <a href="#" class="mobile-nav-link">Home</a>
        <a href="#products" class="mobile-nav-link">Products</a>
        <a href="#pricing" class="mobile-nav-link">Pricing</a>
        <a href="#ai" class="mobile-nav-link" style="color:#a78bfa;font-weight:600;">✦ Aura AI</a>
        <a href="#about" class="mobile-nav-link">About</a>
        <a href="#contact" class="mobile-nav-link">Contact</a>
        <div class="mobile-nav-cta">
            <a href="/admin/login" class="m-login">Log in</a>
            <a href="/onboard/register" class="m-signup">Sign Up</a>
        </div>
    </div>
</nav>

<!-- ══════════════════════ HERO ══════════════════════ -->
<section class="hero-bg relative overflow-hidden flex items-center" style="min-height: 90vh;">

    <!-- Video background -->
    <div class="hero-video-bg">
        <video autoplay muted loop playsinline preload="auto"
               poster="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1600&q=80&fm=jpg">
            <source src="https://videos.pexels.com/video-files/3129957/3129957-uhd_2560_1440_30fps.mp4" type="video/mp4">
            <source src="https://videos.pexels.com/video-files/3571264/3571264-uhd_2560_1440_25fps.mp4" type="video/mp4">
        </video>
    </div>
    <!-- Gradient overlay for readability -->
    <div class="hero-video-overlay"></div>

    <!-- Keep wiremesh div (hidden via CSS) so dark-mode selectors don't throw errors -->
    <div class="hero-wiremesh"></div>

    <div class="relative z-10 w-full max-w-4xl mx-auto px-6 py-20 text-center">
        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6" style="letter-spacing:-1px; color:#f0f0f8;">
            Boost Business Efficiency<br class="hidden sm:block">
            With Our <span class="relative inline-block">
                <span class="relative z-10" style="color:#c4b5fd;">ERP solutions</span>
                <span class="absolute inset-0 rounded -mx-1 -my-0.5" style="z-index:0; background:rgba(109,40,217,0.25);"></span>
            </span>
        </h1>

        <p class="text-base sm:text-lg mb-10 max-w-xl mx-auto leading-relaxed px-2" style="color:#a0a0b8;">
            Streamline every part of your business — from accounting to HR to inventory — in one unified platform designed for modern teams.
        </p>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-0">
            <a href="/onboard/register" class="hero-btn-primary w-full sm:w-auto justify-center">
                Get 14 Days Free Trial
                <span class="arrow-circle">
                    <svg width="14" height="14" fill="none" stroke="black" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                </span>
            </a>
            <a href="#demo" class="hero-btn-secondary">
                <span class="play-circle">
                    <svg width="12" height="12" fill="white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </span>
                Watch Demo
            </a>
        </div>
    </div>
</section>

<!-- Trust bar -->
<div class="trust-bar bg-white border-y border-gray-100 py-6 overflow-hidden">
    <p class="text-center text-xs text-gray-400 mb-5 uppercase tracking-widest font-medium">Trusted by 10,000+ founders & business owners</p>
    <div class="trust-marquee-wrap">
        <div class="trust-marquee-track">
            @php
            $brands = ['Techcorp','NovaBiz','StartupX','VentureHub','ScaleIQ','GrowthCo','Pinnacle','CoreBiz','NexaGroup','AlphaOps','SwiftScale','BuildCo'];
            $doubled = array_merge($brands, $brands);
            @endphp
            @foreach($doubled as $brand)
            <div class="trust-logo flex items-center gap-2 cursor-default mx-8">
                <div class="w-5 h-5 rounded bg-gray-300 flex-shrink-0"></div>
                <span class="font-semibold text-sm text-gray-700 whitespace-nowrap">{{ $brand }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ══════════════════════ STATS ══════════════════════ -->
<section class="bg-white py-14">
    <div class="max-w-5xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([['17+','Modules','Every business function covered'],['50K+','Businesses','Growing with AureusERP'],['99.9%','Uptime','Enterprise-grade reliability'],['<2s','Load Time','Blazing fast on any device']] as $s)
            <div class="text-center reveal">
                <div class="text-4xl font-bold text-gray-900 mb-1">{{ $s[0] }}</div>
                <div class="font-semibold text-gray-700 text-sm mb-1">{{ $s[1] }}</div>
                <div class="text-xs text-gray-400">{{ $s[2] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════════════ BENEFITS / PRODUCTS ══════════════════════ -->
<section id="products" class="bg-gray-50 py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="max-w-xl mb-12 reveal">
            <div class="section-label">Products</div>
            <h2 class="section-title">Smarter Business <span>Decisions</span></h2>
            <p class="section-sub">Start with the modules you need. Add more as you grow. All your business data in one place.</p>
        </div>

        @php
        $plugins = [
            ['Accounting',      'Full general ledger, chart of accounts, journal entries, bank reconciliation and financial reporting.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M9 7h6m-6 4h3"/>','Finance'],
            ['Invoicing',       'Automated invoicing, payment reminders, credit notes, and real-time outstanding balances.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>','Billing'],
            ['CRM',             'Track leads, deals, pipelines, and customer relationships from first contact to closed deal.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>','Sales'],
            ['Inventory',       'Multi-warehouse stock management, lot/serial tracking, automated reorder rules, and stock valuation.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>','Stock'],
            ['Human Resources', 'Employee profiles, org chart, document management, onboarding workflows, and self-service portal.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>','People'],
            ['Payroll',         'Payslip generation, salary rules, deductions, tax compliance, and direct bank transfers.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>','Payroll'],
            ['Projects',        'Kanban & Gantt views, task dependencies, time tracking, milestone tracking, and client reporting.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>','PM'],
            ['Purchases',       'RFQ management, vendor comparisons, purchase orders, goods receipts, and spend analytics.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>','Procurement'],
            ['Sales',           'Quotations, sales orders, delivery confirmations, commission tracking, and revenue forecasting.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>','Sales'],
            ['Manufacturing',   'Work orders, BOMs, routing, MRP planning, quality control, and production cost tracking.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>','Ops'],
            ['Helpdesk',        'Ticket management, SLA tracking, customer portal, canned responses, and CSAT surveys.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>','Support'],
            ['Field Service',   'Job scheduling, technician dispatch, mobile checklists, on-site signatures, and invoicing.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>','Service'],
            ['Timesheets',      'Employee time logging, project billing by hours, approval workflows, and overtime tracking.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>','Time'],
            ['Recruitment',     'Job postings, candidate pipeline, interview scheduling, offer letters, and onboarding handoff.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>','HR'],
            ['Leave Management','Leave policies, balance tracking, approval flows, holiday calendars, and absence reports.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>','HR'],
            ['Website Builder', 'Drag-and-drop page builder, SEO tools, blog, product catalogue, and contact forms.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>','Web'],
            ['Analytics',       'Custom dashboards, KPI widgets, scheduled reports, data exports, and AI-powered insights.',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>','Reports'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($plugins as $p)
            <div class="plugin-card reveal">
                <div class="plugin-icon">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $p[2] !!}</svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-semibold text-sm text-gray-900">{{ $p[0] }}</h3>
                    <span class="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-medium">{{ $p[3] }}</span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $p[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════════════ FEATURES ══════════════════════ -->
<section id="features" class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <!-- Left: Text -->
            <div class="reveal">
                <div class="section-label">Features</div>
                <h2 class="section-title">Everything your team <span>needs to move fast</span></h2>
                <p class="section-sub mb-8">Built for scale. From a 5-person startup to a 500-person enterprise, AureusERP grows with you.</p>

                <div class="space-y-4">
                    @foreach([
                        ['Real-time collaboration','Multiple team members on the same record, with live change indicators and audit trails.'],
                        ['Role-based permissions','Granular access control — restrict by module, record type, or individual field.'],
                        ['Automated workflows','Set triggers and actions to eliminate repetitive manual tasks across any module.'],
                        ['AI-powered analytics','Forecasting, anomaly detection, and smart suggestions powered by your own data.'],
                        ['Multi-company support','Manage multiple business entities with consolidated reporting and shared master data.'],
                    ] as $f)
                    <div class="flex gap-4 group">
                        <div class="w-8 h-8 rounded bg-brand-50 flex items-center justify-center flex-shrink-0 group-hover:bg-brand-100 transition-colors">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900 mb-0.5">{{ $f[0] }}</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ $f[1] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Notification feed mockup -->
            <div class="reveal">
                <div class="bg-gray-50 rounded border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="font-semibold text-sm text-gray-800">Activity Feed</p>
                        <span class="text-xs text-gray-400">Live</span>
                    </div>
                    <div class="space-y-3">
                        @foreach([
                            ['Invoice #INV-1042 paid','$4,200 received from Techcorp Ltd','2m ago','bg-green-400'],
                            ['New lead assigned','Sarah K. from NovaBiz entered pipeline','5m ago','bg-blue-400'],
                            ['Low stock alert','Laptop Stand SKU-441 — only 3 left','12m ago','bg-yellow-400'],
                            ['Leave approved','James O. — 3 days annual leave','1h ago','bg-purple-400'],
                            ['PO created','Purchase Order #PO-2091 sent to vendor','2h ago','bg-brand-400'],
                        ] as $n)
                        <div class="flex items-start gap-3 bg-white rounded p-3 border border-gray-100 shadow-sm">
                            <div class="w-2 h-2 rounded-full {{ $n[3] }} mt-1.5 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-800 truncate">{{ $n[0] }}</p>
                                <p class="text-[11px] text-gray-400">{{ $n[1] }}</p>
                            </div>
                            <span class="text-[10px] text-gray-300 flex-shrink-0">{{ $n[2] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Mini feature pills -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach(['Automated alerts','Role filters','Export CSV','Slack sync'] as $pill)
                        <span class="feature-pill">
                            <svg class="w-3 h-3 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $pill }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ AI SHOWCASE ══════════════════════ -->
<section id="ai" class="ai-showcase-section relative overflow-hidden py-24">

    <!-- Background layers -->
    <div class="ai-bg-gradient"></div>
    <div class="ai-grid-overlay"></div>
    <div class="ai-orb ai-orb-1"></div>
    <div class="ai-orb ai-orb-2"></div>
    <div class="ai-orb ai-orb-3"></div>

    <div class="max-w-6xl mx-auto px-6 relative z-10">

        <!-- Header -->
        <div class="text-center mb-16 reveal">
            
            <h2 class="ai-title">Meet <span class="ai-gradient-text">Aura</span> your intelligent<br>ERP Assistant</h2>
            <p class="ai-subtitle">While other ERPs show you dashboards, Aura <em>understands</em> your business.<br>Ask questions, detect problems, and get answers — all in plain language.</p>
        </div>

        <!-- Main two-column layout -->
        <div class="grid lg:grid-cols-2 gap-12 items-start mb-16">

            <!-- Left: capability callouts -->
            <div class="space-y-5 reveal">
                @foreach([
                    [
                        'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
                        'color' => 'violet',
                        'title' => 'Ask in plain English',
                        'body'  => '"Show me last quarter\'s top 5 customers by revenue" — Aura queries your live ERP data and responds in seconds.',
                    ],
                    [
                        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'color' => 'blue',
                        'title' => 'Proactive anomaly detection',
                        'body'  => 'Aura silently monitors your payroll, inventory, and tickets — surfacing issues before they escalate into crises.',
                    ],
                    [
                        'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                        'color' => 'emerald',
                        'title' => 'Smart business forecasting',
                        'body'  => 'Revenue trends, staffing needs, stock reorder points — Aura synthesises all ERP modules for holistic predictions.',
                    ],
                    [
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'color' => 'amber',
                        'title' => 'Auto-generated reports',
                        'body'  => 'Ask for an HR summary or sales debrief and Aura builds a formatted, shareable report from your actual data.',
                    ],
                ] as $cap)
                <div class="ai-cap-card ai-cap-{{ $cap['color'] }}">
                    <div class="ai-cap-icon ai-cap-icon-{{ $cap['color'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $cap['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="ai-cap-title">{{ $cap['title'] }}</h4>
                        <p class="ai-cap-body">{{ $cap['body'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Right: animated chat mockup -->
            <div class="reveal" style="transition-delay:.15s">
                <div class="ai-chat-window">
                    <!-- Title bar -->
                    <div class="ai-chat-titlebar">
                        <div class="ai-chat-avatar">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Aura AI</p>
                            <p class="text-xs text-violet-300">Connected to your ERP · Live data</p>
                        </div>
                        <div class="ml-auto flex items-center gap-1.5">
                            <span class="ai-live-dot"></span>
                            <span class="text-xs text-violet-400">Live</span>
                        </div>
                    </div>

                    <!-- Chat messages -->
                    <div class="ai-chat-body" id="aiChatMessages">

                        <!-- Context pill -->
                        <div class="flex justify-center mb-4">
                            <span class="ai-context-pill">📊 Connected to AureusERP · 10 employees · 3 open tickets</span>
                        </div>

                        <!-- Conversation items — animated in by JS -->
                        <div class="ai-msg-row ai-msg-user" id="aiMsg1" style="display:none">
                            <div class="ai-bubble-user">What's our payroll status for this month?</div>
                        </div>

                        <div class="ai-msg-row ai-msg-aura" id="aiMsg2" style="display:none">
                            <div class="ai-bubble-aura-wrap">
                                <div class="ai-bubble-aura">
                                    <p>📋 <strong>May 2026 Payroll</strong></p>
                                    <div class="ai-stat-grid">
                                        <div class="ai-stat-item"><span class="ai-stat-num ai-c-green">8</span><span class="ai-stat-label">Draft</span></div>
                                        <div class="ai-stat-item"><span class="ai-stat-num ai-c-blue">2</span><span class="ai-stat-label">Confirmed</span></div>
                                        <div class="ai-stat-item"><span class="ai-stat-num ai-c-amber">0</span><span class="ai-stat-label">Paid</span></div>
                                    </div>
                                    <p class="mt-2">⚠️ No payslips have been marked as <strong>paid</strong> yet. Would you like me to flag this for your finance team?</p>
                                </div>
                            </div>
                        </div>

                        <div class="ai-msg-row ai-msg-user" id="aiMsg3" style="display:none">
                            <div class="ai-bubble-user">Yes, and show me any urgent support tickets</div>
                        </div>

                        <div class="ai-msg-row ai-msg-aura" id="aiMsg4" style="display:none">
                            <div class="ai-bubble-aura-wrap">
                                <div class="ai-bubble-aura">
                                    <p>🎫 <strong>3 open tickets</strong> need attention:</p>
                                    <div class="space-y-2 mt-2">
                                        <div class="ai-ticket-item ai-ticket-urgent">
                                            <span class="ai-ticket-badge">🔴 Urgent</span>
                                            <span>Login system failing for Enterprise clients</span>
                                        </div>
                                        <div class="ai-ticket-item ai-ticket-high">
                                            <span class="ai-ticket-badge">🟠 High</span>
                                            <span>Invoice discrepancy on PO-2041</span>
                                        </div>
                                        <div class="ai-ticket-item">
                                            <span class="ai-ticket-badge">🟡 Medium</span>
                                            <span>Dashboard loading slowly for 3 users</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-violet-300">I've notified your support team about the urgent ticket. 📨</p>
                                </div>
                            </div>
                        </div>

                        <!-- Typing indicator -->
                        <div class="ai-msg-row ai-msg-aura" id="aiTyping" style="display:none">
                            <div class="ai-bubble-aura-wrap">
                                <div class="ai-typing-indicator">
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input bar -->
                    <div class="ai-chat-footer">
                        <div class="ai-input-bar">
                            <svg class="w-4 h-4 text-violet-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                            </svg>
                            <span class="ai-input-text" id="aiInputText">Ask Aura anything about your business…</span>
                            <div class="ai-input-send">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                        </div>
                        <!-- Quick chips -->
                        <div class="ai-chips">
                            <span class="ai-chip">📈 Revenue summary</span>
                            <span class="ai-chip">👥 HR overview</span>
                            <span class="ai-chip">📦 Low stock</span>
                            <span class="ai-chip">💡 Insights</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom stats row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 reveal" style="transition-delay:.25s">
            @foreach([
                ['< 2s',    'Average response time',  'violet'],
                ['100%',    'Powered by live ERP data','blue'],
                ['6+',      'ERP modules understood',  'emerald'],
                ['24 / 7',  'Always-on monitoring',    'amber'],
            ] as $stat)
            <div class="ai-bottom-stat ai-bottom-{{ $stat[2] }}">
                <div class="ai-bottom-num">{{ $stat[0] }}</div>
                <div class="ai-bottom-label">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════════════ PRICING ══════════════════════ -->
<section id="pricing" class="bg-gray-50 py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14 reveal">
            <div class="section-label mx-auto" style="display:inline-flex;">Pricing</div>
            <h2 class="section-title">Simple, transparent pricing</h2>
            <p class="section-sub max-w-lg mx-auto">No hidden fees. No setup costs. Cancel anytime.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 items-start">
            @foreach($plans as $i => $plan)
            <div class="pricing-card reveal {{ $i === 1 ? 'featured' : '' }}">
                @if($i === 1)
                <div class="text-xs font-semibold text-brand-300 uppercase tracking-widest mb-4">Most Popular</div>
                @endif
                <h3 class="text-xl font-bold mb-1 {{ $i === 1 ? 'text-white' : 'text-gray-900' }}">{{ $plan->name }}</h3>
                <p class="text-sm mb-6 {{ $i === 1 ? 'text-gray-400' : 'text-gray-500' }}">{{ $plan->description ?? 'Everything you need to grow' }}</p>
                <div class="flex items-end gap-1 mb-8 pb-8 border-b {{ $i === 1 ? 'border-gray-700' : 'border-gray-100' }}">
                    <span class="text-4xl font-bold {{ $i === 1 ? 'text-white' : 'text-gray-900' }}">${{ number_format($plan->price_monthly ?? $plan->price ?? 0) }}</span>
                    <span class="text-sm mb-2 {{ $i === 1 ? 'text-gray-400' : 'text-gray-400' }}">/month</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @forelse($plan->features ?? [] as $feat)
                    <li class="flex items-start gap-2 text-sm {{ $i === 1 ? 'text-gray-300' : 'text-gray-600' }}">
                        <span class="pricing-check mt-0.5">✓</span>{{ is_array($feat) ? ($feat['value'] ?? '') : $feat }}
                    </li>
                    @empty
                    @foreach(['Unlimited invoices','Email support','Basic analytics','Up to '.$plan->max_users.' users'] as $feat)
                    <li class="flex items-start gap-2 text-sm {{ $i === 1 ? 'text-gray-300' : 'text-gray-600' }}">
                        <span class="pricing-check mt-0.5">✓</span>{{ $feat }}
                    </li>
                    @endforeach
                    @endforelse
                </ul>
                <a href="/onboard/register?plan={{ $plan->id }}"
                   class="block text-center py-3 rounded font-semibold text-sm transition-all
                   {{ $i === 1
                       ? 'bg-white text-gray-900 hover:bg-gray-100'
                       : 'bg-gray-900 text-white hover:bg-gray-700' }}">
                    Get Started
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════════════ ABOUT ══════════════════════ -->
<section id="about" class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="reveal">
                <div class="section-label">About</div>
                <h2 class="section-title">Built for the way <span>modern businesses work</span></h2>
                <p class="text-gray-500 text-base leading-relaxed mb-6">
                    AureusERP was built from the ground up to replace a dozen disconnected tools with one unified platform. We believe software should work for you — not the other way around.
                </p>
                <p class="text-gray-500 text-base leading-relaxed mb-8">
                    Every module is designed to work seamlessly with every other. Your accounting speaks to your inventory. Your HR speaks to your payroll. That's how it should be.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([['Open Source Core','Transparent and community-driven'],['99.9% SLA','Enterprise uptime guarantee'],['GDPR Ready','Built-in data privacy controls'],['24/7 Support','Real humans, not bots']] as $v)
                    <div class="bg-gray-50 rounded p-4 border border-gray-100">
                        <div class="font-semibold text-sm text-gray-900 mb-1">{{ $v[0] }}</div>
                        <div class="text-xs text-gray-500">{{ $v[1] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="reveal">
                <div class="bg-gradient-to-br from-brand-50 to-white rounded border border-brand-100 p-8">
                    <div class="space-y-6">
                        @foreach([['2021','Founded with a vision to unify business operations'],['2022','First 500 customers onboarded across Africa & Asia'],['2023','Launched payroll, manufacturing and helpdesk modules'],['2024','Crossed 10,000 active companies on the platform'],['2025','Introduced AI analytics and multi-tenant SaaS platform']] as $e)
                        <div class="flex gap-5">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">{{ $e[0] }}</div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $e[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ CONTACT ══════════════════════ -->
<section id="contact" class="bg-gray-50 py-20">
    <div class="max-w-2xl mx-auto px-6 text-center reveal">
        <div class="section-label mx-auto" style="display:inline-flex;">Contact</div>
        <h2 class="section-title">Let's talk about your business</h2>
        <p class="section-sub mb-10">Have questions? Our team typically responds within 2 hours.</p>
        <div class="bg-white rounded border border-gray-100 p-8 shadow-sm text-left">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1.5">Full Name</label>
                    <input type="text" placeholder="John Doe" class="w-full border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 transition-colors">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1.5">Work Email</label>
                    <input type="email" placeholder="you@company.com" class="w-full border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 transition-colors">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-xs font-medium text-gray-600 block mb-1.5">Company</label>
                <input type="text" placeholder="Your company name" class="w-full border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 transition-colors">
            </div>
            <div class="mb-6">
                <label class="text-xs font-medium text-gray-600 block mb-1.5">Message</label>
                <textarea rows="4" placeholder="Tell us about your needs..." class="w-full border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 transition-colors resize-none"></textarea>
            </div>
            <button class="hero-btn-primary w-full justify-center">Send Message</button>
        </div>
    </div>
</section>

<!-- ══════════════════════ FOOTER ══════════════════════ -->
<footer class="py-16">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-10 mb-14">
            <!-- Brand -->
            <div class="col-span-1 sm:col-span-2 md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('images/aura.png') }}" alt="AureusERP" class="h-9 w-auto object-contain">
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed max-w-xs">The modern business operating system. Built open, designed to scale — from solo founders to enterprise teams.</p>
                <!-- Socials -->
                <div class="flex gap-3">
                    {{-- X / Twitter --}}
                    <a href="#" aria-label="X (Twitter)" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/8 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 flex items-center justify-center text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L2.25 2.25h6.977l4.257 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    {{-- LinkedIn --}}
                    <a href="#" aria-label="LinkedIn" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/8 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 flex items-center justify-center text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    {{-- GitHub --}}
                    <a href="#" aria-label="GitHub" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/8 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 flex items-center justify-center text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                    </a>
                    {{-- YouTube --}}
                    <a href="#" aria-label="YouTube" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/8 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 flex items-center justify-center text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    {{-- Facebook --}}
                    <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-white/8 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 flex items-center justify-center text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>
            <!-- Links -->
            @foreach(['Product' => ['Features','Pricing','Changelog','Roadmap','Documentation'],'Company' => ['About','Blog','Careers','Press','Partners'],'Support' => ['Help Center','API Docs','Status','Community','Contact']] as $cat => $links)
            <div>
                <h4 class="font-semibold text-xs text-gray-900 dark:text-gray-100 uppercase tracking-widest mb-4">{{ $cat }}</h4>
                <ul class="space-y-3">
                    @foreach($links as $link)
                    <li><a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        <!-- Newsletter strip -->
        <div class="bg-gray-50 dark:bg-white/4 border border-gray-100 dark:border-white/6 rounded-2xl p-6 mb-10 flex flex-col sm:flex-row items-center gap-4">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800 dark:text-gray-100">Stay in the loop</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Product updates, tips, and ERP insights — no spam.</p>
            </div>
            <form class="flex gap-2 w-full sm:w-auto" onsubmit="return false">
                <input type="email" placeholder="your@email.com" class="flex-1 sm:w-56 text-sm px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors flex-shrink-0">Subscribe</button>
            </form>
        </div>

        <div class="border-t border-gray-100 dark:border-white/6 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">© {{ date('Y') }} AureusERP. All rights reserved.</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Powered by <a href="https://kicowebdesign.com" target="_blank" class="text-brand-600 hover:text-brand-700 font-medium transition-colors">Kico Webdesign</a></p>
            <div class="flex flex-wrap justify-center gap-4 sm:gap-5">
                @foreach(['Privacy Policy','Terms of Service','Cookie Policy'] as $l)
                <a href="#" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">{{ $l }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<!-- ══════════════════════ STICKY CTA BANNER ══════════════════════ -->
<div class="cta-banner hidden-banner" id="ctaBanner">
    <p class="cta-banner-text text-sm font-medium">Ready to streamline your business?</p>
    <a href="/onboard/register" class="bg-white text-gray-900 text-sm font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition-colors flex-shrink-0">
        Start Free Trial →
    </a>
    <button onclick="document.getElementById('ctaBanner').classList.add('hidden-banner')" class="cta-banner-close text-gray-400 hover:text-white flex-shrink-0 text-xl leading-none">×</button>
</div>

<script>
    // ── Dynamic Island expand/collapse on scroll ──
    const island = document.getElementById('dynamicIsland');
    window.addEventListener('scroll', () => {
        island.classList.toggle('expanded', window.scrollY > 60);
    }, { passive: true });

    // ── Mobile hamburger toggle ──
    document.getElementById('mobileMenuBtn').addEventListener('click', () => {
        island.classList.toggle('mobile-open');
    });

    // Close mobile menu on link click
    document.querySelectorAll('.mobile-nav-link, .mobile-nav-cta a').forEach(el => {
        el.addEventListener('click', () => island.classList.remove('mobile-open'));
    });

    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('visible'), i * 80);
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(r => observer.observe(r));

    // Sticky CTA banner
    const banner = document.getElementById('ctaBanner');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 700) banner.classList.remove('hidden-banner');
    }, { passive: true });

    // ── Dark mode toggle ──
    function applyTheme(dark) {
        document.documentElement.classList.toggle('dark', dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    }
    document.querySelectorAll('.theme-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            applyTheme(!document.documentElement.classList.contains('dark'));
        });
    });

    // ══════════════════════════════════════════════
    // AI Chat Demo — animated conversation playback
    // ══════════════════════════════════════════════
    let aiAnimStarted = false;
    const aiSection   = document.getElementById('ai');

    function showAiMsg(id, delay) {
        return new Promise(resolve => {
            setTimeout(() => {
                const el = document.getElementById(id);
                if (el) {
                    el.style.display = 'flex';
                    el.classList.add('ai-msg-animate');
                    // Auto-scroll chat body
                    const body = document.querySelector('.ai-chat-body');
                    if (body) body.scrollTop = body.scrollHeight;
                }
                resolve();
            }, delay);
        });
    }

    async function runAiChatAnimation() {
        // Animate input text
        const inputEl = document.getElementById('aiInputText');
        const q1 = "What's our payroll status for this month?";
        const q2 = "Yes, and show me any urgent support tickets";

        // Type first question
        await new Promise(r => setTimeout(r, 400));
        inputEl.textContent = '';
        for (let i = 0; i < q1.length; i++) {
            await new Promise(r => setTimeout(r, 28));
            inputEl.textContent = q1.slice(0, i + 1);
        }

        await showAiMsg('aiMsg1', 300);
        inputEl.textContent = 'Ask Aura anything about your business…';

        // Show typing indicator then Aura reply
        await showAiMsg('aiTyping', 400);
        await new Promise(r => setTimeout(r, 1200));
        document.getElementById('aiTyping').style.display = 'none';
        await showAiMsg('aiMsg2', 0);

        // Wait then type second question
        await new Promise(r => setTimeout(r, 2000));
        inputEl.textContent = '';
        for (let i = 0; i < q2.length; i++) {
            await new Promise(r => setTimeout(r, 30));
            inputEl.textContent = q2.slice(0, i + 1);
        }

        await showAiMsg('aiMsg3', 300);
        inputEl.textContent = 'Ask Aura anything about your business…';

        // Typing then final reply
        document.getElementById('aiTyping').style.display = 'flex';
        document.getElementById('aiTyping').classList.add('ai-msg-animate');
        await new Promise(r => setTimeout(r, 1400));
        document.getElementById('aiTyping').style.display = 'none';
        await showAiMsg('aiMsg4', 0);
    }

    const aiObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting && !aiAnimStarted) {
                aiAnimStarted = true;
                setTimeout(runAiChatAnimation, 600);
                aiObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.3 });

    if (aiSection) aiObserver.observe(aiSection);

    // Also add #ai to navbar
    document.querySelectorAll('a[href="#ai"]').forEach(a => {
        a.addEventListener('click', e => {
            if (!aiAnimStarted) {
                aiAnimStarted = true;
                setTimeout(runAiChatAnimation, 800);
            }
        });
    });
</script>
</body>
</html>
