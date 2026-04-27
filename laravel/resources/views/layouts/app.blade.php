<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kawan Puncak</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1F7A63',
                        gold: '#D4AF37',
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100 text-gray-800">    

<!-- 🧭 NAVBAR -->
<nav class="bg-primary text-white backdrop-blur-md shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- LOGO -->
        <a href="/" class="text-xl font-bold text-white">
            kawanpuncak<span class="text-yellow-500">.com</span>
        </a>

        <!-- MENU DESKTOP -->
        <div class="hidden md:flex gap-6 items-center">
            <a href="/" class="hover:text-primary transition">Home</a>
            <a href="/villas" class="hover:text-primary transition">Villa</a>
        </div>
<!-- 🌐 TOP SOCIAL BAR (CLEAN VERSION) -->
<div class="bg-primary text-white">
    <div class="max-w-7xl mx-auto flex justify-end items-center px-4 py-2 gap-4">

        <!-- Facebook -->
        <a href="https://www.facebook.com/kawanpuncak" target="_blank" class="hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                <path d="M22 12a10 10 0 10-11.6 9.9v-7H8v-2.9h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.2h-1.1c-1.1 0-1.5.7-1.5 1.4v1.7H16l-.4 2.9h-2.2v7A10 10 0 0022 12z"/>
            </svg>
        </a>

        <!-- Instagram -->
        <a href="https://www.instagram.com/kawa.npuncak" target="_blank" class="hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                <path d="M7 2C4.8 2 3 3.8 3 6v12c0 2.2 1.8 4 4 4h10c2.2 0 4-1.8 
                4-4V6c0-2.2-1.8-4-4-4H7zm5 5a5 5 0 110 10 5 5 0 010-10zm6.5-.9a1.1 
                1.1 0 11-2.2 0 1.1 1.1 0 012.2 0zM12 9a3 3 0 100 6 3 3 0 000-6z"/>
            </svg>
        </a>

        <!-- YouTube -->
        <a href="https://www.youtube.com/@kawanpuncak-y6t" target="_blank" class="hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                <path d="M21.8 8s-.2-1.5-.8-2.1c-.7-.8-1.5-.8-1.9-.9C16.4 4.8 
                12 4.8 12 4.8h0s-4.4 0-7.1.2c-.4.1-1.2.1-1.9.9C2.4 6.5 
                2.2 8 2.2 8S2 9.7 2 11.3v1.4C2 14.3 2.2 16 2.2 
                16s.2 1.5.8 2.1c.7.8 1.7.8 2.1.9 1.5.1 6.9.2 
                6.9.2s4.4 0 7.1-.2c.4-.1 1.2-.1 1.9-.9.6-.6.8-2.1.8-2.1s.2-1.7.2-3.3v-1.4C22 
                9.7 21.8 8 21.8 8zM10 14.7V9.3l5 2.7-5 2.7z"/>
            </svg>
        </a>

        <!-- TikTok -->
        <a href="https://www.tiktok.com/@kawanpuncak.1" target="_blank" class="hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                <path d="M16 2h2a4 4 0 004 4v2a6 6 0 01-4-1.5V14a6 6 0 11-6-6 
                h1v2h-1a4 4 0 104 4V2z"/>
            </svg>
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/62895365536497" target="_blank" class="hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                <path d="M20 3.9A10 10 0 004.2 18.5L3 21l2.6-1.2A10 10 0 1020 
                3.9zM12 20a8 8 0 01-4.1-1.1l-.3-.2-3.1 1.4 1.5-3-.2-.3A8 
                8 0 1112 20zm4.5-5.5c-.2-.1-1.2-.6-1.4-.7-.2-.1-.3-.1-.4.1-.1.2-.5.7-.6.8-.1.1-.2.1-.4 
                0-.2-.1-.8-.3-1.5-.9-.6-.5-1-1.1-1.1-1.3-.1-.2 0-.3.1-.4.1-.1.2-.2.3-.3.1-.1.1-.2.2-.3 
                0-.1 0-.2 0-.3 0-.1-.4-1-.6-1.4-.2-.4-.3-.3-.4-.3h-.3c-.1 
                0-.3.1-.4.2-.1.1-.5.5-.5 1.2s.5 1.4.6 1.5c.1.2 1.1 
                1.7 2.6 2.4.4.2.7.3.9.4.4.1.7.1 1 .1.3 0 1.2-.5 
                1.3-1 .2-.5.2-.9.1-1z"/>
            </svg>
        </a>

    </div>
