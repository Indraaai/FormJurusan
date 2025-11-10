<?php

namespace App\Services\Forms;

use App\Models\Form;
use App\Models\FormSection;

class SectionNavigator
{
    public function getByPosition(Form $form, int $pos): ?FormSection
    {
        $form->loadMissing(['sections' => fn($q) => $q->orderBy('position')]);
        return $form->sections->firstWhere('position', $pos);
    }

    public function next(Form $form, FormSection $current): ?FormSection
    {
        // TODO: nanti evaluasi branching di sini
        return $form->sections->firstWhere('position', $current->position + 1);
    }

    public function previous(Form $form, FormSection $current): ?FormSection
    {
        return $form->sections->firstWhere('position', max(1, $current->position - 1));
    }
}
