@extends('layouts.petugas')

@section('title', 'Detail Surat Masuk')
@section('submenu-active', true)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow rounded-lg p-6">

            <div class="grid grid-cols-1 gap-4 text-lg text-gray-800">
                <div class="md:col-span-2 flex gap-14 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Kode Surat:</p>
                    <p>{{ $suratMasuk->kode_surat }}</p>
                </div>
                <div class="md:col-span-2 flex gap-10 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Nomor Surat:</p>
                    <p>{{ $suratMasuk->nomor_surat }}</p>
                </div>
                <div class="md:col-span-2 flex gap-16 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Lampiran:</p>
                    <p>{{ $suratMasuk->lampiran }}</p>
                </div>
                <div class="md:col-span-2 flex gap-6 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Tanggal Masuk:</p>
                    <p>{{ \Carbon\Carbon::parse($suratMasuk->tanggal_masuk)->format('d-m-Y') }}</p>
                </div>
                <div class="md:col-span-2 flex gap-20 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Pengirim:</p>
                    <p>{{ $suratMasuk->pengirim }}</p>
                </div>
                <div class="md:col-span-2 flex gap-24 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Perihal:</p>
                    <p>{{ $suratMasuk->perihal }}</p>
                </div>
                <div class="md:col-span-2 flex gap-14 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Keterangan:</p>
                    <p>{{ $suratMasuk->keterangan ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-2">File Surat:</h2>
                @php
                    $filePath = $suratMasuk->file_path;
                    $fileName = basename($filePath);

                    // Path untuk Windows
                    $storagePath = str_replace('/', '\\', storage_path('app\public\\' . $filePath));
                    $publicPath = str_replace('/', '\\', public_path('storage\\' . $filePath));

                    $fileExists = file_exists($storagePath) || file_exists($publicPath);

                    // Coba kedua lokasi untuk URL
                    if (file_exists(public_path('storage/' . $filePath))) {
                        $fileUrl = asset('storage/' . $filePath);
                    } elseif (file_exists(storage_path('app/public/' . $filePath))) {
                        $fileUrl = asset('storage/' . $filePath); // Asumsi symbolic link bekerja
                    } else {
                        $fileUrl = null;
                    }

                    $extension = $fileExists ? pathinfo($filePath, PATHINFO_EXTENSION) : null;
                @endphp

                {{-- Debug info --}}
                {{-- <div class="debug-info bg-gray-100 p-4 mb-4 rounded text-sm">
                    <p><strong>Database Path:</strong> {{ $filePath }}</p>
                    <p><strong>Storage Path:</strong> {{ $fullStoragePath }}</p>
                    <p><strong>Public Path:</strong> {{ $publicPath }}</p>
                    <p><strong>File Exists:</strong> {{ $fileExists ? 'YES' : 'NO' }}</p>
                    <p><strong>File URL:</strong> {{ $fileUrl ?? 'NULL' }}</p>
                </div> --}}

                @if ($fileExists)
                    @if (in_array(strtolower($extension), ['pdf']))
                        <iframe src="{{ $fileUrl }}#toolbar=0" class="w-full h-[800px] border rounded shadow-inner"
                            frameborder="0"></iframe>
                    @elseif(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ $fileUrl }}" alt="File Surat"
                            class="w-full max-w-3xl rounded shadow border mx-auto">
                    @else
                        <div class="p-4 bg-blue-50 rounded border border-blue-200">
                            <a href="{{ $fileUrl }}" download
                                class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download File Surat ({{ strtoupper($extension) }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-red-50 rounded border border-red-200 text-red-600">
                        <p>File tidak ditemukan di server.</p>
                        <p class="text-sm mt-2">Coba solusi berikut:</p>
                        <ul class="list-disc pl-5 mt-2">
                            <li>Jalankan <code class="bg-gray-200 px-1">php artisan storage:link</code></li>
                            <li>Periksa permission folder storage</li>
                            {{-- <li>Verifikasi file ada di: {{ $fullStoragePath }}</li> --}}
                        </ul>
                    </div>
                @endif
            </div>

            <div class="mt-6 text-right">
                <a href="{{ route('petugas.surat-masuk.index') }}"
                    class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
                    ← Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
