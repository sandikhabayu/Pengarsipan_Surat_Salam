@extends('layouts.petugas')

@section('title', 'Laporan Surat Masuk & Keluar')
@section('submenu-active', true)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header dan Form Filter (tidak dicetak) -->
        <div class="no-print">

            <form method="GET" action="{{ route('petugas.laporan.index') }}" class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan" id="bulan" class="border rounded px-3 py-2 w-40">
                            <option value="">Semua Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="tahun" id="tahun" class="border rounded px-3 py-2 w-40">
                            <option value="">Semua Tahun</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="bg-[#17AD90] hover:bg-[#136958] transition text-white px-4 py-2 rounded">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>

                    @if (request('bulan') || request('tahun'))
                        <a href="{{ route('petugas.laporan.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            @if (request('bulan') || request('tahun'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Menampilkan data untuk
                                @if (request('bulan') && request('tahun'))
                                    Bulan {{ DateTime::createFromFormat('!m', request('bulan'))->format('F') }} Tahun
                                    {{ request('tahun') }}
                                @elseif(request('tahun'))
                                    Tahun {{ request('tahun') }}
                                @elseif(request('bulan'))
                                    Bulan {{ DateTime::createFromFormat('!m', request('bulan'))->format('F') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Area yang akan dicetak -->
        <div id="printable-area">
            <!-- Header untuk cetakan -->
            <div class="print-only hidden mb-4">
                <h1 class="text-2xl font-bold text-center">LAPORAN SURAT</h1>
                <p class="text-center">SD N 2 BUMIREJO</p>
                @if (request('bulan') || request('tahun'))
                    <p class="text-center text-sm">
                        Periode:
                        @if (request('bulan') && request('tahun'))
                            {{ DateTime::createFromFormat('!m', request('bulan'))->format('F') }} {{ request('tahun') }}
                        @elseif(request('tahun'))
                            Tahun {{ request('tahun') }}
                        @elseif(request('bulan'))
                            Bulan {{ DateTime::createFromFormat('!m', request('bulan'))->format('F') }}
                        @endif
                    </p>
                @endif
                <hr class="my-2 border-t-2 border-gray-300">
            </div>

            <!-- Laporan Surat Masuk -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xl font-bold text-gray-700">Laporan Surat Masuk</h2>
                    <span class="text-sm text-gray-500">Total: {{ $suratMasuks->count() }} surat</span>
                </div>

                @if ($suratMasuks->isEmpty())
                    <div class="bg-white p-4 rounded-lg shadow text-center text-gray-500">
                        Tidak ada data surat masuk.
                    </div>
                @else
                    <div class="bg-white shadow-md rounded-lg overflow-x-auto print:shadow-none">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#A5EBDD] print:bg-gray-200">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Kode Surat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Nomor Surat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Pengirim</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Tanggal Masuk</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Perihal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($suratMasuks as $index => $surat)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $surat->kode_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $surat->nomor_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $surat->pengirim }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($surat->tanggal_masuk)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $surat->perihal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Laporan Surat Keluar -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xl font-bold text-gray-700">Laporan Surat Keluar</h2>
                    <span class="text-sm text-gray-500">Total: {{ $suratKeluars->count() }} surat</span>
                </div>

                @if ($suratKeluars->isEmpty())
                    <div class="bg-white p-4 rounded-lg shadow text-center text-gray-500">
                        Tidak ada data surat keluar.
                    </div>
                @else
                    <div class="bg-white shadow-md rounded-lg overflow-x-auto print:shadow-none">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#A5EBDD] print:bg-gray-200">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Kode Surat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Nomor Surat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Tujuan</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Tanggal Keluar</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                                        Perihal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($suratKeluars as $index => $surat)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $surat->kode_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $surat->nomor_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $surat->tujuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($surat->tanggal_keluar)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $surat->perihal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Aksi (tidak dicetak) -->
        <div class="no-print mt-6 flex gap-2">
            <button onclick="printReport()" class="bg-gray-600 hover:bg-gray-700 transition text-white px-4 py-2 rounded">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>

            <a href="{{ route('petugas.laporan.pdf', request()->query()) }}"
                class="bg-[#17AD90] hover:bg-[#136958] transition text-white px-4 py-2 rounded">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Script untuk Handle Print -->
    <script>
        function printReport() {
            // Clone printable area
            const printContent = document.getElementById('printable-area').cloneNode(true);

            // Show print-only header
            const printOnlyElements = printContent.querySelectorAll('.print-only');
            printOnlyElements.forEach(el => {
                el.classList.remove('hidden');
            });

            // Create new window for printing
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(`
        <html>
            <head>
                <title>Laporan Surat</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; }
                    td { border: 1px solid #ddd; padding: 8px; }
                    .text-center { text-align: center; }
                    .mb-4 { margin-bottom: 1rem; }
                    .my-2 { margin-top: 0.5rem; margin-bottom: 0.5rem; }
                    .border-t-2 { border-top-width: 2px; }
                    .border-gray-300 { border-color: #d1d5db; }
                    .hidden { display: none; }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 100);
                    };
                <\/script>
            </body>
        </html>
    `);
            printWindow.document.close();
        }
    </script>

    <!-- CSS untuk Print -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printable-area,
            #printable-area * {
                visibility: visible;
            }

            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .print-only {
                display: block !important;
            }
        }
    </style>
@endsection
