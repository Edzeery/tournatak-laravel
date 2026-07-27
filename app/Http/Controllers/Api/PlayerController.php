<?php

namespace App\Http\Controllers\Api;

use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlayerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $players = Player::with(['user', 'team'])
            ->withCount('goals')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($players);
    }
}
