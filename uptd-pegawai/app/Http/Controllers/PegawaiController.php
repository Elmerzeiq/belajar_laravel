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
            'nama' => 'required|string|max:25',
            'nip' => 'required|string|max:20',
            'jabatan' => 'required|string',
            'gaji_pokok' => 'required|numeric',
            'insentif_kotor' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validasi foto
        ], [
            'nama.required' => 'Nama Pegawai harus diisi.',
            'nip.required' => 'Nomor Pegawai harus diisi.',
            'nip.max' => 'Nomor Pegawai maksimal 20 karakter.',
            'jabatan.required' => 'Jabatan harus Dipilih',
            'gaji_pokok.required' => 'Gaji Pokok harus diisi.',
            'insentif_kotor.required' => 'Insentif Kotor harus diisi.',
            'gaji_pokok.numeric' => 'Gaji Pokok harus berupa angka.',
            'insentif_kotor.numeric' => 'Insentif Kotor harus berupa angka.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Tangani AutoNumeric input (jika kosong bisa terkirim sebagai "" atau dengan titik pemisah ribuan)
        $validated['gaji_pokok'] = str_replace('.', '', $validated['gaji_pokok']) ?: 0;
        $validated['insentif_kotor'] = str_replace('.', '', $validated['insentif_kotor']) ?: 0;

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $namaFile = uniqid('foto_') . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('foto_pegawai'), $namaFile);
            $validated['foto'] = $namaFile;
        }

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
            'nama' => 'required|string|max:25',
            'nip' => 'required|string|max:20',
            'jabatan' => 'required|string',
            'gaji_pokok' => 'required|numeric',
            'insentif_kotor' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validasi foto
        ], [
            'nama.required' => 'Nama Pegawai harus diisi.',
            'nip.required' => 'Nomor Pegawai harus diisi.',
            'nip.max' => 'Nomor Pegawai maksimal 20 karakter.',
            'jabatan.required' => 'Jabatan harus Dipilih.',
            'gaji_pokok.required' => 'Gaji Pokok harus diisi.',
            'insentif_kotor.required' => 'Insentif Kotor harus diisi.',
            'gaji_pokok.numeric' => 'Gaji Pokok harus berupa angka.',
            'insentif_kotor.numeric' => 'Insentif Kotor harus berupa angka.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $validated['gaji_pokok'] = str_replace('.', '', $validated['gaji_pokok']) ?: 0;
        $validated['insentif_kotor'] = str_replace('.', '', $validated['insentif_kotor']) ?: 0;

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($pegawai->foto && file_exists(public_path('foto_pegawai/' . $pegawai->foto))) {
                @unlink(public_path('foto_pegawai/' . $pegawai->foto));
            }
            $foto = $request->file('foto');
            $namaFile = uniqid('foto_') . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('foto_pegawai'), $namaFile);
            $validated['foto'] = $namaFile;
        }

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    public function destroy(String $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        try {
            $pegawai->delete();

            // Hapus foto hanya jika delete berhasil
            if ($pegawai->foto && file_exists(public_path('foto_pegawai/' . $pegawai->foto))) {
                @unlink(public_path('foto_pegawai/' . $pegawai->foto));
            }

            return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                // Foreign key constraint violation
                return redirect()->route('pegawai.index')->with('error', 'Data pegawai tidak bisa dihapus karena masih memiliki data gaji. Silakan hapus data gaji terlebih dahulu.');
            }
            return redirect()->route('pegawai.index')->with('error', 'Terjadi kesalahan saat menghapus data pegawai.');
        }
    }
}
