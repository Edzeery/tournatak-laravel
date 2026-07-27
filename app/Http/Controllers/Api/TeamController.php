<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teams = Team::with('captain')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($teams);
    }
}
