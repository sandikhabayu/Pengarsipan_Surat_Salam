@extends('layouts.petugas')

@section('title', 'Kelola Arsip')
@section('submenu-active', true)

@section('content')
    <div class="container mx-auto px-4 py-8">

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

        {{-- Surat Masuk Belum Diarsipkan --}}
        <div class="mb-10">
            <div class="flex flex-col items-center pb-5">
                <h2 class="text-xl font-semibold text-gray-700 text-center mb-2">Surat Masuk Belum Diarsipkan</h2>
                <!-- Form Pencarian dan Filter Surat Masuk -->
                <form action="{{ route('petugas.arsip.index') }}" method="GET" class="flex items-center space-x-2">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search_masuk" placeholder="Cari surat masuk..."
                            value="{{ request('search_masuk') }}"
                            class="pl-10 pr-4 py-2 border-none bg-[#A5EBDD] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Bulan Filter -->
                    <select name="bulan_masuk"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" {{ request('bulan_masuk') == $month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tahun Filter -->
                    <select name="tahun_masuk"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach (range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}" {{ request('tahun_masuk') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-[#17AD90] hover:bg-[#136958] transition text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>

                    <a href="{{ route('petugas.arsip.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Reset
                    </a>
                </form>
            </div>

            @if ($suratMasuks->isEmpty())
                <p class="text-gray-500">Tidak ada surat masuk yang belum diarsipkan</p>
            @else
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-[#A5EBDD] text-gray-700">
                            <tr>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Nomor Surat</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Pengirim</th>
                                <th class="px-6 py-3">Perihal</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @foreach ($suratMasuks as $surat)
                                <tr>
                                    <td class="px-6 py-4">{{ $surat->kode_surat }}</td>
                                    <td class="px-6 py-4">{{ $surat->nomor_surat }}</td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($surat->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $surat->pengirim }}</td>
                                    <td class="px-6 py-4">{{ $surat->perihal }}</td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('petugas.arsip.arsipkan', $surat->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="jenis_surat" value="masuk">
                                            <button type="submit" class="text-blue-600 hover:underline">Arsipkan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Surat Keluar Belum Diarsipkan --}}
        <div class="mb-10">
            <div class="flex flex-col items-center pb-5">
                <h2 class="text-xl font-semibold text-gray-700 text-center mb-2">Surat Keluar Belum Diarsipkan</h2>
                <!-- Form Pencarian dan Filter Surat Keluar -->
                <form action="{{ route('petugas.arsip.index') }}" method="GET" class="flex items-center space-x-2">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search_keluar" placeholder="Cari surat keluar..."
                            value="{{ request('search_keluar') }}"
                            class="pl-10 pr-4 py-2 border-none bg-[#A5EBDD] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Bulan Filter -->
                    <select name="bulan_keluar"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" {{ request('bulan_keluar') == $month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tahun Filter -->
                    <select name="tahun_keluar"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach (range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}" {{ request('tahun_keluar') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-[#17AD90] hover:bg-[#136958] transition text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>

                    <a href="{{ route('petugas.arsip.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Reset
                    </a>
                </form>
            </div>

            @if ($suratKeluars->isEmpty())
                <p class="text-gray-500">Tidak ada surat keluar yang belum diarsipkan</p>
            @else
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-[#A5EBDD] text-gray-700">
                            <tr>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Nomor Surat</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Tujuan</th>
                                <th class="px-6 py-3">Perihal</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @foreach ($suratKeluars as $surat)
                                <tr>
                                    <td class="px-6 py-4">{{ $surat->kode_surat }}</td>
                                    <td class="px-6 py-4">{{ $surat->nomor_surat }}</td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($surat->tanggal_keluar)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $surat->tujuan }}</td>
                                    <td class="px-6 py-4">{{ $surat->perihal }}</td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('petugas.arsip.arsipkan', $surat->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="jenis_surat" value="keluar">
                                            <button type="submit" class="text-blue-600 hover:underline">Arsipkan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Daftar Arsip --}}
        <div>
            <div class="flex flex-col gap-2 justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Daftar Arsip</h2>

                <!-- Form Pencarian dan Filter -->
                <form action="{{ route('petugas.arsip.index') }}" method="GET" class="flex items-center space-x-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search" placeholder="Cari arsip..."
                            value="{{ request('search') }}"
                            class="pl-10 pr-4 py-2 border-none bg-[#A5EBDD] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Jenis Surat Filter -->
                    <select name="jenis_surat"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk
                        </option>
                        <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar
                        </option>
                    </select>

                    <!-- Bulan Filter -->
                    <select name="bulan"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" {{ request('bulan') == $month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tahun Filter -->
                    <select name="tahun"
                        class="px-4 py-2 border rounded-lg w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach (range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-[#17AD90] hover:bg-[#136958] transition text-white rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>

                    <a href="{{ route('petugas.arsip.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Reset
                    </a>
                </form>
            </div>

            @if ($arsips->isEmpty())
                <p class="text-gray-500">Belum ada arsip surat.</p>
            @else
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-[#A5EBDD] text-gray-700">
                            <tr>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Nomor Surat</th>
                                <th class="px-6 py-3">Jenis</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Pihak Terkait</th>
                                <th class="px-6 py-3">Perihal</th>
                                <th class="px-6 py-3 text-center">File</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @foreach ($arsips as $arsip)
                                <tr>
                                    <td class="px-6 py-4">{{ $arsip->kode_surat }}</td>
                                    <td class="px-6 py-4">{{ $arsip->nomor_surat }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 text-xs font-semibold rounded-full
                                        {{ $arsip->jenis_surat === 'masuk' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $arsip->jenis_surat === 'masuk' ? 'Surat Masuk' : 'Surat Keluar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($arsip->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">{{ $arsip->pihak_terkait }}</td>
                                    <td class="px-6 py-4">{{ $arsip->perihal }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($arsip->file_path)
                                            <a href="{{ route('arsip.showFile', $arsip->id) }}" target="_blank"
                                                class="text-indigo-600 hover:underline">Lihat</a>
                                        @else
                                            <span class="text-gray-400 italic">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('petugas.arsip.destroy', $arsip->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus arsip ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $arsips->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
