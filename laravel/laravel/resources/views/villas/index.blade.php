@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- 🔍 FILTER -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">

        <input type="number" name="min_price" placeholder="Min Harga"
            value="{{ request('min_price') }}"
            class="border rounded-full px-4 py-2">

        <input type="number" name="max_price" placeholder="Max Harga"
            value="{{ request('max_price') }}"
            class="border rounded-full px-4 py-2">

        <input type="number" name="bedrooms" placeholder="Kamar Tidur"
            value="{{ request('bedrooms') }}"
            class="border rounded-full px-4 py-2">

        <input type="number" name="guests" placeholder="Jumlah Tamu"
            value="{{ request('guests') }}"
            class="border rounded-full px-4 py-2">

        <button class="col-span-2 md:col-span-4 bg-primary text-white py-2 rounded-full hover:bg-green-700">
            Filter
        </button>

    </form>


    <!-- 🏡 LIST VILLA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($villas as $villa)

            @php
                $images = is_array($villa->images) ? $villa->images : [];
            @endphp

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden group">

                <!-- 📸 SLIDER -->
                <div class="relative h-48 overflow-hidden">
                    @forelse ($images as $i => $img)
                        <img 
                            src="{{ asset('storage/' . $img) }}"
                            class="villa-slide-{{ $villa->id }} absolute w-full h-full object-cover transition-opacity duration-700 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
                    @empty
                        <img src="https://picsum.photos/400/300" class="w-full h-full object-cover">
                    @endforelse
                </div>

                <!-- 📄 CONTENT -->
                <div class="p-4">

                    <h3 class="text-lg font-semibold mb-2">
                        {{ $villa->name }}
                    </h3>

                    <!-- ICON INFO -->
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>👥 {{ $villa->max_guests }} tamu</span>
                        <span>🛏 {{ $villa->bedrooms }} kamar</span>
                    </div>

                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                        {{ $villa->description }}
                    </p>

                    <!-- 💰 PRICE -->
                    <div class="text-sm mb-3">
                        <p class="text-primary font-bold">
                            Weekday: Rp {{ number_format($villa->weekday_price, 0, ',', '.') }}
                        </p>
                        <p class="text-gray-600">
                            Weekend: Rp {{ number_format($villa->weekend_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <a href="{{ route('villas.show', $villa) }}" 
                        class="block text-center bg-primary text-white py-2 rounded-full hover:bg-green-700 transition">
                        Lihat Detail
                    </a>

                </div>

            </div>

        @empty
            <p class="col-span-3 text-center text-gray-500">
                Tidak ada villa ditemukan
            </p>
        @endforelse

    </div>


    <!-- 📄 PAGINATION -->
    <div class="mt-8">
        {{ $villas->links() }}
    </div>

</div>


<!-- ⚡ SLIDER SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('[class^="villa-slide-"]').forEach((_, i) => {

        let slides = document.querySelectorAll(`.villa-slide-${i+1}`);
        let index = 0;

        setInterval(() => {
            if (slides.length === 0) return;

            slides[index].classList.remove('opacity-100');
            slides[index].classList.add('opacity-0');

            index = (index + 1) % slides.length;

            slides[index].classList.remove('opacity-0');
            slides[index].classList.add('opacity-100');
        }, 4000);

    });

});
</script>

@endsection