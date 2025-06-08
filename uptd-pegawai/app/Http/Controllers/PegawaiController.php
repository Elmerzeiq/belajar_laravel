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
        $request->validate(
            [
                'nama' => 'required',
                'jabatan' => 'required',
                'alamat' => 'required',
                'tanggal_lahir' => 'required',

            ],
            [
                'nama.required' => 'Nama Pegawai harus diisi.',
                'jabatan.required' => 'Jabatan harus diisi.',
                'alamat.required' => 'Alamat harus diisi.',
                'tanggal_lahir.required' => 'Tanggal Lahir harus diisi.',

            ]
        );

        Pegawai::create($request->all());
        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil disimpan.');
    }
}
