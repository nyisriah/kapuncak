
@extends('layouts.app')

@section('content')

<!-- 🔥 HERO SECTION -->
<div class="relative w-full h-[70vh] overflow-hidden rounded-2xl shadow-lg">

    <!-- Slides -->
    <div id="slider" class="w-full h-full relative">
        <img src="https://picsum.photos/1600/900?random=11" class="slide absolute w-full h-full object-cover transition-opacity duration-1000 opacity-100">
        <img src="https://picsum.photos/1600/900?random=12" class="slide absolute w-full h-full object-cover transition-opacity duration-1000 opacity-0">
        <img src="https://picsum.photos/1600/900?random=13" class="slide absolute w-full h-full object-cover transition-opacity duration-1000 opacity-0">
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/60 flex flex-col justify-center items-center text-center px-4">

        <!-- Typing Text -->
        <h1 class="text-white text-3xl md:text-5xl font-bold mb-4 h-[60px]">
            <span id="typing"></span>
        </h1>

        <p class="text-gray-200 text-sm md:text-lg mb-6 max-w-xl">
        
        </p>

        <a href="/villas" class="bg-primary text-white px-6 py-3 rounded-full shadow hover:shadow-lg hover:bg-green-700 transition">
            Galery Villa
        </a>
    </div>
</div>

<!-- 🏡 VILLA LIST -->
<div class="mt-10">
    <h2 class="text-2xl font-serif text-primary mb-6">
        Villa Terpopuler di Puncak

    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($villas as $villa)

           @php
    $img = json_decode($villa->images, true)[0] ?? null;
@endphp
            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden group">

                <!-- Image -->
                <div class="overflow-hidden">
                    <img 
                        src="{{ $image ? asset('storage/' . $image) : 'https://source.unsplash.com/400x300/?villa' }}"
                        onerror="this.src='https://source.unsplash.com/400x300/?villa'"
                        class="w-full h-48 object-cover group-hover:scale-110 transition duration-500"
                    >
                </div>

                <!-- Content -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        {{ $villa->name }}
                    </h3>

                    <p class="text-sm text-gray-500 mb-2">
                        {{ $villa->location ?? 'Puncak, Bogor' }}
                    </p>

                    <p class="text-primary font-bold mb-4">
                        Rp {{ number_format($villa->weekday_price, 0, ',', '.') }} / malam
                    </p>

                    <a href="/villas/{{ $villa->slug }}" 
                       class="block text-center bg-primary text-white py-2 rounded-full hover:bg-green-700 shadow hover:shadow-lg transition">
                        Lihat Detail
                    </a>
                </div>
            </div>

        @empty
            <p class="text-gray-500 col-span-3 text-center">
                Belum ada villa tersedia
            </p>
        @endforelse

    </div>
