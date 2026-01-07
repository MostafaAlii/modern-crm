<!DOCTYPE html>
@if (app()->getLocale() == 'ar')
    <html direction="rtl" dir="rtl" style="direction: rtl">
@else
    <html lang="en">
@endif
<head>
    <title>{{-- $settings?->company_name --}} | @yield('title')</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{-- $settings?->company_name --}}" />
    <meta name="keywords" content="{{-- $settings?->company_name --}}" />
    <meta name="author" content="{{-- $settings?->company_name --}}" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{-- $favicon --}}" type="image/x-icon" />

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('dashboard/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.4/css/buttons.bootstrap5.min.css">
    <!-- vendor css -->
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('dashboard/assets/css/style-rtl.css') }}" id="rtl-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('dashboard/assets/css/style.css') }}" id="main-style-link">
    @endif
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('dashboard/assets/fonts/Cairo/static/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: 400;
        }

        html,
        body,
        a,
        i,
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        .btn,
        .alert,
        .dt-button {
            font-family: 'Cairo', sans-serif;
        }
        .section-title {
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.25rem;
        }
        /* لو الصفحة RTL (العربية) */
        html[dir="rtl"] .navbar-content {
        direction: rtl; /* تخلي النصوص والقوائم لليمين */
        scrollbar-gutter: stable both-edges; /* يمنع overlap مع المحتوى */
        }

        html[dir="rtl"] .navbar-content::-webkit-scrollbar {
        width: 6px;
        }

        html[dir="rtl"] .navbar-content::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
        }

        html[dir="rtl"] .navbar-content::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0;
        }

        /* أهم حاجة: إجبار الـ scrollbar يكون على الشمال في RTL */
        html[dir="rtl"] .navbar-content {
        scrollbar-color: #c1c1c1 transparent;
        scrollbar-width: thin;
        direction: rtl;
        }
        /* خلي الـ sidebar كـ flex column */
        .app-sidebar {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* نخلي التحكم في الـ scroll جوّه navbar-content */
        }

        /* الحاوية اللي فيها اللوجو والقوائم */
        .app-navbar-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 0; /* مهم جداً للسماح للأطفال بالتمدد والـ overflow بالعمل */
        }

        /* اللوجو/الهيدر فوق يفضل ثابت */
        .brand-link {
        flex: 0 0 auto;
        }

        /* الجزء اللي فيه القوائم — ده اللي هيعمل scroll */
        .navbar-content {
        flex: 1 1 auto; /* ياخد المساحة الباقية */
        min-height: 0; /* <--- أهم سطر */ overflow-y: auto; /* يظهر scrollbar بس عند الحاجة */ overflow-x: hidden;
            -webkit-overflow-scrolling: touch; /* smoother on mobile */ } /* شكل scrollbar (اختياري) */
            .navbar-content::-webkit-scrollbar { width: 6px; } .navbar-content::-webkit-scrollbar-thumb { background: #c1c1c1;
            border-radius: 10px; } .navbar-content::-webkit-scrollbar-thumb:hover { background: #a0a0a0; }
    </style>
    <script>
            document.addEventListener('DOMContentLoaded', function() {
                const brand = document.querySelector('.brand-link');
                const nav = document.querySelector('.navbar-content');
                if (brand && nav) {
                nav.style.height = `calc(100vh - ${brand.getBoundingClientRect().height}px)`;
                nav.style.overflowY = 'auto';
                }
                });
        </script>
    @stack('css')
</head>

<body class="theme-2">
    <!-- { Pre-loader } start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- { Pre-loader } End -->
