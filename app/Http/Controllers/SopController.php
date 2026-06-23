<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use Illuminate\Http\Request;

class SopController extends Controller
{
    private function ensureAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola SOP.');
        }
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'asc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'asc';

        $pelaksana = $request->get('pelaksana');

        $query = Sop::query();

        if ($pelaksana) {
            $query->where('pelaksana', 'like', '%' . $pelaksana . '%');
        }

        $sops = $query
            ->orderBy('urutan', $sort)
            ->paginate(16)
            ->withQueryString();

        $pelaksanaList = Sop::select('pelaksana')
            ->whereNotNull('pelaksana')
            ->distinct()
            ->orderBy('pelaksana')
            ->pluck('pelaksana');

        return view('sop.index', compact('sops', 'pelaksanaList', 'sort', 'pelaksana'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('sop.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'urutan' => ['required', 'integer', 'min:1'],
            'tahapan' => ['required', 'string', 'max:255'],
            'pelaksana' => ['nullable', 'string', 'max:255'],
            'waktu' => ['nullable', 'string', 'max:255'],
            'kelengkapan' => ['nullable', 'string'],
            'output' => ['nullable', 'string', 'max:255'],
        ]);

        Sop::create($validated);

        return redirect()
            ->route('sop.index')
            ->with('success', 'Data SOP berhasil ditambahkan.');
    }

    public function edit(Sop $sop)
    {
        $this->ensureAdmin();

        return view('sop.edit', compact('sop'));
    }

    public function update(Request $request, Sop $sop)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'urutan' => ['required', 'integer', 'min:1'],
            'tahapan' => ['required', 'string', 'max:255'],
            'pelaksana' => ['nullable', 'string', 'max:255'],
            'waktu' => ['nullable', 'string', 'max:255'],
            'kelengkapan' => ['nullable', 'string'],
            'output' => ['nullable', 'string', 'max:255'],
        ]);

        $sop->update($validated);

        return redirect()
            ->route('sop.index')
            ->with('success', 'Data SOP berhasil diperbarui.');
    }

    public function destroy(Sop $sop)
    {
        $this->ensureAdmin();

        $sop->delete();

        return redirect()
            ->route('sop.index')
            ->with('success', 'Data SOP berhasil dihapus.');
    }
}