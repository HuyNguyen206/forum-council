<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AvatarUpload extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public $photo;
    public $profileUser;

    public function mount()
    {
        $this->photo = $this->profileUser->avatarPath();
    }

    public function render()
    {
        return view('livewire.avatar-upload');
    }

    public function save()
    {
        if (! auth()->user()) return $this->redirect(route('login'));
        $this->authorize('upload', auth()->user());

        $this->validate([
            'photo' => 'image|max:5024', // 1MB Max
        ]);

        $filenameOrigin = $this->photo->getClientOriginalName();
        $fileName = $this->profileUser->id."_$filenameOrigin";

        $saveImagePath = $this->profileUser->image_path;
        if ($saveImagePath && Storage::exists($saveImagePath)) {
            Storage::delete($saveImagePath);
        }

        $path = $this->photo->storeAs('photos', $fileName, 'public');
        $this->profileUser->image_path  = $path;
        $this->profileUser->save();

        $this->emitTo(AvatarDisplay::class, 'updateAvatar');
        $this->dispatchBrowserEvent('notify', ['message' => 'Your avatar was update successfully!']);
    }

}
