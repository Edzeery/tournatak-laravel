<?php

namespace App\Services;

use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamService
{
    public function create(array $data): Team
    {
        if (empty($data['sport_id'])) {
            $data['sport_id'] = Sport::where('slug', 'football')->value('id');
        }

        return Team::create($data);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team;
    }

    public function getValidationRules(bool $isUpdate = false, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:teams,name';
        if ($isUpdate && $ignoreId) {
            $uniqueRule .= ','.$ignoreId;
        }

        return [
            'name' => 'required|string|max:255|'.$uniqueRule,
            'captain_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string|max:2048',
            'logoFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:500',
            'points' => 'integer|min:0',
        ];
    }

    public function storeLogo(UploadedFile $file): string
    {
        $filename = 'logo_'.uniqid().'.'.$file->extension();
        $path = $file->storeAs('teams', $filename, 'uploads');

        return basename($path);
    }

    public function deleteLogoFile(?string $filename): void
    {
        if (! $filename || Str::startsWith($filename, 'http')) {
            return;
        }
        Storage::disk('uploads')->delete('teams/'.$filename);
    }
}
