@extends('layouts.petugas')

@section('title', 'Detail Surat')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="m-10 p-6">
                <!-- Header Surat -->
                <div class="pb-2 border-b-4 border-stone-900 flex justify-around">
                    <div>
                        <img src="{{ asset('images/logo_salam.png') }}" alt="Logo Sekolah" class="h-28 mx-auto mb-4">
                    </div>
                    <div class="text-center ">
                        <h4 class="text-2xl font-semibold">PEMERINTAH KABUPATEN PURWOREJO</h4>
                        <h4 class="text-2xl font-semibold">KECAMATAN GEBANG</h4>
                        <h4 class="text-2xl font-bold">
                            {{ $suratTemplate->jenis_surat == 'kepala_desa' ? 'KEPALA DESA' : 'SEKRETARIAT' }} DESA SALAM
                        </h4>
                        <p class="text-md leading-5">Alamat: Desa Salam Kecamatan Gebang Kabupaten Purworejo
                            Kode Pos 54191</p>
                    </div>
                    <div></div>
                </div>

                <!-- Data Surat -->
                <div class="mt-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <table>
                                <tr>
                                    <td class="pr-2">Nomor Surat</td>
                                    <td>:</td>
                                    <td class="pl-2">{{ $suratTemplate->nomor_surat ?? 'Nomor tidak tersedia' }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2">Lampiran</td>
                                    <td>:</td>
                                    <td class="pl-2">{{ $suratTemplate->lampiran ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2">Hal</td>
                                    <td>:</td>
                                    <td class="pl-2 font-bold underline">
                                        {{ $suratTemplate->perihal ?? 'Perihal tidak tersedia' }}</td>
                            </table>
                        </div>
                        <div class="text-left">
                            <div class="pt-24"></div>
                            <p class="text-gray-950">Kepada Yth :
                            </p>
                            <span class="italic font-bold">{{ $suratTemplate->kepada ?? 'Penerima tidak tersedia' }}</span>
                            <p class="text-gray-950">Di Tempat</p>
                        </div>
                    </div>


                    <!-- Isi Surat -->
                    <div class="mb-8">
                        <div class="text-gray-950 whitespace-pre-line">
                            {!! old('isi_surat', $suratTemplate->isi_surat) !!}
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="flex justify-end mt-12 pr-16">
                        <div class="text-center">
                            <p class="text-gray-950">
                                {{ $suratTemplate->jenis_surat == 'kepala_desa' ? 'Pj. Kepala Desa Salam' : 'Sekretaris Desa Salam' }}
                            </p>
                            <div class="h-24"></div>
                            <p class="font-bold underline">
                                {{ $suratTemplate->jenis_surat == 'kepala_desa' ? 'BAMBANG LISTIONO AGUS,P.S.Sos' : 'MAULANA AMIRUL AKHMAD' }}
                            </p>
                            <p class="text-gray-950">
                                @if ($suratTemplate->jenis_surat == 'kepala_desa' || $suratTemplate->jenis_surat == 'wakil_kepala_desa')
                                    Pembina /IVa, NIP.196808111989031008
                                @else
                                    &nbsp;
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('petugas.surat-template.index') }}"
                class="bg-yellow-500 py-2 px-4 rounded text-white hover:bg-yellow-600 transition">
                Kembali ke Daftar
            </a>
            <a href="{{ route('petugas.surat-template.download', $suratTemplate->id) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Download PDF
            </a>
        </div>
    </div>
@endsection
