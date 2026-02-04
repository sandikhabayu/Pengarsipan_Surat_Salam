<!-- resources/views/petugas/profile/index.blade.php -->
@extends('layouts.petugas')

@section('title', 'Profil')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Profile Photo -->
        <div class="flex flex-col items-center gap-2 py-10">
            @if (auth()->user()->profile_photo_path)
                <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-40 h-40 rounded-full object-cover"
                    alt="Current Profile Photo">
            @else
                <div class="w-40 h-40 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-gray-500 text-4xl font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            <div>
                <span class="text-xl font-bold">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="col-span-1 grid grid-cols-1 gap-6 border-2 border-neutral-400 rounded-xl p-20 m-20">
                <h1 class="font-bold text-lg">INFORMASI PROFIL</h1>

                <!-- Name Field -->
                <div class="border-b pb-4">
                    <p class="text-sm font-medium text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="text-gray-800 font-medium">{{ auth()->user()->name }}</p>
                </div>

                <!-- Email Field -->
                <div class="border-b pb-4">
                    <p class="text-sm font-medium text-gray-500 mb-1">Alamat Email</p>
                    <p class="text-gray-800 font-medium">{{ auth()->user()->email }}</p>
                </div>

                <!-- Phone Number -->
                <div class="border-b pb-4">
                    <p class="text-sm font-medium text-gray-500 mb-1">Nomor Telepon</p>
                    <p class="text-gray-800 font-medium">
                        {{ auth()->user()->telepon ?? '-' }}
                    </p>
                </div>

                <!-- Address -->
                <div class="border-b pb-4">
                    <p class="text-sm font-medium text-gray-500 mb-1">Alamat</p>
                    <p class="text-gray-800 font-medium">
                        {{ auth()->user()->alamat ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('petugas.profile.edit') }}"
                class="px-4 py-2 bg-[#17AD90] text-white rounded-md hover:bg-[#136958] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:bg-[#17AD90] transition duration-150">
                Edit Profil
            </a>
        </div>
    </div>
@endsection
