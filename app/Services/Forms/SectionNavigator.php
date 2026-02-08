<?php

namespace App\Services\Forms;

use App\Models\Form;
use App\Models\FormLogicRule;
use App\Models\FormResponse;
use App\Models\FormSection;
use App\Models\FormAnswer;

class SectionNavigator
{
    public function getByPosition(Form $form, int $pos): ?FormSection
    {
        $form->loadMissing(['sections' => fn($q) => $q->orderBy('position')]);
        return $form->sections->firstWhere('position', $pos);
    }

    /**
     * Determine next section based on branching logic rules.
     * Falls back to sequential (position + 1) if no rules match.
     */
    public function next(Form $form, FormSection $current, ?FormResponse $response = null): ?FormSection
    {
        // If we have a response, evaluate branching logic
        if ($response) {
            $targetSection = $this->evaluateBranching($form, $current, $response);
            if ($targetSection === 'submit') {
                return null; // Signal caller to go to review/submit
            }
            if ($targetSection instanceof FormSection) {
                return $targetSection;
            }
        }

        // Default: sequential next section
        return $form->sections->firstWhere('position', $current->position + 1);
    }

    public function previous(Form $form, FormSection $current): ?FormSection
    {
        return $form->sections->firstWhere('position', max(1, $current->position - 1));
    }

    /**
     * Evaluate branching logic rules for the current section's questions.
     *
     * @return FormSection|string|null  FormSection to jump to, 'submit' to end, or null for default
     */
    protected function evaluateBranching(Form $form, FormSection $current, FormResponse $response): FormSection|string|null
    {
        // Get enabled rules for questions in current section, ordered by priority
        $rules = FormLogicRule::where('form_id', $form->id)
            ->where('is_enabled', true)
            ->whereIn('source_question_id', $current->questions->pluck('id'))
            ->orderBy('priority')
            ->get();

        if ($rules->isEmpty()) {
            return null;
        }

        // Get answers for current section
        $answers = FormAnswer::where('response_id', $response->id)
            ->whereIn('question_id', $current->questions->pluck('id'))
            ->with('selectedOptions')
            ->get()
            ->keyBy('question_id');

        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $answers)) {
                if ($rule->action === 'submit') {
                    return 'submit';
                }
                if ($rule->action === 'goto_section' && $rule->target_section_id) {
                    return $form->sections->firstWhere('id', $rule->target_section_id);
                }
            }
        }

        return null;
    }

    /**
     * Check if a single logic rule matches the current answers.
     */
    protected function ruleMatches(FormLogicRule $rule, $answers): bool
    {
        $answer = $answers->get($rule->source_question_id);

        return match ($rule->operator) {
            'answered'     => $answer !== null && $this->hasValue($answer),
            'not_answered' => $answer === null || !$this->hasValue($answer),
            '='            => $this->compareEqual($rule, $answer),
            '!='           => !$this->compareEqual($rule, $answer),
            'contains'     => $this->compareContains($rule, $answer),
            'in'           => $this->compareIn($rule, $answer),
            '>='           => $this->compareNumeric($rule, $answer, '>='),
            '<='           => $this->compareNumeric($rule, $answer, '<='),
            'between'      => $this->compareBetween($rule, $answer),
            default        => false,
        };
    }

    protected function hasValue(?FormAnswer $answer): bool
    {
        if (!$answer) return false;

        return $answer->text_value !== null
            || $answer->long_text_value !== null
            || $answer->number_value !== null
            || $answer->date_value !== null
            || $answer->time_value !== null
            || $answer->option_id !== null
            || $answer->selectedOptions->isNotEmpty();
    }

    protected function compareEqual(FormLogicRule $rule, ?FormAnswer $answer): bool
    {
        if (!$answer) return false;

        // Compare by option_id
        if ($rule->option_id !== null) {
            // Check single option
            if ($answer->option_id == $rule->option_id) return true;
            // Check multiple options (checkboxes)
            return $answer->selectedOptions->contains('option_id', $rule->option_id);
        }

        // Compare by text
        if ($rule->value_text !== null) {
            $val = $answer->text_value ?? $answer->long_text_value ?? '';
            return mb_strtolower($val) === mb_strtolower($rule->value_text);
        }

        // Compare by number
        if ($rule->value_number !== null) {
            return (float) $answer->number_value === (float) $rule->value_number;
        }

        return false;
    }

    protected function compareContains(FormLogicRule $rule, ?FormAnswer $answer): bool
    {
        if (!$answer || $rule->value_text === null) return false;
        $val = $answer->text_value ?? $answer->long_text_value ?? '';
        return str_contains(mb_strtolower($val), mb_strtolower($rule->value_text));
    }

    protected function compareIn(FormLogicRule $rule, ?FormAnswer $answer): bool
    {
        if (!$answer || $rule->option_id === null) return false;
        // Check if the option is among selected options (for checkboxes)
        return $answer->selectedOptions->contains('option_id', $rule->option_id)
            || $answer->option_id == $rule->option_id;
    }

    protected function compareNumeric(FormLogicRule $rule, ?FormAnswer $answer, string $op): bool
    {
        if (!$answer || $rule->value_number === null || $answer->number_value === null) return false;
        return match ($op) {
            '>=' => (float) $answer->number_value >= (float) $rule->value_number,
            '<=' => (float) $answer->number_value <= (float) $rule->value_number,
            default => false,
        };
    }

    protected function compareBetween(FormLogicRule $rule, ?FormAnswer $answer): bool
    {
        if (!$answer || $answer->number_value === null) return false;
        // Use value_number as min and extras or value_text as max
        // Convention: value_number = min, value_text = max (as string number)
        $min = (float) $rule->value_number;
        $max = $rule->value_text !== null ? (float) $rule->value_text : null;
        if ($max === null) return false;
        $val = (float) $answer->number_value;
        return $val >= $min && $val <= $max;
    }
}
