<?php

namespace App\Http\Controllers;

use App\Models\Kapal;
use App\Models\Patroli;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function ensureAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola user.');
        }
    }

    private function ensureAdminOrPimpinan()
    {
        if (! in_array(auth()->user()->role, ['admin', 'pimpinan'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman user.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrPimpinan();

        $sort = $request->get('sort', 'asc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'asc';

        $role = $request->get('role');
        $status = $request->get('status');
        $search = trim($request->get('search', ''));

        $query = User::with('kapal');

        if ($role) {
            $query->where('role', $role);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nrp', 'like', '%' . $search . '%')
                    ->orWhere('pangkat', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('kode_kapal', 'like', '%' . $search . '%')
                            ->orWhere('zona_patroli', 'like', '%' . $search . '%')
                            ->orWhere('wilayah_patroli', 'like', '%' . $search . '%')
                            ->orWhere('kelompok', 'like', '%' . $search . '%');
                    });
            });
        }

        $users = $query
            ->orderBy('nama', $sort)
            ->paginate(10)
            ->withQueryString();

        $userBertugasIds = $this->getUserBertugasIds();

        return view('users.index', compact(
            'users',
            'userBertugasIds',
            'sort',
            'role',
            'status',
            'search'
        ));
    }

    public function exportPdf(Request $request)
    {
        $this->ensureAdmin();

        $sort = $request->get('sort', 'asc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'asc';

        $role = $request->get('role');
        $status = $request->get('status');
        $search = trim($request->get('search', ''));

        $query = User::with('kapal');

        if ($role) {
            $query->where('role', $role);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nrp', 'like', '%' . $search . '%')
                    ->orWhere('pangkat', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('kode_kapal', 'like', '%' . $search . '%')
                            ->orWhere('zona_patroli', 'like', '%' . $search . '%')
                            ->orWhere('wilayah_patroli', 'like', '%' . $search . '%')
                            ->orWhere('kelompok', 'like', '%' . $search . '%');
                    });
            });
        }

        $users = $query
            ->orderBy('nama', $sort)
            ->get();

        $userBertugasIds = $this->getUserBertugasIds();

        $pdf = Pdf::loadView('users.export-pdf', [
            'users' => $users,
            'userBertugasIds' => $userBertugasIds,
            'role' => $role,
            'status' => $status,
            'sort' => $sort,
            'search' => $search,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('manajemen-user-polairud-' . now()->format('Ymd_His') . '.pdf');
    }

    private function getUserBertugasIds()
    {
        return Patroli::whereIn('status', ['diproses', 'berjalan', 'selesai', 'perbaiki'])
            ->with(['personels', 'kapal.komandan'])
            ->get()
            ->flatMap(function ($patroli) {
                $ids = $patroli->personels->pluck('id');

                if ($patroli->kapal && $patroli->kapal->komandan_id) {
                    $ids->push($patroli->kapal->komandan_id);
                }

                return $ids;
            })
            ->unique()
            ->values();
    }

    public function create()
    {
        $this->ensureAdmin();

        $kapals = Kapal::orderBy('kode_kapal')->get();

        return view('users.create', compact('kapals'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:50', 'unique:users,nrp'],
            'pangkat' => ['nullable', 'string', 'max:100'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'role' => ['required', Rule::in(['admin', 'pimpinan', 'komandan', 'abk'])],
            'kapal_id' => ['nullable', 'exists:kapals,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['required', 'string', 'min:6'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();

        $kapals = Kapal::orderBy('kode_kapal')->get();

        return view('users.edit', compact('user', 'kapals'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nrp')->ignore($user->id),
            ],
            'pangkat' => ['nullable', 'string', 'max:100'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'role' => ['required', Rule::in(['admin', 'pimpinan', 'komandan', 'abk'])],
            'kapal_id' => ['nullable', 'exists:kapals,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', 'string', 'min:6'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Admin tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Admin tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        $user->update([
            'status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $this->ensureAdmin();

        $passwordBaru = match ($user->role) {
            'admin' => 'ADM' . substr($user->nrp, -4) . '#',
            'pimpinan' => 'PMP' . substr($user->nrp, -4) . '#',
            default => 'Patroliairud' . substr($user->nrp, -4) . '#',
        };

        $user->update([
            'password' => Hash::make($passwordBaru),
        ]);

        return back()->with(
            'success',
            'Password berhasil direset menjadi: ' . $passwordBaru
        );
    }
}