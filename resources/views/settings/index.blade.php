@extends('layouts.dash')

@section('title', __('messages.settings'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">

    {{-- ══════════════ PAGE HEADER ══════════════ --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">⚙️ {{ __('messages.settings') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('messages.settings_desc') }}</p>
    </div>

    {{-- ══════════════ FLASH MESSAGES ══════════════ --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ══════════════ CARD: PROFIL ══════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-user-circle text-indigo-500"></i> {{ __('messages.profile') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('messages.profile_desc') }}</p>
        </div>

        <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
            @csrf

            {{-- FOTO PROFIL --}}
            <div x-data="{
                    preview: '{{ $user->photo ? asset('storage/profile/' . $user->photo) : '' }}',
                    fallback: 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=128',
                    setPreview(e) {
                        const file = e.target.files[0];
                        if (file) this.preview = URL.createObjectURL(file);
                    }
                }" class="flex items-center gap-5">

                {{-- Avatar preview --}}
                <div class="relative shrink-0">
                    <img :src="preview || fallback"
                         class="w-20 h-20 rounded-full object-cover border-4 border-indigo-100 shadow"
                         alt="{{ __('messages.profile_photo') }}">
                    <label for="photo"
                           class="absolute -bottom-1 -right-1 w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center cursor-pointer shadow hover:bg-indigo-700 transition">
                        <i class="fas fa-camera text-white text-[10px]"></i>
                    </label>
                </div>

                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">{{ __('messages.profile_photo') }}</p>
                    <p class="text-xs text-gray-400 mb-2">JPG, PNG, WebP — maks. 2 MB</p>

                    <label for="photo"
                           class="inline-flex items-center gap-2 cursor-pointer text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition">
                        <i class="fas fa-upload"></i> {{ __('messages.choose_photo') }}
                    </label>

                    @if($user->photo)
                        <button type="button" onclick="document.getElementById('deletePhotoForm').submit()"
                           class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 ml-3 transition">
                            <i class="fas fa-trash-alt"></i> {{ __('messages.delete') }}
                        </button>
                    @endif

                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="hidden" @change="setPreview($event)">

                    @error('photo')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </form>
        {{-- Form DELETE terpisah agar tidak bentrok --}}
        <form id="deletePhotoForm" action="{{ route('settings.photo.delete') }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        <form action="{{ route('settings.profile') }}" method="POST" class="px-6 py-5 space-y-5 pt-0">
            @csrf
            {{-- NAME --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition
                              @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-save mr-1"></i> {{ __('messages.save_profile') }}
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════ CARD: GANTI PASSWORD ══════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-lock text-red-500"></i> {{ __('messages.change_password') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('messages.password_instruction') }}</p>
        </div>

        <form action="{{ route('settings.password') }}" method="POST" class="px-6 py-5 space-y-4"
              x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.current_password') }}</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.new_password') }}</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-key mr-1"></i> {{ __('messages.update_password') }}
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════ CARD: PREFERENSI ══════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-green-500"></i> {{ __('messages.preferences') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('messages.preferences_desc') }}</p>
        </div>

        <form action="{{ route('settings.preferences') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.interface_language') }}</label>
                <div class="relative">
                    <select name="language" onchange="this.form.submit()"
                            class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-2.5 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition bg-white">
                        <option value="id" {{ $user->language === 'id' ? 'selected' : '' }}>🇮🇩 Bahasa Indonesia</option>
                        <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection