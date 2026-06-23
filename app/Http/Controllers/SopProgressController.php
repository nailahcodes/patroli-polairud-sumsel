<?php

namespace App\Http\Controllers;

use App\Models\SopProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SopProgressController extends Controller
{
    private array $sarprasWajib = [
        'kapal',
        'ht',
        'rig',
        'gps',
        'lampu_sorot',
        'lampu_navigasi',
        'radar',
        'kompas',
        'echo_sounder',
        'life_jacket',
        'ring_buoy',
        'racun_api',
        'kotak_p3k',
        'tali_tambang',
        'dapra',
        'air_tawar',
        'alat_masak_ransum',
        'senter',
        'tool_kit',
        'bbm',
        'senpi_amunisi',
        'borgol',
        'anggaran',
    ];

    public function update(Request $request, $id)
    {
        $progress = SopProgress::with([
            'patroli.kapal.komandan',
            'patroli.abkLaporan.riksaKapals',
            'patroli.abkAnev',
            'patroli.sopProgress.sop',
            'patroli.personels',
            'sop',
        ])->findOrFail($id);

        $user = auth()->user();
        $urutan = (int) $progress->sop->urutan;

        $this->authorizeSopUpdate($user, $progress, $urutan);

        if ($urutan === 4 && $user->role === 'komandan') {
            return $this->updateSop4($request, $progress, $user);
        }

        if ($urutan === 5 && $user->role === 'komandan') {
            return $this->updateSop5($request, $progress, $user);
        }

        if ($urutan === 6 && $user->role === 'komandan') {
            return $this->updateSop6($request, $progress, $user);
        }

        if ($urutan === 11 && $user->role === 'komandan') {
            return $this->updateSop11($request, $progress, $user);
        }

        if ($urutan === 15 && $user->role === 'abk') {
            return back()->with('error', 'SOP 15 diselesaikan melalui menu Isi ANEV, bukan checklist langsung.');
        }

        $request->validate([
            'selesai' => ['nullable'],
        ]);

        DB::transaction(function () use ($progress, $request, $user) {
            $isChecked = $request->has('selesai');

            $progress->update([
                'status' => $isChecked ? 'selesai' : 'belum',
                'waktu_mulai' => $isChecked ? ($progress->waktu_mulai ?? now()) : null,
                'waktu_selesai' => $isChecked ? now() : null,
                'user_id' => $user->id,
            ]);

            $this->updateStatusPatroliOtomatis($progress);
        });

        return back()->with('success', 'Status SOP berhasil diperbarui.');
    }

    private function updateSop4(Request $request, SopProgress $progress, $user)
    {
        $request->validate([
            'bukti_file' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'bukti_file_2' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'bukti_file.required' => 'Scan Surat Perintah wajib diupload.',
            'bukti_file_2.required' => 'File Berita Acara wajib diupload.',
            'bukti_file.mimes' => 'Surat Perintah harus PDF.',
            'bukti_file_2.mimes' => 'Berita Acara harus PDF.',
            'bukti_file.max' => 'Surat Perintah maksimal 5MB.',
            'bukti_file_2.max' => 'Berita Acara maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $progress, $user) {
            if ($progress->bukti_file) {
                Storage::disk('public')->delete($progress->bukti_file);
            }

            if ($progress->bukti_file_2) {
                Storage::disk('public')->delete($progress->bukti_file_2);
            }

            $surat = $request->file('bukti_file')->store('sop4/surat-perintah', 'public');
            $berita = $request->file('bukti_file_2')->store('sop4/berita-acara', 'public');

            $progress->update([
                'bukti_file' => $surat,
                'bukti_file_2' => $berita,
                'status' => 'selesai',
                'waktu_mulai' => $progress->waktu_mulai ?? now(),
                'waktu_selesai' => now(),
                'user_id' => $user->id,
            ]);

            $this->updateStatusPatroliOtomatis($progress);
        });

        return back()->with('success', 'SOP 4 selesai. Surat Perintah dan Berita Acara berhasil diupload.');
    }

    private function updateSop5(Request $request, SopProgress $progress, $user)
    {
        if (! $this->sopUrutanSelesai($progress, 4)) {
            return back()->with('error', 'SOP 5 belum bisa diselesaikan. SOP 4 harus selesai terlebih dahulu.');
        }

        $request->validate([
            'sarpras' => ['required', 'array'],
            'air_tawar_file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'air_tawar_file.required' => 'Bukti foto pengisian air tawar wajib diupload.',
            'air_tawar_file.image' => 'Foto air tawar harus berupa gambar.',
            'air_tawar_file.mimes' => 'Foto air tawar harus JPG, JPEG, atau PNG.',
            'air_tawar_file.max' => 'Foto air tawar maksimal 5MB.',
        ]);

        $sarpras = $request->input('sarpras', []);
        $kurang = array_diff($this->sarprasWajib, array_keys($sarpras));

        if (count($kurang) > 0) {
            $progress->update([
                'checklist_sarpras' => array_keys($sarpras),
                'status' => 'belum',
                'waktu_selesai' => null,
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'SOP 5 belum bisa diselesaikan. Semua sarpras wajib harus dicentang.');
        }

        DB::transaction(function () use ($request, $progress, $sarpras, $user) {
            if ($progress->air_tawar_file) {
                Storage::disk('public')->delete($progress->air_tawar_file);
            }

            $airTawar = $request->file('air_tawar_file')->store('sop5/air-tawar', 'public');

            $progress->update([
                'checklist_sarpras' => array_keys($sarpras),
                'air_tawar_file' => $airTawar,
                'status' => 'selesai',
                'waktu_mulai' => $progress->waktu_mulai ?? now(),
                'waktu_selesai' => now(),
                'user_id' => $user->id,
            ]);

            $this->updateStatusPatroliOtomatis($progress);
        });

        return back()->with('success', 'SOP 5 selesai. Sarpras lengkap dan bukti air tawar sudah diupload.');
    }

    private function updateSop6(Request $request, SopProgress $progress, $user)
    {
        $request->validate([
            'bukti_file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'bukti_file.required' => 'Bukti APP wajib diupload.',
            'bukti_file.image' => 'Bukti APP harus berupa gambar.',
            'bukti_file.mimes' => 'Bukti APP harus JPG, JPEG, atau PNG.',
            'bukti_file.max' => 'Bukti APP maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $progress, $user) {
            if ($progress->bukti_file) {
                Storage::disk('public')->delete($progress->bukti_file);
            }

            $path = $request->file('bukti_file')->store('sop6/bukti-app', 'public');

            $progress->update([
                'bukti_file' => $path,
                'status' => 'selesai',
                'waktu_mulai' => $progress->waktu_mulai ?? now(),
                'waktu_selesai' => now(),
                'user_id' => $user->id,
            ]);

            $this->updateStatusPatroliOtomatis($progress);
        });

        return back()->with('success', 'SOP 6 selesai. Bukti APP berhasil diupload.');
    }

    private function updateSop11(Request $request, SopProgress $progress, $user)
    {
        $laporan = $progress->patroli->abkLaporan;

        $tindakPidana = $laporan
            ? $laporan->riksaKapals()->where('kategori', 'tindak_pidana')->count()
            : 0;

        if ($tindakPidana > 0) {
            $request->validate([
                'selesai' => ['required'],
            ], [
                'selesai.required' => 'Centang jika gelar perkara sudah dilaksanakan.',
            ]);

            $nihil = false;
        } else {
            $request->validate([
                'nihil_gelar_perkara' => ['required'],
            ], [
                'nihil_gelar_perkara.required' => 'Centang nihil jika tidak ada tindak pidana.',
            ]);

            $nihil = true;
        }

        DB::transaction(function () use ($progress, $user, $nihil) {
            $progress->update([
                'status' => 'selesai',
                'nihil_gelar_perkara' => $nihil,
                'waktu_mulai' => $progress->waktu_mulai ?? now(),
                'waktu_selesai' => now(),
                'user_id' => $user->id,
            ]);

            $this->updateStatusPatroliOtomatis($progress);
        });

        return back()->with('success', 'SOP 11 berhasil diselesaikan.');
    }

    private function authorizeSopUpdate($user, SopProgress $progress, int $urutan): void
    {
        if ($user->role === 'admin') {
            if ($urutan >= 1 && $urutan <= 3) {
                return;
            }

            abort(403, 'Admin hanya dapat mencentang SOP nomor 1 sampai 3.');
        }

        if ($user->role === 'komandan') {
            if (optional($progress->patroli->kapal)->komandan_id !== $user->id) {
                abort(403, 'Komandan hanya dapat mengubah progress SOP pada kapal yang ditugaskan kepadanya.');
            }

            if (! $this->rangeSopSelesai($progress, 1, 3)) {
                abort(403, 'Komandan belum bisa melanjutkan SOP. Admin harus menyelesaikan SOP nomor 1 sampai 3 terlebih dahulu.');
            }

            if ($urutan >= 5 && $urutan <= 13 && ! $this->sopUrutanSelesai($progress, 4)) {
                abort(403, 'SOP 5-13 belum aktif. SOP 4 harus selesai terlebih dahulu.');
            }

            if ($urutan === 11 && ! $this->sopUrutanSelesai($progress, 10)) {
                abort(403, 'SOP 11 baru dapat diproses setelah SOP 10 selesai.');
            }

            if ($urutan >= 4 && $urutan <= 13) {
                return;
            }

            abort(403, 'Komandan hanya dapat mencentang SOP nomor 4 sampai 13.');
        }

        if ($user->role === 'abk') {
            $isPersonel = $progress->patroli
                ->personels()
                ->where('users.id', $user->id)
                ->exists();

            if (! $isPersonel) {
                abort(403, 'ABK hanya dapat mengubah SOP pada patroli yang ditugaskan.');
            }

            if ($urutan === 14) {
                return;
            }

            if ($urutan === 15) {
                if (! $this->sopUrutanSelesai($progress, 14)) {
                    abort(403, 'ANEV baru bisa diisi setelah SOP 14 selesai.');
                }

                return;
            }

            if ($urutan === 16) {
                if (! $this->rangeSopSelesai($progress, 1, 15)) {
                    abort(403, 'SOP 16 menunggu SOP 1 sampai 15 selesai.');
                }

                return;
            }

            abort(403, 'ABK hanya dapat mengubah SOP 14 sampai 16.');
        }

        abort(403, 'Role ini hanya dapat melihat progress SOP.');
    }

    private function rangeSopSelesai(SopProgress $progress, int $awal, int $akhir): bool
    {
        $total = $progress->patroli
            ->sopProgress()
            ->whereHas('sop', fn ($q) => $q->whereBetween('urutan', [$awal, $akhir]))
            ->count();

        $selesai = $progress->patroli
            ->sopProgress()
            ->where('status', 'selesai')
            ->whereHas('sop', fn ($q) => $q->whereBetween('urutan', [$awal, $akhir]))
            ->count();

        return $total > 0 && $total === $selesai;
    }

    private function updateStatusPatroliOtomatis(SopProgress $progress): void
    {
        $patroli = $progress->patroli;

        if ($this->sopUrutanSelesai($progress, 13)) {
            $patroli->update(['status' => 'selesai']);
            return;
        }

        if ($this->sopUrutanSelesai($progress, 5)) {
            $patroli->update(['status' => 'berjalan']);
            return;
        }

        if ($this->sopUrutanSelesai($progress, 1)) {
            $patroli->update(['status' => 'diproses']);
            return;
        }

        $patroli->update(['status' => 'draft']);
    }

    private function sopUrutanSelesai(SopProgress $progress, int $urutan): bool
    {
        return $progress->patroli
            ->sopProgress()
            ->where('status', 'selesai')
            ->whereHas('sop', fn ($q) => $q->where('urutan', $urutan))
            ->exists();
    }
}