<?php

namespace App\Http\Controllers;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jasas = Jasa::all();
        return view('servis.jasamanage', compact('jasas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('servis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'harga' => 'required|numeric|min:0',
        'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
    ]);

    // Buat instance Jasa baru
    $jasa = new Jasa();
    $jasa->nama = $request->nama;
    $jasa->deskripsi = $request->deskripsi;
    $jasa->harga = $request->harga;

    // Simpan gambar
    if ($request->hasFile('gambar')) {
        $image = $request->file('gambar');
        $jasa->saveImage($image);
    }

    // Simpan data ke dalam database
    $jasa->save();

    // Redirect ke halaman indeks jasa dengan pesan sukses
    return redirect()->route('servis.index')->with('success', 'Jasa berhasil ditambahkan.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Temukan jasa berdasarkan ID
        $jasa = Jasa::findOrFail($id);

        // Tampilkan halaman edit dengan data jasa yang ditemukan
        return view('servis.edit', compact('jasa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang dikirim dari form
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required|numeric',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // optional: menambahkan validasi untuk jenis dan ukuran gambar
        ]);

        // Temukan jasa berdasarkan ID
        $jasa = Jasa::findOrFail($id);

        // Update data jasa berdasarkan data yang dikirim dari form
        $jasa->nama = $request->nama;
        $jasa->deskripsi = $request->deskripsi;
        $jasa->harga = $request->harga;

        // Jika ada gambar yang dikirim dari form, simpan gambar yang baru
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $nama_gambar = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/images', $nama_gambar);
            $jasa->gambar = $nama_gambar;
        }

        // Simpan perubahan pada data jasa
        $jasa->save();

        // Redirect kembali ke halaman manage dengan pesan sukses
        return redirect()->route('servis.index')->with('success', 'Jasa berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    // Temukan jasa berdasarkan ID
    $jasa = Jasa::findOrFail($id);

    // Hapus gambar dari penyimpanan
    if ($jasa->gambar) {
        Storage::delete('public/images/' . $jasa->gambar);
    }

    // Hapus jasa dari database
    $jasa->delete();

    // Redirect kembali ke halaman manage dengan pesan sukses
    return redirect()->route('servis.index')->with('success', 'Jasa berhasil dihapus.');
}

    public function service()
    {
        $jasas = Jasa::all();
        return view('servis.service', compact('jasas'));  
    }

}
