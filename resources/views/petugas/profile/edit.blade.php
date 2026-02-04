<!-- resources/views/petugas/profile/edit.blade.php -->
@extends('layouts.petugas')

@section('title', 'Profil')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">

        <form method="POST" action="{{ route('petugas.profile.update') }}" enctype="multipart/form-data" class="mx-20 px-20">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <!-- Profile Photo -->
                <div class="col-span-1 flex flex-col items-center">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    <div class="flex flex-col items-center">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}"
                                class="w-48 h-48 rounded-full object-cover" alt="Current Profile Photo">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-500 text-2xl font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <input type="file" name="profile_photo" id="profile_photo"
                                class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-green-50 file:text-green-700
                                      hover:file:bg-green-100">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Empty column for alignment -->
                <div class="col-span-1"></div>

                <!--Kolom identitas-->
                <div class="col-span-1 grid grid-cols-1 gap-6 border-2 border-neutral-600 rounded-xl p-10">
                    <!-- Name Field -->
                    <div class="flex flex-col gap-2">
                        <h1 class="font-bold">INFORMASI PROFIL</h1>
                        <p>Perbarui informasi lengkapmu</p>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                  focus:border-[#17AD90] focus:ring focus:ring-green-200 focus:ring-opacity-50
                                  transition duration-150 ease-in-out"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                  focus:border-[#17AD90] focus:ring focus:ring-green-200 focus:ring-opacity-50
                                  transition duration-150 ease-in-out"
                            required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Nomor Telepon -->
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" name="telepon" id="telepon"
                            value="{{ old('telepon', auth()->user()->telepon) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
              focus:border-[#17AD90] focus:ring focus:ring-green-200 focus:ring-opacity-50
              transition duration-150 ease-in-out"
                            required>
                        @error('telepon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <input type="text" name="alamat" id="alamat"
                            value="{{ old('alamat', auth()->user()->alamat) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                  focus:border-[#17AD90] focus:ring focus:ring-green-200 focus:ring-opacity-50
                                  transition duration-150 ease-in-out"
                            required>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!--Kolom Password-->
                <div class="col-span-1 grid grid-cols-1 gap-6 border-2 border-neutral-600 rounded-xl p-10">
                    <div class="flex flex-col gap-2">
                        <h1 class="font-bold">PERBARUI KATA SANDI</h1>
                        <p>Perbarui kata sandimu</p>
                    </div>
                    <!-- Password Field -->
                    <div class="col-span-1">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                  focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50
                                  transition duration-150 ease-in-out"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="col-span-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                  focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50
                                  transition duration-150 ease-in-out"
                            placeholder="Ketik ulang password baru">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('petugas.profile.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 
                      bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-[#17AD90] text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
