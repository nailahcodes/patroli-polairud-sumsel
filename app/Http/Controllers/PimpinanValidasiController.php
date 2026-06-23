<?php

namespace App\Http\Controllers;

use App\Models\Patroli;
use Illuminate\Http\Request;

class PimpinanValidasiController extends Controller
{
    private function ensurePimpinan(): void
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Hanya Pimpinan yang dapat melakukan validasi patroli.');
        }
    }

    public function valid(Request $request, Patroli $patroli)
    {
        $this->ensurePimpinan();

        if (! in_array($patroli->status, ['selesai', 'perbaiki', 'valid'])) {
            return back()->with('error', 'Patroli hanya dapat divalidasi setelah statusnya selesai.');
        }

        $patroli->update([
            'status' => 'valid',
            'validasi_pimpinan_status' => 'valid',
            'validasi_pimpinan_catatan' => null,
            'validasi_pimpinan_user_id' => auth()->id(),
            'validasi_pimpinan_at' => now(),
        ]);

        return back()->with('success', 'Patroli berhasil divalidasi oleh Pimpinan.');
    }

    public function perbaiki(Request $request, Patroli $patroli)
    {
        $this->ensurePimpinan();

        if (! in_array($patroli->status, ['selesai', 'perbaiki', 'valid'])) {
            return back()->with('error', 'Patroli hanya dapat diberi catatan perbaikan setelah statusnya selesai.');
        }

        $request->validate([
            'validasi_pimpinan_catatan' => ['required', 'string'],
        ], [
            'validasi_pimpinan_catatan.required' => 'Keterangan perbaikan wajib diisi.',
        ]);

        $patroli->update([
            'status' => 'perbaiki',
            'validasi_pimpinan_status' => 'perbaiki',
            'validasi_pimpinan_catatan' => $request->validasi_pimpinan_catatan,
            'validasi_pimpinan_user_id' => auth()->id(),
            'validasi_pimpinan_at' => now(),
        ]);

        return back()->with('success', 'Patroli ditandai perlu perbaikan.');
    }
}