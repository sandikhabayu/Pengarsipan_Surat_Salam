@extends('layouts.petugas')

@section('title', 'Surat Keluar')
@section('submenu-active', true)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#003B4B]">Data Surat Keluar</h1>
            <div class="flex items-center gap-2">
                <form action="{{ route('petugas.surat-keluar.index') }}" method="GET"
                    class="flex flex-wrap p-2 rounded-3xl bg-[#A5EBDD]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nomor / tujuan / perihal..."
                        class="border-none bg-[#A5EBDD] px-4 py-2 text-sm w-64">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="border-none bg-[#A5EBDD] px-4 py-2 text-sm">
                </form>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('petugas.surat-template.create') }}"
                    class="bg-[#F4B724] text-white p-2 font-semibold rounded hover:bg-[#d89c1a] transition">
                    Buat dari Template
                </a>
                <a href="{{ route('petugas.surat-keluar.create') }}"
                    class="bg-[#17AD90] text-white p-2 font-semibold rounded hover:bg-[#136958] transition">
                    Tambah Surat Keluar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 border border-green-400 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-[#A5EBDD] text-gray-700 font-semibold">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Kode Surat</th>
                        <th class="px-6 py-3">Nomor Surat</th>
                        <th class="px-6 py-3">Lampiran</th>
                        <th class="px-6 py-3">Tujuan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Perihal</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($suratKeluars as $index => $surat)
                        <tr>
                            <td class="px-6 py-4">{{ $suratKeluars->firstItem() + $index }}</td>
                            <td class="px-6 py-4">{{ $surat->kode_surat }}</td>
                            <td class="px-6 py-4">{{ $surat->nomor_surat }}</td>
                            <td class="px-6 py-4">{{ $surat->lampiran }}</td>
                            <td class="px-6 py-4">{{ $surat->tujuan }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($surat->tanggal_keluar)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4">{{ $surat->perihal }}</td>
                            <td class="px-6 py-4">{{ $surat->keterangan ?? '-' }}</td>
                            <td class="px-6 py-4 flex pt-6 space-x-2">
                                <a href="{{ route('petugas.surat-keluar.show', $surat->id) }}"
                                    class="text-blue-600 hover:underline">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('petugas.surat-keluar.edit', $surat->id) }}"
                                    class="text-green-600 hover:text-green-800 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('petugas.surat-keluar.destroy', $surat->id) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data surat keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $suratKeluars->links() }}
        </div>
    </div>
@endsection
