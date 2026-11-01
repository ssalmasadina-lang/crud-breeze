<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    // READ: Menampilkan daftar berkas milik user yang sedang login
    public function index()
    {
        $uploads = Upload::where('user_id', Auth::id())
            ->latest()
            ->paginate(5);

        return view('uploads.index', compact('uploads'));
    }

    // CREATE: Menampilkan form unggah berkas
    public function create()
    {
        return view('uploads.create');
    }

    // CREATE: Memvalidasi dan menyimpan berkas baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file');

        // Simpan file ke storage/app/public/uploads
        $path = $file->store('uploads', 'public');

        Upload::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'filename' => $file->getClientOriginalName(),
            'filepath' => $path,
        ]);

        return redirect()->route('uploads.index')
            ->with('success', 'Berkas berhasil diunggah.');
    }

    // READ: Menampilkan detail satu berkas
    public function show(Upload $upload)
    {
        abort_if($upload->user_id !== Auth::id(), 403);

        return view('uploads.show', compact('upload'));
    }

    // UPDATE: Menampilkan form edit berkas
    public function edit(Upload $upload)
    {
        abort_if($upload->user_id !== Auth::id(), 403);

        return view('uploads.edit', compact('upload'));
    }

    // UPDATE: Menyimpan perubahan, file baru bersifat opsional
    public function update(Request $request, Upload $upload)
    {
        abort_if($upload->user_id !== Auth::id(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $data = $request->only('judul', 'keterangan');

        if ($request->hasFile('file')) {
            // Hapus file lama
            Storage::disk('public')->delete($upload->filepath);

            $file = $request->file('file');

            $data['filename'] = $file->getClientOriginalName();
            $data['filepath'] = $file->store('uploads', 'public');
        }

        $upload->update($data);

        return redirect()->route('uploads.index')
            ->with('success', 'Berkas berhasil diperbarui.');
    }

    // DELETE: Menghapus file di storage dan data di database
    public function destroy(Upload $upload)
    {
        abort_if($upload->user_id !== Auth::id(), 403);

        // Hapus file fisik
        Storage::disk('public')->delete($upload->filepath);

        // Hapus data di tabel uploads
        $upload->delete();

        return redirect()->route('uploads.index')
            ->with('success', 'Berkas berhasil dihapus.');
    }
}
