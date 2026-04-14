<!DOCTYPE html>
<html lang="sk" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sellwinar')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        violet: {
                            50:  '#EDE9FE',
                            100: '#DDD6FE',
                            200: '#C4B5FD',
                            300: '#A78BFA',
                            400: '#8B5CF6',
                            500: '#7C4DFF',
                            600: '#6C3AED',
                            700: '#5B21B6',
                            800: '#4C1D95',
                            900: '#3B0764',
                        },
                        ink: {
                            50:  '#F8F7FF',
                            100: '#F0EFF5',
                            200: '#E2E1EA',
                            300: '#C8C7D4',
                            400: '#9E9DAE',
                            500: '#6E6D80',
                            600: '#4A4958',
                            700: '#2D2C3E',
                            800: '#1A1A2E',
                            900: '#0F0F12',
                        },
                        success: {
                            50: '#ECFDF5', 100: '#D1FAE5', 400: '#34D399',
                            500: '#10B981', 600: '#059669', 700: '#047857',
                        },
                        warning: {
                            50: '#FFFBEB', 100: '#FEF3C7', 400: '#FBBF24',
                            500: '#F59E0B', 600: '#D97706', 700: '#B45309',
                        },
                        danger: {
                            50: '#FEF2F2', 100: '#FEE2E2', 400: '#F87171',
                            500: '#EF4444', 600: '#DC2626', 700: '#B91C1C',
                        },
                        info: {
                            50: '#EFF6FF', 100: '#DBEAFE', 400: '#60A5FA',
                            500: '#3B82F6', 600: '#2563EB', 700: '#1D4ED8',
                        },
                    },
                    fontFamily: {
                        heading: ['"Space Grotesk"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', '"Fira Code"', 'monospace'],
                    },
                    borderRadius: {
                        'sm':   '4px',
                        'md':   '6px',
                        'lg':   '10px',
                        'xl':   '12px',
                        '2xl':  '16px',
                        'full': '9999px',
                    },
                    boxShadow: {
                        'sm':  '0 1px 2px rgba(0, 0, 0, 0.04)',
                        'md':  '0 2px 8px rgba(0, 0, 0, 0.06)',
                        'lg':  '0 4px 16px rgba(0, 0, 0, 0.08)',
                        'xl':  '0 8px 32px rgba(0, 0, 0, 0.10)',
                        'violet': '0 4px 20px rgba(108, 58, 237, 0.25)',
                    },
                },
            },
        }
    </script>

    <!-- Custom base styles -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        /* Focus ring */
        .ring-violet-focus:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 58, 237, 0.3);
        }
        /* Smooth transitions */
        .transition-fast { transition: all 150ms ease; }
        .transition-base { transition: all 200ms ease; }
        .transition-slow { transition: all 300ms ease; }
        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    @stack('styles')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-body bg-[#FAFAFA] text-ink-800 dark:bg-ink-900 dark:text-[#F0EFF5] antialiased">

    @yield('body')

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Chart.js (loaded only where needed) -->
    @stack('scripts')
</body>
</html>
