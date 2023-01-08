<?php

namespace Tests\Feature;

use App\Http\Livewire\AvatarUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    public function test_only_authenticate_user_can_upload_avatar()
    {
        $user = create(User::class);
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.png');

        Livewire::test(AvatarUpload::class, ['profileUser' => $user, 'photo' => $file])
            ->call('save')->assertRedirect(route('login'));
    }

    public function test_only_valid_avatar_can_be_uploaded()
    {
        Storage::fake('public');

        Livewire::test(AvatarUpload::class, ['profileUser' => $this->signIn(), 'photo' =>  'not-image'])
            ->call('save')->assertHasErrors('photo');
    }

    public function test_authenticate_user_can_upload_avatar()
    {
        $user = create(User::class);
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.png');

        Livewire::actingAs($user)->test(AvatarUpload::class, ['profileUser' => $user,  'photo' => $file])
            ->call('save');
        $imagePath = $user->fresh()->image_path;
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'image_path' => $imagePath
        ]);

        Storage::disk('public')->assertExists($imagePath);
    }
}
