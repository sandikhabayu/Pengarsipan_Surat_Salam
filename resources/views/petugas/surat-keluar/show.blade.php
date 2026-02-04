@extends('layouts.petugas')

@section('title', 'Detail Surat Keluar')
@section('submenu-active', true)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow rounded-lg p-6">

            <div class="grid grid-cols-1 gap-4 text-lg text-gray-800">
                <div class="md:col-span-2 flex gap-16 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Kode Surat:</p>
                    <p>{{ $suratKeluar->kode_surat }}</p>
                </div>
                <div class="md:col-span-2 flex gap-14 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Nomor Surat:</p>
                    <p>{{ $suratKeluar->nomor_surat }}</p>
                </div>
                <div class="md:col-span-2 flex gap-20 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Lampiran:</p>
                    <p>{{ $suratKeluar->lampiran }}</p>
                </div>
                <div class="md:col-span-2 flex gap-10 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Tanggal Keluar:</p>
                    <p>{{ \Carbon\Carbon::parse($suratKeluar->tanggal_keluar)->format('d-m-Y') }}</p>
                </div>
                <div class="md:col-span-2 flex gap-28 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Tujuan:</p>
                    <p>{{ $suratKeluar->tujuan }}</p>
                </div>
                <div class="md:col-span-2 flex gap-28 border border-[#17AD90] rounded-md p-3"">
                    <p class="font-semibold">Perihal:</p>
                    <p>{{ $suratKeluar->perihal }}</p>
                </div>
                <div class="md:col-span-2 flex gap-20 border border-[#17AD90] rounded-md p-3">
                    <p class="font-semibold">Keterangan:</p>
                    <p>{{ $suratKeluar->keterangan ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-2">File Surat:</h2>
                @php
                    $filePath = $suratKeluar->file_path;
                    $fileExists = $filePath && Storage::disk('public')->exists($filePath);
                    $fileUrl = $fileExists ? asset('storage/' . $filePath) : null;
                    $extension = $fileExists ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
                @endphp

                @if ($fileExists)
                    <?php
                    $fileContent = Storage::disk('public')->get($filePath);
                    $base64 = base64_encode($fileContent);
                    ?>

                    @if ($extension === 'pdf')
                        <iframe src="data:application/pdf;base64,{{ $base64 }}"
                            class="w-full h-[800px] border rounded shadow-inner" frameborder="0"></iframe>
                    @elseif (in_array($extension, ['jpg', 'jpeg', 'png']))
                        <img src="data:image/{{ $extension }};base64,{{ $base64 }}" alt="File Surat"
                            class="w-full max-w-3xl rounded shadow border mx-auto">
                    @endif
                    {{-- @if ($extension === 'pdf')
                    <iframe src="{{ $fileUrl }}" class="w-full h-[800px] border rounded shadow-inner" frameborder="0"></iframe>
                @elseif (in_array($extension, ['jpg', 'jpeg', 'png']))
                    <img src="{{ $fileUrl }}" alt="File Surat" class="w-full max-w-3xl rounded shadow border mx-auto">
                @else
                    <a href="{{ $fileUrl }}" target="_blank" class="inline-block mt-2 text-blue-600 hover:underline">
                        📎 Lihat / Unduh File ({{ strtoupper($extension) }})
                    </a>
                @endif --}}
                @else
                    <p class="text-gray-500">Tidak ada file surat yang tersedia atau file tidak ditemukan.</p>
                @endif
            </div>

            <div class="mt-6 text-right">
                <a href="{{ route('petugas.surat-keluar.index') }}"
                    class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
                    ← Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
