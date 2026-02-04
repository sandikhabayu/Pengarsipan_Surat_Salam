@extends('layouts.kepala-sekolah')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
    <div>
        <!-- Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-6">
            <div class="bg-white shadow-lg rounded-lg p-4 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold text-gray-500 mb-2">Surat Masuk</h2>
                <p class="text-2xl font-bold">{{ $countSuratMasuk }}</p>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-4 border-l-4 border-green-500">
                <h2 class="text-xl font-bold text-gray-500 mb-2">Surat Keluar</h2>
                <p class="text-2xl font-bold">{{ $countSuratKeluar }}</p>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-4 border-l-4 border-yellow-500">
                <h2 class="text-xl font-bold text-gray-500 mb-2">Arsip</h2>
                <p class="text-2xl font-bold">{{ $countArsip }}</p>
            </div>
        </div>

        <!-- Laporan Terkini -->
        <div class="space-y-8">
            <h2 class="text-xl font-semibold text-gray-800">Laporan Terkini</h2>

            <!-- Surat Masuk -->
            <div>
                <h3 class="text-center font-bold text-gray-700 mb-2">Surat Masuk</h3>
                <div class="overflow-auto bg-white rounded-lg shadow">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-teal-200 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Kode Surat</th>
                                <th class="px-4 py-2 border">Nomor Surat</th>
                                <th class="px-4 py-2 border">Pengirim</th>
                                <th class="px-4 py-2 border">Tanggal Masuk</th>
                                <th class="px-4 py-2 border">Perihal</th>
                                <th class="px-4 py-2 border">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSuratMasuks as $index => $surat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->kode_surat }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->nomor_surat }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->pengirim }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ \Carbon\Carbon::parse($surat->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->perihal }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center px-4 py-2 border text-gray-500">Tidak ada surat
                                        masuk
                                        terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Surat Keluar -->
            <div>
                <h3 class="text-center font-bold text-gray-700 mb-2">Surat Keluar</h3>
                <div class="overflow-auto bg-white rounded-lg shadow">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-teal-200 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Kode Surat</th>
                                <th class="px-4 py-2 border">Nomor Surat</th>
                                <th class="px-4 py-2 border">Tujuan</th>
                                <th class="px-4 py-2 border">Tanggal Keluar</th>
                                <th class="px-4 py-2 border">Perihal</th>
                                <th class="px-4 py-2 border">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSuratKeluars as $index => $surat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->kode_surat }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->nomor_surat }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->tujuan }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ \Carbon\Carbon::parse($surat->tanggal_keluar)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->perihal }}</td>
                                    <td class="px-4 py-2 border">{{ $surat->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center px-4 py-2 border text-gray-500">Tidak ada surat
                                        keluar
                                        terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
