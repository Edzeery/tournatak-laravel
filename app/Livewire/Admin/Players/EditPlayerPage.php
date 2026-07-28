<?php
namespace App\Livewire\Admin\Players;

use App\Models\Player;
use App\Models\User;
use App\Models\Team;
use App\Services\PlayerService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditPlayerPage extends Component
{
    public Player $player;
    public ?int $user_id = null;
    public ?int $team_id = null;
    public ?int $number = null;
    public ?string $position_text = null;
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
        $this->authorize('update', $player);

        $this->player = $player;
        $this->user_id = $player->user_id;
        $this->team_id = $player->team_id;
        $this->number = $player->number;
        $this->position_text = $player->position_text;
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
        $service = app(PlayerService::class);
        $this->validate($service->getValidationRules());

        $service->update($this->player, [
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'number' => $this->number,
            'position_text' => $this->position_text,
            'image' => $this->image,
            'position_id' => $this->position_id,
            'date_of_birth' => $this->date_of_birth,
            'nationality' => $this->nationality,
            'height' => $this->height,
            'weight' => $this->weight,
            'foot' => $this->foot,
            'sport_type' => $this->sport_type,
            'bio' => $this->bio,
            'is_captain' => $this->is_captain,
        ]);

        session()->flash('success', __('app.player_updated'));
        return redirect()->route('admin.players.index');
    }

    public function render()
    {
        return view('livewire.admin.players.edit-player-page', [
            'title' => __('app.page_title_edit_player'),
            'player' => $this->player,
            'users' => User::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }
}
