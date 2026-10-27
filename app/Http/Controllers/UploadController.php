<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Tampilan Manajemen Berkas & Media Toko (Integrasi Acara 29 - File Storage).
     */
    public function index()
    {
        // Pastikan folder uploads tersedia
        Storage::disk('public')->makeDirectory('uploads');

        $allFiles = Storage::disk('public')->files('uploads');
        $files = [];
        $totalBytes = 0;

        foreach ($allFiles as $file) {
            $filename = basename($file);
            $size = Storage::disk('public')->size($file);
            $lastModified = Storage::disk('public')->lastModified($file);
            $mimeType = Storage::disk('public')->mimeType($file);
            $url = asset('storage/' . $file);
            $totalBytes += $size;

            $isImage = str_starts_with($mimeType ?? '', 'image/');

            $files[] = [
                'name'          => $filename,
                'path'          => $file,
                'url'           => $url,
                'size'          => $this->formatBytes($size),
                'size_bytes'    => $size,
                'last_modified' => date('d M Y, H:i', $lastModified),
                'mime_type'     => $mimeType,
                'is_image'      => $isImage,
            ];
        }

        // Urutkan file terbaru di atas
        usort($files, fn($a, $b) => strcmp($b['name'], $a['name']));

        $stats = [
            'total_files' => count($files),
            'total_usage' => $this->formatBytes($totalBytes),
        ];

        return view('upload', compact('files', 'stats'));
    }

    /**
     * Mengunggah berkas baru ke storage publik.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,png,jpg,webp,gif,pdf,doc,docx,xls,xlsx,zip',
        ], [
            'file.required' => 'Silakan pilih berkas yang ingin diunggah.',
            'file.max'      => 'Ukuran berkas maksimal adalah 10 MB.',
            'file.mimes'    => 'Format berkas tidak diizinkan. Gunakan format gambar, PDF, dokumen Word/Excel, atau ZIP.',
        ]);

        $uploaded = $request->file('file');
        $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $uploaded->getClientOriginalExtension();
        $safeName = time() . '_' . Str::slug($originalName) . '.' . $extension;

        Storage::disk('public')->makeDirectory('uploads');
        $uploaded->storeAs('uploads', $safeName, 'public');

        return redirect()->route('media.index')
            ->with('success', "Berkas '{$safeName}' berhasil diunggah ke penyimpanan sistem!");
    }

    /**
     * Mengunduh berkas dari media storage.
     */
    public function download($filename)
    {
        $filePath = 'uploads/' . basename($filename);

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('media.index')->with('error', 'Berkas tidak ditemukan.');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Menghapus berkas dari media storage.
     */
    public function destroy($filename)
    {
        $filePath = 'uploads/' . basename($filename);

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('media.index')->with('error', 'Berkas tidak ditemukan.');
        }

        Storage::disk('public')->delete($filePath);

        return redirect()->route('media.index')
            ->with('success', "Berkas '{$filename}' berhasil dihapus dari penyimpanan.");
    }

    /**
     * Format byte ke ukuran yang mudah dibaca (KB / MB).
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
