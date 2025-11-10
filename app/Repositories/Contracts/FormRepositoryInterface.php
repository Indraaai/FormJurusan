<?php

namespace App\Repositories\Contracts;

use App\Models\Form;
use Illuminate\Pagination\LengthAwarePaginator;

interface FormRepositoryInterface
{
    public function getAllWithPagination(array $filters = []): LengthAwarePaginator;
    public function findWithRelations(int $id, array $relations = []): Form;
    public function create(array $data): Form;
    public function update(Form $form, array $data): Form;
    public function delete(Form $form): bool;
    public function getPublishedForms(): \Illuminate\Database\Eloquent\Collection;
}