@extends('layouts.kepala-sekolah')

@section('title', 'Profil')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">

        <form method="POST" action="{{ route('kepala-sekolah.profile.update') }}" enctype="multipart/form-data"
            class="mx-20 px-20">
            @csrf
            @method('PUT')

            <!-- Profile Photo Section -->
            <div class="flex flex-col items-center gap-2 py-10">
                @if (auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                        class="w-40 h-40 rounded-full object-cover border-2 border-gray-200" alt="Foto Profil">
                @else
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center border-2 border-gray-200">
                        <span class="text-gray-500 text-3xl font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <span class="text-xl font-bold">{{ auth()->user()->name }}</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <div class="col-span-1 grid grid-cols-1 gap-6 border-2 border-neutral-400 rounded-xl p-10">
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

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('kepala-sekolah.profile.edit') }}"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white 
                           bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
                           transition duration-150 ease-in-out">
                    Edit
                </a>
            </div>
        </form>
    </div>
@endsection
