@extends('layouts.petugas')

@section('title', 'Tambah Surat Keluar')

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

        <form action="{{ route('petugas.surat-keluar.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6 bg-white shadow p-6 rounded-lg">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kode_surat" class="block font-medium text-sm text-gray-700">Kode Surat</label>
                    <input type="text" name="kode_surat" id="kode_surat" value="{{ $kodeSurat }}" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_urut" class="block font-medium text-sm text-gray-700">Nomor Urut</label>
                        <input type="text" name="nomor_urut" id="nomor_urut" value="{{ $nomorUrut }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <p class="text-xs text-gray-500 mt-1">Nomor urut terakhir: {{ $lastNomorUrut }}</p>
                    </div>

                    <div>
                        <label for="format_surat" class="block font-medium text-sm text-gray-700">Format Surat</label>
                        <input type="text" name="format_surat" id="format_surat" value="{{ old('format_surat') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                </div>

                <div>
                    <label for="tanggal_keluar" class="block font-medium text-sm text-gray-700">Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" id="tanggal_keluar"
                        value="{{ old('tanggal_keluar', date('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="lampiran" class="block font-medium text-sm text-gray-700">Lampiran</label>
                    <input type="text" name="lampiran" id="lampiran" value="{{ old('lampiran') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="tujuan" class="block font-medium text-sm text-gray-700">Tujuan</label>
                    <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label for="perihal" class="block font-medium text-sm text-gray-700">Perihal</label>
                    <input type="text" name="perihal" id="perihal" value="{{ old('perihal') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>


            <div>
                <label for="file" class="block font-medium text-sm text-gray-700">Unggah File (PDF/DOC/JPG/PNG) - Maks.
                    3MB</label>
                <input type="file" name="file" id="file"
                    class="mt-1 block w-full text-sm text-gray-500 file:bg-indigo-50 file:border-0 file:px-4 file:py-2 file:rounded-md file:text-indigo-700 hover:file:bg-indigo-100"
                    required>
            </div>

            <div>
                <label for="keterangan" class="block font-medium text-sm text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="bg-blue-50 p-4 rounded-md">
                <p class="text-sm text-blue-700">
                    <strong>Info:</strong> Sistem akan otomatis menampilkan nomor urut berikutnya ({{ $nomorUrut }}).
                    Anda dapat mengubahnya secara manual. Format surat: <span id="previewNomorSurat"
                        class="font-bold">{{ $nomorUrut }}/SEK/2024</span>
                </p>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('petugas.surat-keluar.index') }}"
                    class="bg-[#F4B724] font-bold text-white px-4 py-2 rounded hover:bg-[#b88b22] transition">
                    ← Kembali
                </a>
                <button type="submit"
                    class="bg-[#17AD90] text-white font-bold px-4 py-2 rounded hover:bg-[#136958] transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nomorUrutInput = document.getElementById('nomor_urut');
            const formatSuratInput = document.getElementById('format_surat');
            const previewElement = document.getElementById('previewNomorSurat');

            function updatePreview() {
                const nomorUrut = nomorUrutInput.value || '000';
                const formatSurat = formatSuratInput.value || '';
                previewElement.textContent = `${nomorUrut}/${formatSurat}`;
            }

            nomorUrutInput.addEventListener('input', updatePreview);
            formatSuratInput.addEventListener('input', updatePreview);

            // Initial preview
            updatePreview();
        });
    </script>
@endsection
