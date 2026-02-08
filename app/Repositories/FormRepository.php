<?php

namespace App\Repositories;

use App\Models\Form;
use App\Repositories\Contracts\FormRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FormRepository implements FormRepositoryInterface
{
    public function getAllWithPagination(array $filters = []): LengthAwarePaginator
    {
        $query = Form::query()
            ->select(['id', 'uid', 'title', 'description', 'is_published', 'created_at', 'created_by'])
            ->with(['creator:id,name'])
            ->withCount(['responses', 'questions']);

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('is_published', $filters['status'] === 'published');
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 12);
    }

    public function findWithRelations(int $id, array $relations = []): Form
    {
        return Form::with($relations)->findOrFail($id);
    }

    public function create(array $data): Form
    {
        $form = Form::create($data);

        Log::channel('forms')->info('Form created', [
            'form_id' => $form->id,
            'title' => $form->title,
            'created_by' => $form->created_by,
        ]);

        return $form;
    }

    public function update(Form $form, array $data): Form
    {
        $oldData = $form->only(['title', 'is_published']);
        $form->update($data);

        Log::channel('forms')->info('Form updated', [
            'form_id' => $form->id,
            'old_data' => $oldData,
            'new_data' => $data,
            'updated_by' => Auth::id(),
        ]);

        return $form->fresh();
    }

    public function delete(Form $form): bool
    {
        $formId = $form->id;
        $title = $form->title;
        $result = $form->delete();

        if ($result) {
            Log::channel('forms')->warning('Form deleted', [
                'form_id' => $formId,
                'title' => $title,
                'deleted_by' => Auth::id(),
            ]);
        }

        return $result;
    }

    public function getPublishedForms(): Collection
    {
        return Form::query()
            ->select(['id', 'uid', 'title', 'description', 'created_at'])
            ->where('is_published', true)
            ->with(['creator:id,name'])
            ->latest('created_at')
            ->get();
    }
}
