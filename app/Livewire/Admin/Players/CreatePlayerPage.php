<?php
namespace App\Livewire\Admin\Players;

use App\Models\Player;
use App\Models\User;
use App\Models\Team;
use App\Services\PlayerService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class CreatePlayerPage extends Component
{
    use WithFileUploads;

    public ?int $user_id = null;
    public ?int $team_id = null;
    public ?int $number = null;
    public ?string $position_text = null;
    public ?string $image = null;
    public $imageFile = null;
    public string $imageSrc = 'url';
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

    public function mount()
    {
        $this->positions = \App\Models\Position::where('is_active', true)->orderBy('sport_type')->orderBy('sort_order')->get();
    }

    public function updatedImageFile()
    {
        $this->validate(['imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:512']);
    }

    public function store()
    {
        $service = app(PlayerService::class);
        $this->validate($service->getValidationRules());

        $data = [
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'number' => $this->number,
            'position_text' => $this->position_text,
            'position_id' => $this->position_id,
            'date_of_birth' => $this->date_of_birth,
            'nationality' => $this->nationality,
            'height' => $this->height,
            'weight' => $this->weight,
            'foot' => $this->foot,
            'sport_type' => $this->sport_type,
            'bio' => $this->bio,
            'is_captain' => $this->is_captain,
            'image' => $this->resolveImage(),
        ];

        $service->create($data);

        session()->flash('success', __('app.player_created'));
        return redirect()->route('admin.players.index');
    }

    private function resolveImage(): ?string
    {
        if ($this->imageSrc === 'upload' && $this->imageFile) {
            return app(PlayerService::class)->storeImage($this->imageFile);
        }
        if ($this->imageSrc === 'url' && $this->image) {
            return $this->image;
        }
        return null;
    }

    public function render()
    {
        return view('livewire.admin.players.create-player-page', [
            'title' => __('app.page_title_add_player'),
            'users' => User::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }
}
