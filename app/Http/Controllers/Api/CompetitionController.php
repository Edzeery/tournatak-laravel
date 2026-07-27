<?php

namespace App\Http\Controllers\Api;

use App\Models\Competition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $competitions = Competition::where('approval_status', 'approved')
            ->with(['type', 'subtype', 'organizer'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($competitions);
    }
}
