<?php

namespace App\Http\Controllers;

use App\Models\Kapal;
use App\Models\Patroli;
use App\Models\Sop;
use App\Models\SopProgress;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if ($user->role === 'pimpinan') {
            $totalKapal = Kapal::count();
            $totalSop = Sop::count();
            $totalPatroli = Patroli::count();

            $patroliTerbaru = Patroli::with(['kapal', 'validatorPimpinan'])
                ->latest()
                ->take(8)
                ->get();

            return view('dashboard-pimpinan', compact(
                'totalKapal',
                'totalSop',
                'totalPatroli',
                'patroliTerbaru'
            ));
        }

        if ($user->role === 'komandan') {

            $patrolisDitangani = Patroli::with([
                'kapal',
                'sopProgress.sop'
            ])
            ->latest()
            ->get();

            $patroliIds = $patrolisDitangani->pluck('id');

            $sopKomandan = SopProgress::with(['patroli.kapal', 'sop'])
                ->whereIn('patroli_id', $patroliIds)
                ->whereHas('sop', function ($query) {
                    $query->whereBetween('urutan', [4, 13]);
                })
                ->orderByDesc('updated_at')
                ->take(8)
                ->get();

            return view('dashboard-komandan', [
                'totalKapal' => Kapal::count(),
                'totalSop' => Sop::whereBetween('urutan', [4, 13])->count(),
                'totalPatroli' => Patroli::whereHas('kapal', function ($query) use ($user) {
                    $query->where('komandan_id', $user->id);
                })->count(),
                'riwayatPatroli' => $patrolisDitangani->take(8),
                'sopKomandan' => $sopKomandan,
            ]);
        }

        if ($user->role === 'abk') {

            $patrolisDitangani = Patroli::with([
                'kapal',
                'sopProgress.sop'
            ])
            ->latest()
            ->get();

            $patroliIds = $patrolisDitangani->pluck('id');

            $sopAbk = SopProgress::with(['patroli.kapal', 'sop'])
                ->whereIn('patroli_id', $patroliIds)
                ->whereHas('sop', function ($query) {
                    $query->whereBetween('urutan', [14, 16]);
                })
                ->orderByDesc('updated_at')
                ->take(8)
                ->get();

            $totalKapal = $patrolisDitangani
                ->pluck('kapal_id')
                ->filter()
                ->unique()
                ->count();

            return view('dashboard-abk', [
                'totalPatroli' => Patroli::whereHas('personels', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })->count(),
                'totalKapal' => $totalKapal,
                'totalSop' => Sop::whereBetween('urutan', [14, 16])->count(),
                'riwayatPatroli' => $patrolisDitangani->take(8),
                'sopAbk' => $sopAbk,
            ]);
        }

        $totalUser = User::count();
        $totalKapal = Kapal::count();
        $totalSop = Sop::count();
        $totalPatroli = Patroli::count();

        $patroliBerjalan = Patroli::where('status', 'berjalan')->count();
        $patroliSelesai = Patroli::where('status', 'selesai')->count();
        $patroliDraft = Patroli::where('status', 'draft')->count();

        /*
|--------------------------------------------------------------------------
| STATUS ARMADA KAPAL
|--------------------------------------------------------------------------
*/

        /* Kapal yang sedang digunakan patroli */
        $kapalSedangPatroli = Patroli::where('status', 'berjalan')
            ->distinct('kapal_id')
            ->count('kapal_id');

        /* Kapal yang sedang perawatan */
        $kapalPerawatan = Kapal::where('status', 'perawatan')
            ->count();

        /* Kapal docking */
        $kapalDocking = Kapal::where('status', '!=', 'perawatan')
            ->whereNotIn('id', function ($query) {
                $query->select('kapal_id')
                    ->from('patrolis')
                    ->where('status', 'berjalan');
            })
            ->count();

        $patroliTerbaru = Patroli::with(['kapal', 'sopProgress'])
            ->latest()
            ->take(6)
            ->get();

        $progressTerbaru = SopProgress::with(['patroli.kapal', 'sop'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalUser',
            'totalKapal',
            'totalSop',
            'totalPatroli',
            'patroliBerjalan',
            'patroliSelesai',
            'patroliDraft',
            'kapalSedangPatroli',
            'kapalDocking',
            'kapalPerawatan',
            'patroliTerbaru',
            'progressTerbaru'
        ));
    }
}