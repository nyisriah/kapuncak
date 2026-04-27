@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4">

    @php
        $images = is_array($villa->images) ? $villa->images : [];
        $facilities = is_array($villa->facilities ?? null) ? $villa->facilities : [];
    @endphp

    <div class="relative h-[500px] rounded-3xl overflow-hidden mb-10 shadow-xl">

        @forelse ($images as $i => $img)
            <img src="{{ asset('storage/' . $img) }}"
                class="hero-slide absolute w-full h-full object-cover transition-opacity duration-700 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
        @empty
            <img src="https://picsum.photos/1200/600" class="w-full h-full object-cover">
        @endforelse

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

        <button id="prevHero" class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-3xl">❮</button>
        <button id="nextHero" class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-3xl">❯</button>

        <div class="absolute bottom-8 left-8 text-white">
            <h1 class="text-4xl font-bold mb-2">{{ $villa->name }}</h1>
            <p class="text-lg opacity-90">{{ $villa->location }}</p>

            <div class="flex gap-4 mt-3 text-sm">
                <span>👥 {{ $villa->max_guests }} tamu</span>
                <span>🛏 {{ $villa->bedrooms }} kamar</span>
            </div>
        </div>

    </div>

    <div class="flex gap-3 overflow-x-auto mb-10 pb-2">
        @foreach ($images as $i => $img)
            <img src="{{ asset('storage/' . $img) }}"
                class="thumb h-24 w-40 flex-shrink-0 object-cover rounded-xl cursor-pointer hover:scale-105 transition"
                data-index="{{ $i }}">
        @endforeach
    </div>

    <div id="lightbox" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50">
        <button id="closeLightbox" class="absolute top-6 right-6 text-white text-3xl">✕</button>
        <button id="prevLight" class="absolute left-6 text-white text-3xl">❮</button>
        <img id="lightboxImg" class="max-h-[90%] max-w-[90%] rounded-xl">
        <button id="nextLight" class="absolute right-6 text-white text-3xl">❯</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur p-6 rounded-3xl shadow-lg mb-6 border">
                <h2 class="text-2xl font-semibold mb-4">Tentang Villa</h2>
                <p class="text-gray-600 leading-relaxed">
                    {{ $villa->description }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg mb-6 border">
                <h2 class="text-2xl font-semibold mb-4">Fasilitas</h2>
                @if(count($facilities))
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-gray-600">
                        @foreach ($facilities as $f)
                            <div class="flex items-center gap-2">
                                <span class="text-green-600">✔</span>
                                <span>{{ ucfirst($f) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">Belum ada fasilitas</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-gradient-to-br from-green-500 to-green-700 text-white p-6 rounded-3xl shadow-lg mb-6">
                <h2 class="text-xl font-semibold mb-2">Harga</h2>
                <p class="text-3xl font-bold">
                    Rp {{ number_format($villa->weekday_price, 0, ',', '.') }}
                    <span class="text-sm font-normal">/ malam</span>
                </p>
                <p class="text-sm opacity-90 mt-2">
                    Weekend: Rp {{ number_format($villa->weekend_price, 0, ',', '.') }}
                </p>
                <p class="text-red-200 text-xs mt-2 font-medium">
                    * slot bulan ini tinggal sedikit
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-xl sticky top-6 border">
                <h2 class="text-xl font-semibold mb-4">Booking Sekarang</h2>

                @auth
                <form action="{{ route('bookings.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="villa_id" value="{{ $villa->id }}">

                    <input type="text" value="{{ auth()->user()->name }}"
                        class="w-full border rounded-full px-4 py-2 bg-gray-100" readonly>

                    <input type="email" value="{{ auth()->user()->email }}"
                        class="w-full border rounded-full px-4 py-2 bg-gray-100" readonly>   

                    <input type="text" name="phone"
                        value="{{ auth()->user()->phone ?? '' }}"
                        placeholder="Nomor HP"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full border rounded-full px-4 py-2" required>

                    <input type="number" name="guest"
                        min="1" max="{{ $villa->max_guests }}"
                        placeholder="Jumlah tamu"
                        class="w-full border rounded-full px-4 py-2" required>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs ml-3 text-gray-500">Check In</label>
                            <input type="date" id="check_in" name="check_in"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full border rounded-full px-4 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="text-xs ml-3 text-gray-500">Check Out</label>
                            <input type="date" id="check_out" name="check_out"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full border rounded-full px-4 py-2 text-sm" required>
                        </div>
                    </div>

                    <div id="pricePreview" class="hidden bg-green-50 p-3 rounded-xl text-center border border-green-100">
                        <p class="text-xs text-gray-500">Total Estimasi Harga</p>
                        <p id="totalPrice" class="font-bold text-green-700 text-lg"></p>
                    </div>

                    <div class="flex gap-2 text-sm items-start mt-2">
                        <input type="checkbox" id="terms" class="mt-1">
                        <label for="terms" class="text-gray-600 cursor-pointer">Saya setuju dengan syarat dan ketentuan yang berlaku.</label>
                    </div>

                    <button id="bookingBtn"
                        type="submit" disabled
                        class="w-full bg-gray-300 text-white py-3 rounded-full font-semibold transition-all duration-300 cursor-not-allowed">
                        Booking Sekarang
                    </button>
                </form>
                @else
                    <a href="/login"
                        class="block bg-green-600 text-white py-3 rounded-full text-center font-semibold hover:bg-green-700 transition">
                        Login untuk booking
                    </a>
                @endauth

                @if ($errors->any())
                    <div class="mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100">
                        <ul class="list-disc ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // 1. HERO SLIDER LOGIC
    let slides = document.querySelectorAll('.hero-slide');
    let index = 0;

    function showSlide(i) {
        slides.forEach(s => {
            s.classList.remove('opacity-100');
            s.classList.add('opacity-0');
        });
        slides[i].classList.remove('opacity-0');
        slides[i].classList.add('opacity-100');
    }

    document.getElementById('nextHero').onclick = () => {
        index = (index + 1) % slides.length;
        showSlide(index);
    };

    document.getElementById('prevHero').onclick = () => {
        index = (index - 1 + slides.length) % slides.length;
        showSlide(index);
    };

    setInterval(() => {
        if (slides.length > 1) {
            index = (index + 1) % slides.length;
            showSlide(index);
        }
    }, 5000);

    // 2. LIGHTBOX LOGIC
    let thumbs = document.querySelectorAll('.thumb');
    let lightbox = document.getElementById('lightbox');
    let lightImg = document.getElementById('lightboxImg');
    let current = 0;

    thumbs.forEach((t, i) => {
        t.onclick = () => {
            current = i;
            lightImg.src = t.src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }
    });

    document.getElementById('closeLightbox').onclick = () => {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
    };

    document.getElementById('nextLight').onclick = () => {
        current = (current + 1) % thumbs.length;
        lightImg.src = thumbs[current].src;
    };

    document.getElementById('prevLight').onclick = () => {
        current = (current - 1 + thumbs.length) % thumbs.length;
        lightImg.src = thumbs[current].src;
    };

    // 3. PRICE CALCULATION LOGIC
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const priceBox = document.getElementById('pricePreview');
    const totalText = document.getElementById('totalPrice');

    const weekdayPrice = {{ $villa->weekday_price }};
    const weekendPrice = {{ $villa->weekend_price }};

    function calculatePrice() {
        if (!checkIn.value || !checkOut.value) return;

        let start = new Date(checkIn.value);
        let end = new Date(checkOut.value);
        
        if (end <= start) {
            priceBox.classList.add('hidden');
            return;
        }

        let total = 0;
        let tempDate = new Date(start);

        while (tempDate < end) {
            let day = tempDate.getDay();
            // 0 = Minggu, 6 = Sabtu
            total += (day === 0 || day === 6) ? weekendPrice : weekdayPrice;
            tempDate.setDate(tempDate.getDate() + 1);
        }

        if (total > 0) {
            totalText.innerText = 'Rp ' + total.toLocaleString('id-ID');
            priceBox.classList.remove('hidden');
        }
    }

    checkIn?.addEventListener('change', () => {
        if (checkIn.value) {
            checkOut.min = checkIn.value;
        }
        calculatePrice();
    });

    checkOut?.addEventListener('change', calculatePrice);

    // 4. TERMS & BOOKING BUTTON LOGIC
    const bookingBtn = document.getElementById('bookingBtn');
    const termsCheckbox = document.getElementById('terms');

    termsCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            bookingBtn.disabled = false;
            bookingBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            bookingBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'shadow-lg');
        } else {
            bookingBtn.disabled = true;
            bookingBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
            bookingBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'shadow-lg');
        }
    });

});
</script>

@endsection