<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratMasuk::query();

        if ($request->filled('search')) {
            $query->where('nomor_surat', 'like', '%' . $request->search . '%')
                ->orWhere('pengirim', 'like', '%' . $request->search . '%')
                ->orWhere('perihal', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_masuk', $request->tanggal);
        }

        $suratMasuks = $query->latest()->paginate(10);

        return view('petugas.surat-masuk.index', compact('suratMasuks'));
    }


    public function create()
    {
        // Generate kode_surat otomatis
        $lastSurat = SuratMasuk::latest()->first();
        $lastNumber = $lastSurat ? (int) substr($lastSurat->kode_surat, -2) : 0;
        $kodeSurat = 'SM-'. str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

        return view('petugas.surat-masuk.create', compact('kodeSurat'));
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'kode_surat'     => 'required|string|unique:surat_masuks,kode_surat',
        'nomor_surat'    => 'required|string|max:255',
        'lampiran'       => 'required|string|max:255',
        'tanggal_masuk'  => 'required|date',
        'pengirim'       => 'required|string|max:255',
        'perihal'        => 'required|string|max:255',
        'file'           => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:3072',
        'keterangan'     => 'nullable|string',
    ]);

    // Simpan file dengan cara yang lebih reliable
    $file = $request->file('file');
    $fileName = time().'_'.str_replace(' ', '_', $file->getClientOriginalName()); // Ganti spasi dengan underscore
    $filePath = $file->storeAs('surat-masuk', $fileName, 'public');

    // Simpan juga ke public/storage secara manual (untuk Windows/Laragon)
    $file->move(public_path('storage/surat-masuk'), $fileName);

    SuratMasuk::create([
        'kode_surat'    => $validated['kode_surat'],
        'nomor_surat'   => $validated['nomor_surat'],
        'lampiran'      => $validated['lampiran'],
        'tanggal_masuk' => $validated['tanggal_masuk'],
        'pengirim'      => $validated['pengirim'],
        'perihal'       => $validated['perihal'],
        'file_path' => 'surat-masuk/'.$fileName,
        'keterangan'    => $validated['keterangan'],
    ]);

    return redirect()->route('petugas.surat-masuk.index')
                     ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function show(SuratMasuk $suratMasuk)
    {
        return view('petugas.surat-masuk.show', compact('suratMasuk'));
    }

    public function edit(SuratMasuk $suratMasuk)
    {
        return view('petugas.surat-masuk.edit', compact('suratMasuk'));
    }

    public function update(Request $request, SuratMasuk $suratMasuk)
{
    $rules = [
        'nomor_surat'   => 'required|string|max:255',
        'lampiran'      => 'required|string|max:255',
        'tanggal_masuk' => 'required|date',
        'pengirim'      => 'required|string|max:255',
        'perihal'       => 'required|string|max:255',
        'keterangan'    => 'nullable|string',
    ];

    if ($request->hasFile('file')) {
        $rules['file'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:3072';
    }

    $validated = $request->validate($rules);

    $data = [
        'nomor_surat'   => $validated['nomor_surat'],
        'lampiran'      => $validated['lampiran'],
        'tanggal_masuk' => $validated['tanggal_masuk'],
        'pengirim'      => $validated['pengirim'],
        'perihal'       => $validated['perihal'],
        'keterangan'    => $validated['keterangan'],
    ];

    if ($request->hasFile('file')) {
        // Hapus file lama
        if ($suratMasuk->file_path && Storage::disk('public')->exists($suratMasuk->file_path)) {
            Storage::disk('public')->delete($suratMasuk->file_path);
        }

        // Simpan file baru
        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $data['file_path'] = $file->storeAs('surat-masuk', $fileName, 'public');
    }

    $suratMasuk->update($data);

    return redirect()->route('petugas.surat-masuk.index')
                     ->with('success', 'Surat masuk berhasil diperbarui.');
}

public function destroy(SuratMasuk $suratMasuk)
{
    // Hapus file fisik
    if ($suratMasuk->file_path && Storage::disk('public')->exists($suratMasuk->file_path)) {
        Storage::disk('public')->delete($suratMasuk->file_path);
    }

    // Hapus record database
    $suratMasuk->delete();

    return redirect()->route('petugas.surat-masuk.index')
                     ->with('success', 'Surat masuk berhasil dihapus.');
}
}
