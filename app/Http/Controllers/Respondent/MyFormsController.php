<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyFormsController extends Controller
{
    public function index(Request $r)
    {
        $user = $r->user();
        $q    = trim((string)$r->query('q', ''));

        $forms = \App\Models\Form::query()
            ->where('is_published', true)
            ->when($q, fn($qq) => $qq->where('title', 'like', "%$q%"))
            ->with('settings')
            ->withCount([
                'responses as my_submitted_count' => fn($x) => $x->where('respondent_user_id', $user->id)->where('status', 'submitted'),
                'responses as my_draft_count'     => fn($x) => $x->where('respondent_user_id', $user->id)->where('status', 'draft'),
            ])
            ->latest('id')
            ->get()
            ->filter(function ($form) {
                $s = $form->settings;
                $start = $s?->start_at ? \Carbon\Carbon::parse($s->start_at) : null;
                $end   = $s?->end_at   ? \Carbon\Carbon::parse($s->end_at)   : null;

                if ($start && now()->lt($start)) return false;
                if ($end   && now()->gt($end))   return false;
                return true;
            });

        return view('respondent.forms.index', compact('forms'));
    }
}