</div>
<!-- 🎯 LAYANAN -->
<div class="mt-16">
    <h2 class="text-2xl font-serif text-primary mb-6">
        Layanan Unggulan Kami
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        @php
            $services = [
                [
                    'title' => 'Paintball',
                    'desc' => 'Rasakan sensasi perang strategi seru bersama tim di alam terbuka.',
                    'images' => ['paintball', 'paintball game', 'paintball outdoor'],
                    'link' => '#'
                ],
                [
                    'title' => 'Rafting',
                    'desc' => 'Petualangan arung jeram yang memacu adrenalin di sungai alami.',
                    'images' => ['rafting river', 'white water rafting', 'rafting adventure'],
                    'link' => '#'
                ],
                [
                    'title' => 'Outbond',
                    'desc' => 'Aktivitas team building seru dan menantang di alam terbuka.',
                    'images' => ['outbound team', 'outbound games', 'outdoor activity'],
                    'link' => '#'
                ],
                [
                    'title' => 'Catering',
                    'desc' => 'Layanan makanan lengkap untuk acara keluarga atau gathering.',
                    'images' => ['catering buffet', 'indonesian food buffet', 'event catering'],
                    'link' => '#'
                ],
            ];
        @endphp

        @foreach ($services as $index => $service)
            <a href="{{ $service['link'] }}" class="block group">
                <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden">

                    <!-- Slider -->
                    <div class="relative h-40 overflow-hidden">
                        @foreach ($service['images'] as $i => $img)
                            <img 
                                src="https://picsum.photos/400/300?random={{ $index.$i }}"
                                onerror="this.src='https://picsum.photos/400/300'"
                                class="service-slide-{{ $index }} absolute w-full h-full object-cover transition-opacity duration-700 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
                        @endforeach
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $service['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $service['desc'] }}
                        </p>
                    </div>

                </div>
            </a>
        @endforeach

    </div>
</div>

<!-- 🌄 WISATA MENARIK -->
<div class="mt-16">
    <h2 class="text-2xl font-serif text-primary mb-6">
        Rekomendasi Wisata Menarik di Puncak
    </h2>

    @php
        $destinations = [
            [
                'title' => 'Curug Cilember',
                'desc' => 'Air terjun alami dengan suasana sejuk dan pemandangan hijau.',
                'images' => ['waterfall forest', 'curug waterfall', 'indonesia waterfall'],
                'link' => '#'
            ],
            [
                'title' => 'Taman Safari',
                'desc' => 'Wisata keluarga melihat satwa dari berbagai belahan dunia.',
                'images' => ['safari park', 'zoo indonesia', 'wildlife safari'],
                'link' => '#'
            ],
            [
                'title' => 'Wisata Gunung Mas',
                'desc' => 'terdapat kebun teh dan pemandangan alam yang indah,berbagai macam .',
                'images' => ['tea plantation', 'green tea field', 'mountain tea'],
                'link' => '#'
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($destinations as $index => $place)
            <a href="{{ $place['link'] }}" class="block group">
                <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden">

                    <!-- Slider -->
                    <div class="relative h-48 overflow-hidden">
                        @foreach ($place['images'] as $i => $img)
                            <img 
                                src="https://picsum.photos/500/400?random={{ $index.$i }}"
                                onerror="this.src='https://picsum.photos/500/400'"
                                class="dest-slide-{{ $index }} absolute w-full h-full object-cover transition-opacity duration-700 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
                        @endforeach
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $place['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $place['desc'] }}
                        </p>
                    </div>

                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- ⚡ SCRIPT -->
 <script>
    // 🔄 SLIDER HERO
    let slides = document.querySelectorAll('.slide');
    let current = 0;

    setInterval(() => {
        slides[current].classList.remove('opacity-100');
        slides[current].classList.add('opacity-0');

        current = (current + 1) % slides.length;

        slides[current].classList.remove('opacity-0');
        slides[current].classList.add('opacity-100');
    }, 7000);


    // ⌨️ MULTI TYPING EFFECT
    const texts = [
        "Selamat Datang Di KawanPuncak.Com",
        "Layanan Profesional 24 Jam",
        "Proses Booking Praktis & Transparan",
        "Fasilitas Lengkap & Nyaman",
        "Harga Kompetitif & Terjangkau",
    ];

    let textIndex = 0;
    let charIndex = 0;
    let typingElement = document.getElementById("typing");

    function typeEffect() {
        if (charIndex < texts[textIndex].length) {
            typingElement.innerHTML += texts[textIndex].charAt(charIndex);
            charIndex++;
            setTimeout(typeEffect, 50);
        } else {
            setTimeout(eraseEffect, 2000);
        }
    }

    function eraseEffect() {
        if (charIndex > 0) {
            typingElement.innerHTML = texts[textIndex].substring(0, charIndex - 1);
            charIndex--;
            setTimeout(eraseEffect, 30);
        } else {
            textIndex = (textIndex + 1) % texts.length;
            setTimeout(typeEffect, 500);
        }
    }

    document.addEventListener("DOMContentLoaded", typeEffect);
</script>
 <script>
    // SERVICE SLIDER
    @foreach ($services as $index => $service)
        let serviceSlides{{ $index }} = document.querySelectorAll('.service-slide-{{ $index }}');
        let serviceIndex{{ $index }} = 0;

        setInterval(() => {
            serviceSlides{{ $index }}[serviceIndex{{ $index }}].classList.remove('opacity-100');
            serviceSlides{{ $index }}[serviceIndex{{ $index }}].classList.add('opacity-0');

            serviceIndex{{ $index }} = (serviceIndex{{ $index }} + 1) % serviceSlides{{ $index }}.length;

            serviceSlides{{ $index }}[serviceIndex{{ $index }}].classList.remove('opacity-0');
            serviceSlides{{ $index }}[serviceIndex{{ $index }}].classList.add('opacity-100');
        }, 4000);
    @endforeach

    // DESTINATION SLIDER
    @foreach ($destinations as $index => $place)
        let destSlides{{ $index }} = document.querySelectorAll('.dest-slide-{{ $index }}');
        let destIndex{{ $index }} = 0;

        setInterval(() => {
            destSlides{{ $index }}[destIndex{{ $index }}].classList.remove('opacity-100');
            destSlides{{ $index }}[destIndex{{ $index }}].classList.add('opacity-0');

            destIndex{{ $index }} = (destIndex{{ $index }} + 1) % destSlides{{ $index }}.length;

            destSlides{{ $index }}[destIndex{{ $index }}].classList.remove('opacity-0');
            destSlides{{ $index }}[destIndex{{ $index }}].classList.add('opacity-100');
        }, 5000);
    @endforeach
</script>

<script>
    let slides = document.querySelectorAll('.slide');
    let current = 0;

    setInterval(() => {
        slides[current].classList.remove('opacity-100');
        slides[current].classList.add('opacity-0');

        current = (current + 1) % slides.length;

        slides[current].classList.remove('opacity-0');
        slides[current].classList.add('opacity-100');
    }, 7000);

    const text = "Temukan Villa Impian Anda di Puncak";
    let index = 0;

    function typeEffect() {
        if (index < text.length) {
            document.getElementById("typing").innerHTML += text.charAt(index);
            index++;
            setTimeout(typeEffect, 50);
        }
    }

    document.addEventListener("DOMContentLoaded", typeEffect);
</script>

@endsection