@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 md:px-0">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('profile.show') }}" class="text-primary hover:underline flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Kembali ke Profil
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-primary text-white">
            <h1 class="text-2xl font-bold">Edit Profil</h1>
            <p class="text-green-100">Perbarui informasi akun Anda</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Avatar Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Foto Profil</label>
                <div class="flex items-center gap-6">
                    <!-- Current Avatar -->
                    <div>
                        <img 
                            id="avatarPreview"
                            src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=1F7A63&color=fff' }}" 
                            alt="{{ auth()->user()->name }}"
                            class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                    </div>

                    <!-- Upload Section -->
                    <div class="flex-1">
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-green-700 transition cursor-pointer font-semibold">
                            <i data-lucide="upload" class="w-5 h-5"></i>
                            Pilih Foto
                            <input 
                                type="file" 
                                name="avatar" 
                                accept="image/*"
                                class="hidden"
                                onchange="previewAvatar(this)">
                        </label>
                        <p class="text-sm text-gray-600 mt-2">Format: JPG, PNG, GIF | Max: 2MB</p>
                        @error('avatar')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', auth()->user()->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', auth()->user()->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="{{ old('phone', auth()->user()->phone) }}"
                        placeholder="contoh: 08123456789"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Number -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                    <input 
                        type="tel" 
                        name="whatsapp_number" 
                        value="{{ old('whatsapp_number', auth()->user()->whatsapp_number) }}"
                        placeholder="contoh: +628123456789"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('whatsapp_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address (Full Width) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                <textarea 
                    name="address" 
                    rows="3"
                    placeholder="Masukkan alamat lengkap Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none">{{ old('address', auth()->user()->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-800">
                            <span class="font-semibold">Catatan:</span> Informasi WhatsApp kami gunakan untuk komunikasi penting terkait booking Anda.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t">
                <a 
                    href="{{ route('profile.show') }}"
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold text-center">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview avatar before upload
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    lucide.createIcons();
</script>
@endsection
