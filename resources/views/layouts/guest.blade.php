<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KAP EKO PRASETYO') }}</title>

        <!-- Logo -->
        <link class="w-6 h-6 text-blue-600 dark:text-blue-500" rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%232563eb' stroke-width='2' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'/></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Darkmode Init -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased relative min-h-screen bg-slate-50 dark:bg-slate-950 overflow-x-hidden transition-colors duration-200">
        <!-- Glow blobs -->
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-500/10 dark:bg-blue-600/5 rounded-full filter blur-[120px] pointer-events-none -z-10 animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-indigo-500/10 dark:bg-indigo-600/5 rounded-full filter blur-[100px] pointer-events-none -z-10"></div>

        <!-- Theme Toggle on Guest Page -->
        <div class="absolute top-6 right-6 z-20">
            <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800/80 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-slate-700 rounded-2xl text-sm p-3 transition-all border border-gray-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <div class="text-center animate-fade-in-up">
                <a href="/" class="group">
                    <h1 class="text-3xl font-black bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 dark:from-blue-400 dark:via-indigo-400 dark:to-blue-500 bg-clip-text text-transparent tracking-wider">
                        KAP EKO PRASETYO
                    </h1>
                    <p class="text-sm font-extrabold text-slate-500 dark:text-slate-400 tracking-[0.3em] uppercase mt-2 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors duration-300">
                        MANAJEMEN AUDIT
                    </p>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 p-8 bg-white/80 dark:bg-slate-900/60 border border-white/20 dark:border-slate-800/40 shadow-2xl backdrop-blur-md sm:rounded-3xl transition duration-300 animate-fade-in-up">
                {{ $slot }}
            </div>
        </div>

        <script>
            // dark mode toggle
            const themeToggle = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
            const html = document.documentElement;

            // Set initial icon state based on class 'dark'
            if (html.classList.contains('dark')) {
                themeToggleLightIcon?.classList.remove('hidden');
                themeToggleDarkIcon?.classList.add('hidden');
            } else {
                themeToggleDarkIcon?.classList.remove('hidden');
                themeToggleLightIcon?.classList.add('hidden');
            }

            themeToggle?.addEventListener('click', () => {
                // Toggle classes
                html.classList.toggle('dark');
                
                // Toggle icons
                themeToggleDarkIcon?.classList.toggle('hidden');
                themeToggleLightIcon?.classList.toggle('hidden');
                
                // Save preference to localStorage
                if (html.classList.contains('dark')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });
        </script>
    </body>
</html>
