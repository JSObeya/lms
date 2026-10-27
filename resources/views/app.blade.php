<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="/images/logo.png" />

        <!-- Theme Initialization Script (Prevents Theme Flash) -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-150">
        <!-- Initial Page Load Spinner / Preloader -->
        <div id="initial-page-preloader" style="position: fixed; inset: 0; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #020617; transition: opacity 0.3s ease, visibility 0.3s ease;">
            <style>
                @keyframes lms-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                @keyframes lms-spin-rev { 0% { transform: rotate(0deg); } 100% { transform: rotate(-360deg); } }
                @keyframes lms-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.96); } }
                .lms-loader-outer { width: 50px; height: 50px; border-radius: 50%; border: 3px solid rgba(225, 29, 72, 0.15); border-top-color: #e11d48; animation: lms-spin 0.9s cubic-bezier(0.5, 0, 0.5, 1) infinite; }
                .lms-loader-inner { position: absolute; width: 30px; height: 30px; border-radius: 50%; border: 3px solid rgba(251, 191, 36, 0.2); border-bottom-color: #f59e0b; animation: lms-spin-rev 0.7s linear infinite; }
                .lms-loader-text { margin-top: 16px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 13px; font-weight: 600; color: #f1f5f9; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; }
                .lms-loader-dot { width: 8px; height: 8px; border-radius: 50%; background-color: #e11d48; animation: lms-pulse 1.2s ease-in-out infinite; }
            </style>
            <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                <div class="lms-loader-outer"></div>
                <div class="lms-loader-inner"></div>
            </div>
            <div class="lms-loader-text">
                <span class="lms-loader-dot"></span>
                <span>Greg ICT Hub LMS</span>
            </div>
            <div style="margin-top: 6px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 11px; color: #94a3b8; letter-spacing: 0.02em;">Loading portal...</div>
        </div>

        @inertia
    </body>
</html>
