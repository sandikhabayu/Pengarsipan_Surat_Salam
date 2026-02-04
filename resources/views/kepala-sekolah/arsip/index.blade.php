@extends('layouts.kepala-sekolah')

@section('title', 'Arsip Surat')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <form action="{{ route('kepala-sekolah.arsip.index') }}" method="GET"
                            class="flex items-center space-x-4">
                            <div>
                                <label for="jenis_surat" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
                                <select id="jenis_surat" name="jenis_surat"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#17AD90] focus:border-[#17AD90] sm:text-sm rounded-md">
                                    <option value="">Semua</option>
                                    <option value="masuk" {{ request('jenis_surat') === 'masuk' ? 'selected' : '' }}>Surat
                                        Masuk</option>
                                    <option value="keluar" {{ request('jenis_surat') === 'keluar' ? 'selected' : '' }}>Surat
                                        Keluar</option>
                                </select>
                            </div>
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#17AD90] focus:border-[#17AD90] sm:text-sm">
                            </div>
                            <div class="mt-6">
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#17AD90] hover:bg-[#136958] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#17AD90]">
                                        Filter
                                    </button>
                                    <a href="{{ route('kepala-sekolah.arsip.index') }}"
                                        class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#17AD90]">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if ($arsips->isEmpty())
                        <p class="text-gray-500">Tidak ada arsip ditemukan</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Surat</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pihak Terkait</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Perihal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($arsips as $arsip)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $arsip->nomor_surat }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $arsip->jenis_surat === 'masuk' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $arsip->jenis_surat === 'masuk' ? 'Surat Masuk' : 'Surat Keluar' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $arsip->tanggal->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $arsip->pihak_terkait }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $arsip->perihal }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ Storage::url($arsip->file_path) }}" target="_blank"
                                                    class="text-indigo-600 hover:text-indigo-900">Lihat</a>
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
        </div>
    </div>
@endsection
