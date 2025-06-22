<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all();
        return view('pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'nip' => 'required|numeric',
            'jabatan' => 'required|string',
            'gaji_pokok' => 'required|numeric',
            'insentif_kotor' => 'required|numeric',
        ], [
            'nama.required' => 'Nama Pegawai harus diisi.',
            'nip.required' => 'NIP harus diisi.',
            'jabatan.required' => 'Jabatan harus diisi.',
            'gaji_pokok.required' => 'Gaji Pokok harus diisi.',
            'insentif_kotor.required' => 'Insentif Kotor harus diisi.',
            'gaji_pokok.numeric' => 'Gaji Pokok harus berupa angka.',
            'insentif_kotor.numeric' => 'Insentif Kotor harus berupa angka.',
        ]);

        // Tangani AutoNumeric input (jika kosong bisa terkirim sebagai "" atau dengan titik pemisah ribuan)
        $validated['gaji_pokok'] = str_replace('.', '', $validated['gaji_pokok']) ?: 0;
        $validated['insentif_kotor'] = str_replace('.', '', $validated['insentif_kotor']) ?: 0;

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil disimpan.');
    }

    public function edit(String $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'nip' => 'required|numeric',
            'jabatan' => 'required|string',
            'gaji_pokok' => 'required|numeric',
            'insentif_kotor' => 'required|numeric',
        ], [
            'nama.required' => 'Nama Pegawai harus diisi.',
            'nip.required' => 'NIP harus diisi.',
            'jabatan.required' => 'Jabatan harus diisi.',
            'gaji_pokok.required' => 'Gaji Pokok harus diisi.',
            'insentif_kotor.required' => 'Insentif Kotor harus diisi.',
            'gaji_pokok.numeric' => 'Gaji Pokok harus berupa angka.',
            'insentif_kotor.numeric' => 'Insentif Kotor harus berupa angka.',
        ]);

        $validated['gaji_pokok'] = str_replace('.', '', $validated['gaji_pokok']) ?: 0;
        $validated['insentif_kotor'] = str_replace('.', '', $validated['insentif_kotor']) ?: 0;

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    public function destroy(String $id)
    {
        Pegawai::destroy($id);
        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil dihapus.');
    }
}
