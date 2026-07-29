<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlayerService
{
    public function create(array $data): Player
    {
        $data = array_filter($data, fn($v) => $v !== '');
        $data['position_id'] = $data['position_id'] ?? null;
        $data['date_of_birth'] = $data['date_of_birth'] ?? null;
        $data['nationality'] = $data['nationality'] ?? null;
        $data['height'] = $data['height'] ?? null;
        $data['weight'] = $data['weight'] ?? null;
        $data['foot'] = $data['foot'] ?? null;
        $data['bio'] = $data['bio'] ?? null;
        $data['is_captain'] = $data['is_captain'] ?? false;

        return Player::create($data);
    }

    public function update(Player $player, array $data): Player
    {
        $data = array_filter($data, fn($v) => $v !== '');
        $data['position_id'] = $data['position_id'] ?? null;
        $data['date_of_birth'] = $data['date_of_birth'] ?? null;
        $data['nationality'] = $data['nationality'] ?? null;
        $data['height'] = $data['height'] ?? null;
        $data['weight'] = $data['weight'] ?? null;
        $data['foot'] = $data['foot'] ?? null;
        $data['bio'] = $data['bio'] ?? null;
        $data['is_captain'] = $data['is_captain'] ?? false;

        $player->update($data);

        return $player;
    }

    public function storeImage(UploadedFile $file): string
    {
        $filename = 'player_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('players', $filename, 'uploads');
        return $filename;
    }

    public function deleteImageFile(?string $filename): void
    {
        if (!$filename) return;
        if (Str::startsWith($filename, ['http://', 'https://'])) return;
        Storage::disk('uploads')->delete('players/' . $filename);
    }

    public function getValidationRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
            'number' => 'nullable|integer|min:0',
            'position_text' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:512',
            'position_id' => 'nullable|exists:positions,id',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'height' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:0',
            'foot' => 'nullable|in:right,left,both',
            'sport_type' => 'required|in:football,futsal',
            'bio' => 'nullable|string|max:5000',
            'is_captain' => 'boolean',
        ];
    }
}
