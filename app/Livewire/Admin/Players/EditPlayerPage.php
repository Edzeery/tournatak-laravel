<?php
namespace App\Livewire\Admin\Players;

use App\Models\Player;
use App\Models\User;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditPlayerPage extends Component
{
    public Player $player;
    public ?int $user_id = null;
    public ?int $team_id = null;
    public ?int $number = null;
    public ?string $position = null;
    public ?string $image = null;
    public $position_id = '';
    public $date_of_birth = '';
    public $nationality = '';
    public $height = '';
    public $weight = '';
    public $foot = '';
    public $sport_type = 'football';
    public $bio = '';
    public $is_captain = false;
    public $positions = [];

    public function mount(Player $player)
    {
        $this->player = $player;
        $this->user_id = $player->user_id;
        $this->team_id = $player->team_id;
        $this->number = $player->number;
        $this->position = $player->position;
        $this->image = $player->image;
        $this->position_id = $player->position_id ?? '';
        $this->date_of_birth = $player->date_of_birth ? $player->date_of_birth->format('Y-m-d') : '';
        $this->nationality = $player->nationality ?? '';
        $this->height = $player->height ?? '';
        $this->weight = $player->weight ?? '';
        $this->foot = $player->foot ?? '';
        $this->sport_type = $player->sport_type ?? 'football';
        $this->bio = $player->bio ?? '';
        $this->is_captain = $player->is_captain ?? false;
        $this->positions = \App\Models\Position::where('is_active', true)->orderBy('sport_type')->orderBy('sort_order')->get();
    }

    public function update()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
            'number' => 'nullable|integer|min:0',
            'position' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'position_id' => 'nullable|exists:positions,id',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'height' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:0',
            'foot' => 'nullable|in:right,left,both',
            'sport_type' => 'required|in:football,futsal',
            'bio' => 'nullable|string|max:5000',
            'is_captain' => 'boolean',
        ]);

        $this->player->update([
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'number' => $this->number,
            'position' => $this->position,
            'image' => $this->image,
            'position_id' => $this->position_id ?: null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'nationality' => $this->nationality ?: null,
            'height' => $this->height ?: null,
            'weight' => $this->weight ?: null,
            'foot' => $this->foot ?: null,
            'sport_type' => $this->sport_type,
            'bio' => $this->bio ?: null,
            'is_captain' => $this->is_captain,
        ]);

        session()->flash('success', 'تم تحديث اللاعب بنجاح');
        return redirect()->route('admin.players.index');
    }

    public function render()
    {
        return view('livewire.admin.players.edit-player-page', [
            'title' => 'تعديل لاعب',
            'player' => $this->player,
            'users' => User::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }
}
