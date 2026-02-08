<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\FormResponse;
use App\Models\FormSection;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $respondent;
    private Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test admin and respondent
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->respondent = User::factory()->create(['role' => 'respondent']);

        // Create a test form
        $this->form = Form::factory()->create([
            'created_by_id' => $this->admin->id,
            'uid' => 'test-form-' . uniqid(),
        ]);

        // Create form settings
        $this->form->settings()->create([
            'require_sign_in' => true,
            'collect_emails' => true,
            'limit_one_response' => false,
        ]);
    }

    /**
     * Test form submission with required checkbox questions
     * This tests the bug fix for checkbox field names (q[id][] instead of q[id])
     */
    public function test_form_submission_with_checkbox_questions()
    {
        // Create a section with checkbox question
        $section = FormSection::factory()->create([
            'form_id' => $this->form->id,
            'position' => 1,
        ]);

        $checkboxQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'checkboxes',
            'required' => true,
            'position' => 1,
        ]);

        // Create options for checkbox
        $option1 = QuestionOption::factory()->create([
            'question_id' => $checkboxQuestion->id,
            'label' => 'Option 1',
            'position' => 1,
        ]);
        $option2 = QuestionOption::factory()->create([
            'question_id' => $checkboxQuestion->id,
            'label' => 'Option 2',
            'position' => 2,
        ]);

        // User starts form
        $this->actingAs($this->respondent)
            ->post(route('forms.begin', $this->form->uid))
            ->assertRedirect(route('forms.section', ['form' => $this->form->uid, 'pos' => 1]));

        // User fills in the checkbox section with multiple selections
        $response = $this->actingAs($this->respondent)
            ->post(
                route('forms.section.save', ['form' => $this->form->uid, 'pos' => 1]),
                [
                    "q[{$checkboxQuestion->id}][]" => [(string)$option1->id, (string)$option2->id],
                ]
            );

        $response->assertRedirect(route('forms.review', $this->form->uid));

        // Verify answers were saved
        $formResponse = FormResponse::where('form_id', $this->form->id)
            ->where('respondent_user_id', $this->respondent->id)
            ->first();

        $this->assertNotNull($formResponse);

        $answer = FormAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $checkboxQuestion->id)
            ->first();

        $this->assertNotNull($answer);

        // Verify selected options were saved
        $selectedOptions = $answer->selectedOptions()->pluck('option_id')->sort()->values()->all();
        $this->assertEquals(
            [(string)$option1->id, (string)$option2->id],
            array_map('strval', $selectedOptions)
        );

        // Now test submission - should NOT have validation errors
        $submitResponse = $this->actingAs($this->respondent)
            ->post(route('forms.submit', $this->form->uid));

        // Should succeed and redirect to done page
        $submitResponse->assertRedirect(route('forms.done', $this->form->uid));

        // Verify response status changed to submitted
        $formResponse->refresh();
        $this->assertEquals('submitted', $formResponse->status);
        $this->assertNotNull($formResponse->submitted_at);
    }

    /**
     * Test form submission with required text questions
     */
    public function test_form_submission_with_text_questions()
    {
        $section = FormSection::factory()->create([
            'form_id' => $this->form->id,
            'position' => 1,
        ]);

        $textQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'short_text',
            'required' => true,
            'position' => 1,
        ]);

        // User starts and fills form
        $this->actingAs($this->respondent)
            ->post(route('forms.begin', $this->form->uid));

        $this->actingAs($this->respondent)
            ->post(
                route('forms.section.save', ['form' => $this->form->uid, 'pos' => 1]),
                [
                    "q[{$textQuestion->id}]" => "Test Answer",
                ]
            )
            ->assertRedirect(route('forms.review', $this->form->uid));

        // Verify answer was saved
        $formResponse = FormResponse::where('form_id', $this->form->id)
            ->where('respondent_user_id', $this->respondent->id)
            ->first();

        $answer = FormAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $textQuestion->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertEquals("Test Answer", $answer->text_value);

        // Submit should succeed
        $this->actingAs($this->respondent)
            ->post(route('forms.submit', $this->form->uid))
            ->assertRedirect(route('forms.done', $this->form->uid));
    }

    /**
     * Test form submission fails when required checkbox is not selected
     */
    public function test_form_submission_fails_with_empty_required_checkbox()
    {
        $section = FormSection::factory()->create([
            'form_id' => $this->form->id,
            'position' => 1,
        ]);

        $checkboxQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'checkboxes',
            'required' => true,
            'position' => 1,
        ]);

        QuestionOption::factory()->create([
            'question_id' => $checkboxQuestion->id,
            'position' => 1,
        ]);

        // User starts form
        $this->actingAs($this->respondent)
            ->post(route('forms.begin', $this->form->uid));

        // User submits without selecting any checkboxes
        $submitResponse = $this->actingAs($this->respondent)
            ->post(route('forms.submit', $this->form->uid));

        // Should fail validation
        $submitResponse->assertSessionHasErrors();
        $errors = session('errors')->getMessages();
        $this->assertArrayHasKey('required_questions', $errors);
    }

    /**
     * Test form submission with mixed question types
     */
    public function test_form_submission_with_mixed_question_types()
    {
        $section = FormSection::factory()->create([
            'form_id' => $this->form->id,
            'position' => 1,
        ]);

        // Create multiple question types
        $textQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'short_text',
            'required' => true,
            'position' => 1,
        ]);

        $dropdownQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'dropdown',
            'required' => true,
            'position' => 2,
        ]);

        $dropdownOption = QuestionOption::factory()->create([
            'question_id' => $dropdownQuestion->id,
            'position' => 1,
        ]);

        $checkboxQuestion = Question::factory()->create([
            'section_id' => $section->id,
            'type' => 'checkboxes',
            'required' => true,
            'position' => 3,
        ]);

        $checkboxOption1 = QuestionOption::factory()->create([
            'question_id' => $checkboxQuestion->id,
            'position' => 1,
        ]);
        $checkboxOption2 = QuestionOption::factory()->create([
            'question_id' => $checkboxQuestion->id,
            'position' => 2,
        ]);

        // User starts form
        $this->actingAs($this->respondent)
            ->post(route('forms.begin', $this->form->uid));

        // Fill in all questions
        $this->actingAs($this->respondent)
            ->post(
                route('forms.section.save', ['form' => $this->form->uid, 'pos' => 1]),
                [
                    "q[{$textQuestion->id}]" => "Text Answer",
                    "q[{$dropdownQuestion->id}]" => $dropdownOption->id,
                    "q[{$checkboxQuestion->id}][]" => [
                        (string)$checkboxOption1->id,
                        (string)$checkboxOption2->id,
                    ],
                ]
            )
            ->assertRedirect(route('forms.review', $this->form->uid));

        // Verify all answers were saved
        $formResponse = FormResponse::where('form_id', $this->form->id)
            ->where('respondent_user_id', $this->respondent->id)
            ->first();

        $this->assertCount(3, $formResponse->answers);

        // Submit should succeed
        $this->actingAs($this->respondent)
            ->post(route('forms.submit', $this->form->uid))
            ->assertRedirect(route('forms.done', $this->form->uid));

        // Verify response is submitted
        $formResponse->refresh();
        $this->assertEquals('submitted', $formResponse->status);
    }
}
