<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSetting;
use App\Repositories\Contracts\FormRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FormController extends Controller
{
    private FormRepositoryInterface $formRepository;

    public function __construct(FormRepositoryInterface $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function index()
    {
        $startTime = microtime(true);

        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'per_page' => 12
        ];

        $forms = $this->formRepository->getAllWithPagination($filters);

        $executionTime = microtime(true) - $startTime;
        Log::channel('performance')->info('Form list loaded', [
            'execution_time' => $executionTime,
            'filters' => $filters,
            'results_count' => $forms->count(),
            'user_id' => auth()->id(),
        ]);

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $formData = [
            'uid' => (string) Str::ulid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'created_by' => Auth::id(),
            'is_published' => false,
        ];

        $form = $this->formRepository->create($formData);

        // Buat settings default
        FormSetting::create([
            'form_id' => $form->id,
            'require_sign_in' => true,
            'collect_emails' => true,
            'show_progress_bar' => true,
        ]);

        return redirect()->route('admin.forms.edit', $form)
            ->with('status', 'Form berhasil dibuat.');
    }

    public function show(Form $form)
    {
        $relations = [
            'sections' => function ($query) {
                $query->orderBy('position')->with('questions');
            },
            'responses' => function ($query) {
                $query->latest()->with(['respondent:id,name', 'answers.question:id,title']);
            }
        ];

        $form = $this->formRepository->findWithRelations($form->id, $relations);

        return view('admin.forms.show', compact('form'));
    }

    public function edit(Form $form)
    {
        $form = $this->formRepository->findWithRelations($form->id, ['settings']);
        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $updateData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_published' => (bool) ($data['is_published'] ?? $form->is_published),
        ];

        $this->formRepository->update($form, $updateData);

        return back()->with('status', 'Form diperbarui.');
    }

    public function destroy(Form $form)
    {
        $this->formRepository->delete($form);
        return redirect()->route('admin.forms.index')->with('status', 'Form dihapus.');
    }
    // app/Http/Controllers/Admin/FormController.php
    public function publish(\App\Models\Form $form)
    {
        $form->forceFill(['is_published' => true])->save();

        $settings = $form->settings()->firstOrCreate();
        if (empty($settings->start_at)) $settings->start_at = now();
        // Optional: pastikan tidak langsung ketutup
        $settings->end_at = null;
        $settings->save();

        Log::channel('forms')->info('Form published', [
            'form_id' => $form->id,
            'title' => $form->title,
            'published_by' => auth()->id(),
        ]);

        return back()->with('status', 'Form published & activated.');
    }

    public function unpublish(Form $form)
    {
        $form->forceFill(['is_published' => false])->save();

        Log::channel('forms')->warning('Form unpublished', [
            'form_id' => $form->id,
            'title' => $form->title,
            'unpublished_by' => auth()->id(),
        ]);

        return back()->with('status', 'Form unpublished.');
    }
}
