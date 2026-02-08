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

        // BUG-017 FIX: Move date filtering to SQL and use pagination
        $forms = Form::query()
            ->where('is_published', true)
            ->when($q, fn($qq) => $qq->where('title', 'like', "%$q%"))
            ->where(function ($query) {
                // Filter start_at/end_at di SQL via settings relation
                $query->whereDoesntHave('settings', function ($sq) {
                    $sq->whereNotNull('start_at')
                        ->where('start_at', '>', now());
                })
                    ->whereDoesntHave('settings', function ($sq) {
                        $sq->whereNotNull('end_at')
                            ->where('end_at', '<', now());
                    });
            })
            ->with('settings')
            ->withCount([
                'responses as my_submitted_count' => fn($x) => $x->where('respondent_user_id', $user->id)->where('status', 'submitted'),
                'responses as my_draft_count'     => fn($x) => $x->where('respondent_user_id', $user->id)->where('status', 'draft'),
            ])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('respondent.forms.index', compact('forms'));
    }
}
