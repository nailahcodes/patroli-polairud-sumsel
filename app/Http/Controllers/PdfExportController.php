<?php

namespace App\Http\Controllers;

use App\Models\Patroli;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfExportController extends Controller
{
    public function periode(Patroli $patroli)
    {
        $patroli->load([
            'kapal',
            'personels',
            'sopProgress.sop',
            'kronologis',
            'dokumens',
            'fotos',
            'anggarans',
            'logistiks',
        ]);

        $pdf = Pdf::loadView('pdf.periode', compact('patroli'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-periode-patroli-' . $patroli->id . '.pdf');
    }

    public function harian(Request $request, Patroli $patroli)
    {
        $tanggal = $request->tanggal ?? $patroli->tanggal_mulai;

        $patroli->load([
            'kapal',
            'personels',
            'sopProgress.sop',
            'kronologis' => function ($query) use ($tanggal) {
                $query->whereDate('tanggal', $tanggal)
                    ->orderBy('jam_wib');
            },
        ]);

        $pdf = Pdf::loadView('pdf.harian', compact('patroli', 'tanggal'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-harian-patroli-' . $patroli->id . '-' . $tanggal . '.pdf');
    }
}