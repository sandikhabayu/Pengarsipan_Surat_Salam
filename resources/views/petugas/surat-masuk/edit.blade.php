@extends('layouts.petugas')

@section('title', 'Edit Surat Masuk')

@section('content')
    <div class="container mx-auto px-4 py-8">

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('petugas.surat-masuk.update', $suratMasuk->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6 bg-white shadow p-6 rounded-lg">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="kode_surat" class="block font-medium text-md text-gray-700">Kode Surat</label>
                    <input type="text" name="kode_surat" id="kode_surat"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed"
                        value="{{ old('kode_surat', $suratMasuk->kode_surat) }}" readonly>
                </div>

                <div>
                    <label for="nomor_surat" class="block font-medium text-md text-gray-700">Nomor Surat</label>
                    <input type="text" name="nomor_surat" id="nomor_surat"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('nomor_surat', $suratMasuk->nomor_surat) }}" required>
                </div>

                <div>
                    <label for="lampiran" class="block font-medium text-md text-gray-700">Lampiran</label>
                    <input type="text" name="lampiran" id="lampiran"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('lampiran', $suratMasuk->lampiran) }}" required>
                </div>

                <div>
                    <label for="tanggal_masuk" class="block font-medium text-md text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('tanggal_masuk', $suratMasuk->tanggal_masuk) }}" required>
                </div>

                <div>
                    <label for="pengirim" class="block font-medium text-md text-gray-700">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('pengirim', $suratMasuk->pengirim) }}" required>
                </div>

                <div>
                    <label for="perihal" class="block font-medium text-md text-gray-700">Perihal</label>
                    <input type="text" name="perihal" id="perihal"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('perihal', $suratMasuk->perihal) }}" required>
                </div>

                <div>
                    <label for="file" class="block font-medium text-md text-gray-700">Upload File Baru
                        (Opsional)</label>
                    <input type="file" name="file" id="file"
                        class="mt-1 block w-full text-sm text-gray-500 file:bg-indigo-50 file:border-0 file:px-4 file:py-2 file:rounded-md file:text-indigo-700 hover:file:bg-indigo-100">
                    <a href="{{ asset('storage/' . $suratMasuk->file_path) }}" target="_blank"
                        class="text-blue-600 text-sm hover:underline">
                        Lihat File Saat Ini
                    </a>
                    <p class="text-xs text-gray-500 mt-1">Abaikan jika tidak ingin mengganti file.</p>
                </div>
            </div>

            <div>
                <label for="keterangan" class="block font-medium text-md text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('keterangan', $suratMasuk->keterangan) }}</textarea>
            </div>

            <div class="flex justify-end items-center gap-4 text-lg">
                <a href="{{ route('petugas.surat-masuk.index') }}"
                    class="bg-yellow-500 py-2 px-4 rounded text-white hover:bg-yellow-600 transition">
                    Batal</a>
                <button type="submit" class="bg-[#17AD90] text-white px-4 py-2 rounded hover:bg-[#136958] transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
