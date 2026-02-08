<?php

namespace App\Http\Requests;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\FormResponse;
use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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
            $this->validateResponseStatus($validator);
            $this->validateRequiredQuestions($validator);
        });
    }

    /**
     * Validate response is in draft status and can be submitted
     */
    protected function validateResponseStatus($validator): void
    {
        $form = $this->route('form');
        $user = $this->user();

        // BUG-004 FIX: Explicitly filter for draft status only
        $response = FormResponse::where('form_id', $form->id)
            ->where('respondent_user_id', $user->id)
            ->where('status', 'draft')
            ->latest('id')
            ->first();

        if (!$response) {
            // Check if already submitted to give better error message
            $hasSubmitted = FormResponse::where('form_id', $form->id)
                ->where('respondent_user_id', $user->id)
                ->where('status', 'submitted')
                ->exists();

            if ($hasSubmitted) {
                $validator->errors()->add('response', 'Respons ini sudah dikirim sebelumnya.');
            } else {
                $validator->errors()->add('response', 'Draft response tidak ditemukan. Silakan mulai mengisi form terlebih dahulu.');
            }
            return;
        }

        // Store response in request for controller to use
        $this->merge(['_validated_response' => $response]);
    }

    /**
     * Validate that all required questions have been answered.
     */
    protected function validateRequiredQuestions($validator): void
    {
        $form = $this->route('form');
        $user = $this->user();

        // Get response from previous validation
        $response = $this->get('_validated_response');

        if (!$response) {
            return; // Already handled by validateResponseStatus
        }

        // Get all required questions for this form
        // Questions are required if:
        // 1. question.required = true, OR
        // 2. question has a 'required' validation rule
        $requiredQuestions = Question::query()
            ->whereHas('section', function ($q) use ($form) {
                $q->where('form_id', $form->id);
            })
            ->where(function ($q) {
                $q->where('required', true)
                    ->orWhereHas('validations', function ($subQ) {
                        $subQ->where('validation_type', 'required');
                    });
            })
            ->select('id', 'title')
            ->get()
            ->keyBy('id')
            ->pluck('title', 'id'); // Correct: key=id (numeric), value=title (string)

        if ($requiredQuestions->isEmpty()) {
            return; // No required questions
        }

        // BUG-013 FIX: Only log sensitive data in local environment
        if (app()->environment('local')) {
            Log::debug('Required questions for form submission', [
                'form_id' => $form->id,
                'user_id' => $user->id,
                'response_id' => $response->id,
                'required_question_ids' => $requiredQuestions->keys()->toArray(),
                'required_question_titles' => $requiredQuestions->values()->toArray(),
            ]);
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
                        // Check for checkbox selections
                        $subQ->select(DB::raw(1))
                            ->from('form_answer_options')
                            ->whereColumn('form_answer_options.answer_id', 'form_answers.id');
                    })
                    ->orWhereExists(function ($subQ) {
                        // Check for file uploads
                        $subQ->select(DB::raw(1))
                            ->from('media_assets')
                            ->whereColumn('media_assets.attached_id', 'form_answers.id')
                            ->where('media_assets.attached_type', '=', 'answer');
                    });
            })
            ->pluck('question_id')
            ->all();

        // BUG-013 FIX: Only log sensitive data in local environment
        if (app()->environment('local')) {
            Log::debug('Answered questions check', [
                'response_id' => $response->id,
                'answered_question_ids' => $answeredQuestions,
                'total_answers_in_response' => FormAnswer::where('response_id', $response->id)->count(),
            ]);
        }

        // BUG-013 FIX: Only log sensitive data in local environment
        if (app()->environment('local')) {
            $allAnswers = FormAnswer::where('response_id', $response->id)
                ->with('selectedOptions')
                ->select('id', 'question_id', 'text_value', 'long_text_value', 'number_value', 'option_id')
                ->get();
            Log::debug('All answers in response', [
                'answers' => $allAnswers->toArray(),
            ]);
        }

        // Find unanswered required questions
        $unansweredIds = $requiredQuestions->keys()->diff($answeredQuestions);

        if ($unansweredIds->isNotEmpty()) {
            $unansweredTitles = $requiredQuestions->only($unansweredIds)->values();

            if (app()->environment('local')) {
                Log::warning('Unanswered required questions - DETAILED DEBUG', [
                    'required_questions_collection' => $requiredQuestions->toArray(),
                    'unanswered_ids_array' => $unansweredIds->toArray(),
                    'unanswered_titles' => $unansweredTitles->toArray(),
                    'unanswered_titles_implode' => $unansweredTitles->take(3)->implode(', '),
                ]);
            }

            // Build error message with fallback to IDs if titles are empty
            $titlesList = $unansweredTitles->take(3)->implode(', ');
            if (empty($titlesList)) {
                $titlesList = $unansweredIds->take(3)->implode(', ');
            }

            $validator->errors()->add(
                'required_questions',
                'Masih ada ' . $unansweredIds->count() . ' pertanyaan wajib yang belum dijawab: ' .
                    $titlesList .
                    ($unansweredIds->count() > 3 ? ' dan lainnya.' : '')
            );
        }
    }
}
