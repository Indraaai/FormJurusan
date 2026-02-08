<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Form $form)
    {
        $sections = $form->sections()->withCount('questions')->get();
        return view('admin.sections.index', compact('form', 'sections'));
    }

    public function create(Form $form)
    {
        return view('admin.sections.create', compact('form'));
    }

    public function store(Request $request, Form $form)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $position = (int) ($form->sections()->max('position') ?? 0) + 1;

        $section = $form->sections()->create([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'position' => $position,
        ]);

        return redirect()->route('admin.forms.sections.index', $form)
            ->with('status', 'Section dibuat.');
    }

    public function show(FormSection $section)
    {
        // Opsional: tampilkan daftar pertanyaan pada section ini
        $section->load('form', 'questions');
        return view('admin.sections.show', compact('section'));
    }

    public function edit(FormSection $section)
    {
        $section->load('form');
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, FormSection $section)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'position' => ['nullable', 'integer', 'min:1'],
        ]);

        $section->update($data);

        return back()->with('status', 'Section diperbarui.');
    }

    public function destroy(FormSection $section)
    {
        $form = $section->form;
        $section->delete();
        return redirect()->route('admin.forms.sections.index', $form)
            ->with('status', 'Section dihapus.');
    }
}
