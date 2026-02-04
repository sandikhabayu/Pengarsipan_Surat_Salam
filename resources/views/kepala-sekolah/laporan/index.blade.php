@extends('layouts.kepala-sekolah')

@section('title', 'Laporan Surat')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Laporan Surat</h1>

    <div class="bg-white p-4 rounded shadow">
        <p class="mb-4">Berikut adalah laporan ringkas aktivitas surat masuk dan keluar.</p>

        <ul class="list-disc list-inside text-gray-700">
            <li>Total Surat Masuk: {{ $totalMasuk }}</li>
            <li>Total Surat Keluar: {{ $totalKeluar }}</li>
            <li>Surat Terbaru: {{ $suratTerbaru->nomor ?? 'Tidak ada' }}</li>
        </ul>
    </div>
</div>
@endsection
