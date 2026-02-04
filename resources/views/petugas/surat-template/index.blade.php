<!-- resources/views/petugas/surat-template/index.blade.php -->

@extends('layouts.petugas')

@section('title', 'Pembuatan Surat')

@section('content')
    <div class="">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#003B4B]">Daftar Pembuatan Surat</h1>
            <a href="{{ route('petugas.surat-template.create') }}"
                class="px-4 py-2 rounded text-white font-bold bg-[#17AD90] hover:bg-[#136958] transition">Buat Surat
                Baru</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Pencarian dan Filter -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('petugas.surat-template.index') }}"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Pencarian -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Surat</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Cari perihal, nomor surat, atau tujuan..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#17AD90] focus:border-transparent">
                </div>

                <!-- Filter Bulan -->
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" id="bulan"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#17AD90] focus:border-transparent">
                        <option value="">Semua Bulan</option>
                        @foreach ($bulanList as $key => $bulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" id="tahun"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#17AD90] focus:border-transparent">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-2 items-end">
                    <button type="submit"
                        class="px-4 py-2 bg-[#17AD90] text-white rounded-md hover:bg-[#136958] transition font-medium">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('petugas.surat-template.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition font-medium">
                        Reset
                    </a>
                </div>
            </form>

            <!-- Info Filter Aktif -->
            @if (request()->anyFilled(['search', 'tanggal_mulai', 'tanggal_selesai', 'bulan', 'tahun']))
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-700">
                        <strong>Filter aktif:</strong>
                        @if (request('search'))
                            Pencarian "{{ request('search') }}" •
                        @endif
                        @if (request('tanggal_mulai'))
                            Dari {{ request('tanggal_mulai') }} •
                        @endif
                        @if (request('tanggal_selesai'))
                            Sampai {{ request('tanggal_selesai') }} •
                        @endif
                        @if (request('bulan'))
                            Bulan {{ $bulanList[request('bulan')] }} •
                        @endif
                        @if (request('tahun'))
                            Tahun {{ request('tahun') }} •
                        @endif
                        <strong>Total: {{ $surats->count() }} surat ditemukan</strong>
                    </p>
                </div>
            @endif
        </div>

        <!-- Tabel Data -->
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#A5EBDD] text-gray-700 text-center text-sm font-semibold">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nomor Surat</th>
                        <th class="px-6 py-3">Perihal</th>
                        <th class="px-6 py-3">Tujuan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-center text-sm">
                    @forelse ($surats as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-mono text-sm">{{ $item->nomor_surat }}</td>
                            <td class="px-6 py-4">{{ $item->perihal }}</td>
                            <td class="px-6 py-4">{{ $item->kepada }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('petugas.surat-template.show', $item->id) }}"
                                        class="text-blue-600 hover:text-blue-800 transition" title="Lihat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('petugas.surat-template.edit', $item->id) }}"
                                        class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('petugas.surat-template.download', $item->id) }}"
                                        class="text-yellow-600 hover:text-yellow-800 transition" title="Download PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('petugas.surat-template.destroy', $item->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">Tidak ada data surat.</p>
                                @if (request()->anyFilled(['search', 'tanggal_mulai', 'tanggal_selesai', 'bulan', 'tahun']))
                                    <p class="text-sm">Coba ubah filter pencarian Anda.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Info Jumlah Data -->
        @if ($surats->count() > 0)
            <div class="mt-4 text-sm text-gray-600">
                Menampilkan <strong>{{ $surats->count() }}</strong> surat
                @if (request()->anyFilled(['search', 'tanggal_mulai', 'tanggal_selesai', 'bulan', 'tahun']))
                    berdasarkan filter yang diterapkan
                @endif
            </div>
        @endif
    </div>

    <style>
        .isi-baris {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .text-justify {
            text-align: justify;
        }

        .text-left {
            text-align: left;
        }
    </style>
@endsection
