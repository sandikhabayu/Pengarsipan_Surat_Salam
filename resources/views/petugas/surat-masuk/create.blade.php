@extends('layouts.petugas')

@section('title', 'Form Tambah Surat Masuk')

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

        <form action="{{ route('petugas.surat-masuk.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6 bg-white shadow p-6 rounded-lg">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kode_surat" class="block font-medium text-sm text-gray-700">Kode Surat</label>
                    <input type="text" name="kode_surat" id="kode_surat" value="{{ $kodeSurat }}" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="nomor_surat" class="block font-medium text-sm text-gray-700">Nomor Surat</label>
                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="lampiran" class="block font-medium text-sm text-gray-700">Lampiran</label>
                    <input type="text" name="lampiran" id="lampiran" value="{{ old('lampiran') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="tanggal_masuk" class="block font-medium text-sm text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="pengirim" class="block font-medium text-sm text-gray-700">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="perihal" class="block font-medium text-sm text-gray-700">Perihal</label>
                    <input type="text" name="perihal" id="perihal" value="{{ old('perihal') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            <div>
                <label for="file" class="block font-medium text-sm text-gray-700">Unggah File (PDF/DOC/JPG)</label>
                <input type="file" name="file" id="file"
                    class="mt-1 block w-full text-sm text-gray-500 file:bg-indigo-50 file:border-0 file:px-4 file:py-2 file:rounded-md file:text-indigo-700 hover:file:bg-indigo-100"
                    required>
            </div>

            <div>
                <label for="keterangan" class="block font-medium text-sm text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('petugas.surat-masuk.index') }}"
                    class="bg-[#F4B724] font-bold text-white px-4 py-2 rounded hover:bg-[#b88b22] transition">←
                    Kembali</a>
                <button type="submit"
                    class="bg-[#17AD90] text-white font-bold px-4 py-2 rounded hover:bg-[#136958] transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
