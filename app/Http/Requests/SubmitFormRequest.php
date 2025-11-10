<?php

namespace App\Http\Requests;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\FormResponse;
use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SubmitFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return []; // Main validation in withValidator
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateRequiredQuestions($validator);
        });
    }

    /**
     * Validate that all required questions have been answered.
     */
    protected function validateRequiredQuestions($validator): void
    {
        $form = $this->route('form');
        $user = $this->user();

        // Get the draft response
        $response = FormResponse::where('form_id', $form->id)
            ->where('respondent_user_id', $user->id)
            ->where('status', '!=', 'submitted')
            ->latest('id')
            ->first();

        if (!$response) {
            $validator->errors()->add(
                'response',
                'Draft response tidak ditemukan.'
            );
            return;
        }

        // Get all required questions for this form
        $requiredQuestions = Question::query()
            ->whereHas('section', function ($q) use ($form) {
                $q->where('form_id', $form->id);
            })
            ->where('required', true)
            ->pluck('id', 'title');

        if ($requiredQuestions->isEmpty()) {
            return; // No required questions
        }

        // Get all answered questions (with non-null values)
        $answeredQuestions = FormAnswer::where('response_id', $response->id)
            ->whereIn('question_id', $requiredQuestions->keys())
            ->where(function ($q) {
                $q->whereNotNull('text_value')
                    ->orWhereNotNull('long_text_value')
                    ->orWhereNotNull('number_value')
                    ->orWhereNotNull('date_value')
                    ->orWhereNotNull('time_value')
                    ->orWhereNotNull('option_id')
                    ->orWhereExists(function ($subQ) {
                        $subQ->select(DB::raw(1))
                            ->from('form_answer_options')
                            ->whereColumn('form_answer_options.answer_id', 'form_answers.id');
                    });
            })
            ->pluck('question_id');

        // Find unanswered required questions
        $unansweredIds = $requiredQuestions->keys()->diff($answeredQuestions);

        if ($unansweredIds->isNotEmpty()) {
            $unansweredTitles = $requiredQuestions->only($unansweredIds)->values();

            $validator->errors()->add(
                'required_questions',
                'Masih ada ' . $unansweredIds->count() . ' pertanyaan wajib yang belum dijawab: ' .
                    $unansweredTitles->take(3)->implode(', ') .
                    ($unansweredIds->count() > 3 ? ' dan lainnya.' : '')
            );
        }
    }
}