</div>
        <!-- RIGHT -->
        <div class="flex items-center gap-4">

            @auth
                <!-- 🔔 NOTIF -->
                <div class="relative">
                    <button id="notifBtn" class="relative focus:outline-none">
                        <i data-lucide="bell" class="w-6 h-6 text-gray-700"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">3</span>
                    </button>
                    <!-- NOTIF DROPDOWN -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white text-gray-800 rounded-2xl shadow-lg overflow-hidden z-50">
                        <div class="flex items-center justify-between px-4 py-3 border-b">
                            <span class="font-semibold">Notifikasi</span>
                            <button id="notifClose" class="text-gray-400 hover:text-red-500 focus:outline-none">&times;</button>
                        </div>
                        <div class="p-4 text-sm">
                            <div class="mb-2 flex items-start gap-2">
                                <i data-lucide="info" class="w-4 h-4 mt-1 text-primary"></i>
                                <span>Booking anda sedang diproses.</span>
                            </div>
                            <div class="mb-2 flex items-start gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 mt-1 text-yellow-500"></i>
                                <span>Jangan lupa upload bukti pembayaran jika booking sudah di-approve.</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 mt-1 text-green-600"></i>
                                <span>Booking confirmed! Silakan download invoice.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 👤 AVATAR -->
                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center focus:outline-none">
                        <img 
                            src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=1F7A63&color=fff' }}" 
                            alt="{{ auth()->user()->name }}"
                            class="w-10 h-10 rounded-full border-2 border-white shadow object-cover">
                    </button>

                    <!-- DROPDOWN -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-52 bg-white text-gray-800 rounded-2xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b bg-primary text-white">
                            <p class="font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-sm">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 focus:bg-gray-200 transition text-gray-800">
                            <i data-lucide="calendar"></i> Dashboard User
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 focus:bg-gray-200 transition text-gray-800">
                            <i data-lucide="user"></i> Profile
                        </a>
                        <form action="/logout" method="POST">
                            @csrf
                            <button class="w-full flex items-center gap-2 px-4 py-2 hover:bg-red-100 focus:bg-red-200 text-gray-800 transition">
                                <i data-lucide="log-out"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>

            @else
                <a href="/login"
                   class="bg-primary text-white px-5 py-2 rounded-full shadow hover:bg-green-700 transition">
                    Login
                </a>
            @endauth

        </div>

    </div>
</nav>

<!-- 📱 MOBILE NAV -->
<div class="fixed bottom-0 left-0 right-0 bg-white shadow-md md:hidden z-50 rounded-t-2xl">

    <div class="flex justify-around py-3">

        <a href="/" class="flex flex-col items-center text-gray-600 hover:text-primary">
            <i data-lucide="home"></i>
            <span class="text-xs">Home</span>
        </a>

        <a href="/villas" class="flex flex-col items-center text-gray-600 hover:text-primary">
            <i data-lucide="building"></i>
            <span class="text-xs">Villa</span>
        </a>

        @auth
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-primary">
            <i data-lucide="calendar"></i>
            <span class="text-xs">Booking</span>
        </a>

        <a href="{{ route('profile.show') }}" class="flex flex-col items-center text-gray-600 hover:text-primary">
            <i data-lucide="user"></i>
            <span class="text-xs">Profile</span>
        </a>
        @else
        <a href="/login" class="flex flex-col items-center text-gray-600 hover:text-primary">
            <i data-lucide="log-in"></i>
            <span class="text-xs">Login</span>
        </a>
        @endauth

    </div>

</div>

    <!-- 📦 CONTENT -->
    <main class="p-6">
        @yield('content')
    </main>

    <!-- 🔻 FOOTER -->
    <footer class="bg-white mt-10 shadow-inner rounded-t-2xl p-6 text-center">
        <p class="text-gray-600">
            © {{ date('Y') }} <span class="text-primary font-semibold">Kawan Puncak</span>. All rights reserved.
        </p>

        <p class="text-sm text-gray-400 mt-2">
            Booking villa mudah, cepat, dan terpercaya
        </p>
    </footer>
<script>
    // Dropdown user
    const btn = document.getElementById('userMenuBtn');
    const dropdown = document.getElementById('userDropdown');
    if (btn) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
    // Dropdown notif
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifClose = document.getElementById('notifClose');
    if (notifBtn) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });
        notifClose?.addEventListener('click', () => {
            notifDropdown.classList.add('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }
    // lucide icons
    lucide.createIcons();
</script>
</body>
</html>