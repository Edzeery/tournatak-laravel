<?php

namespace App\Http\Controllers\Api;

use App\Models\Match_;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $matches = Match_::with(['competition', 'team1', 'team2', 'goals'])
            ->latest('match_date')
            ->paginate($request->input('per_page', 15));

        return response()->json($matches);
    }
}
