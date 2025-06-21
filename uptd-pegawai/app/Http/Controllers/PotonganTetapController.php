<?php

namespace App\Http\Controllers;

use App\Models\PotonganTetap;
use Illuminate\Http\Request;

class PotonganTetapController extends Controller
{
    public function index()
    {
        $potongan = PotonganTetap::all();
        return view('potongan_tetap.index', compact('potongan'));
    }

    public function create()
    {
        return view('potongan_tetap.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_potongan'   => 'required|string|max:100',
            'tipe'            => 'required|in:tetap,persen',
            'jumlah'          => 'required|numeric|min:0',
            'jenis_potongan'  => 'required|in:gaji_pokok,insentif,total',
        ], [
            'nama_potongan.required'   => 'Nama Potongan wajib diisi.',
            'jumlah.numeric'           => 'Jumlah Potongan harus berupa angka.',
            'tipe.required'            => 'Tipe Potongan wajib dipilih.',
            'jenis_potongan.required'  => 'Jenis Potongan wajib dipilih.',
        ]);

        PotonganTetap::create($request->all());

        return redirect()->route('potongan-tetap.index')->with('success', 'Potongan berhasil ditambahkan');
    }

    public function edit(PotonganTetap $potongan_tetap)
    {
        return view('potongan_tetap.create', compact('potongan_tetap'));
    }

    public function update(Request $request, PotonganTetap $potongan_tetap)
    {
        $request->validate([
            'nama_potongan'   => 'required|string|max:100',
            'tipe'            => 'required|in:tetap,persen',
            'jumlah'          => 'required|numeric|min:0',
            'jenis_potongan'  => 'required|in:gaji_pokok,insentif,total',
        ], [
            'nama_potongan.required'   => 'Nama Potongan wajib diisi.',
            'jumlah.numeric'           => 'Jumlah Potongan harus berupa angka.',
            'tipe.required'            => 'Tipe Potongan wajib dipilih.',
            'jenis_potongan.required'  => 'Jenis Potongan wajib dipilih.',
        ]);

        $potongan_tetap->update($request->all());

        return redirect()->route('potongan-tetap.index')->with('success', 'Potongan berhasil diperbarui');
    }

    public function destroy(PotonganTetap $potongan_tetap)
    {
        $potongan_tetap->delete();

        return redirect()->route('potongan-tetap.index')->with('success', 'Potongan berhasil dihapus');
    }
}
