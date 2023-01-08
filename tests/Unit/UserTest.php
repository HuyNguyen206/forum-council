<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class UserTest extends TestCase
{
     use RefreshDatabase, RefreshRedis;
     public function test_user_can_determine_avatar_path()
     {
         $user = create(User::class);

         self::assertEquals($user->avatarPath(), Storage::url('photos/avatar-default.png'));

         $anotherUser = create(User::class, ['image_path' => 'photos/1_avatar.jpg']);
         self::assertNull($anotherUser->avatarPath());

         Storage::fake('public');
         $finalUser = create(User::class, ['image_path' => 'photos/3_avatar.jpg']);
         $image = UploadedFile::fake()->image('3_avatar.jpg');
         Storage::putFileAs('photos', $image, '3_avatar.jpg', 'public');
         self::assertEquals($finalUser->avatarPath(), Storage::url('photos/3_avatar.jpg'));

//         $anotherUser = create(User::class, ['image_path' => 'photos/1_avatar.jpg']);
     }
}
