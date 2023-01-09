<?php

test('user earn 10 points when they create thread ', function () {
    $user = signIn();
    \PHPUnit\Framework\assertEquals($user->points, 0);
    \Pest\Laravel\post(route('threads.store'), \App\Models\Thread::factory()->raw());
    \PHPUnit\Framework\assertEquals($user->points, 10);
});

test('user earn 2 points when they reply thread ', function () {
    session(['skipCaptchaValidation' => true]);

    $user = signIn();
    \PHPUnit\Framework\assertEquals($user->points, 0);
    $thread = create(\App\Models\Thread::class, ['user_id' => $user->id]);

    \Livewire\Livewire::actingAs($user)->test(\App\Http\Livewire\NewReply::class, [
        'thread' => $thread
    ])
        ->set('body', 'This is test')
        ->set('captchaToken', 'dummyToken')
        ->call('storeReply');

    \PHPUnit\Framework\assertEquals($user->fresh()->points, 12);
});

test('user earn 50 points when their reply was marked as best reply', function () {
    $user = create(\App\Models\User::class);
    $reply = create(\App\Models\Reply::class, ['user_id' => $user->id]);

    $reply->thread->toggleBestReply($reply->id);

    \PHPUnit\Framework\assertEquals(52, $user->fresh()->points);
});

test('user lose points when they delete thread', function () {
    $user = create(\App\Models\User::class);
    $thread = create(\App\Models\Thread::class, ['user_id' => $user->id]);
    \PHPUnit\Framework\assertEquals(10, $user->fresh()->points);
    $thread->delete();
    \PHPUnit\Framework\assertEquals(0, $user->fresh()->points);
});

test('user lose points when they delete their reply', function () {
    $user = create(\App\Models\User::class);
    $reply = create(\App\Models\Reply::class, ['user_id' => $user->id]);
    \PHPUnit\Framework\assertEquals(2, $user->fresh()->points);
    $reply->delete();
    \PHPUnit\Framework\assertEquals(0, $user->fresh()->points);

});

test('user earn point when their reply was favorited ', function () {
    $user = create(\App\Models\User::class);
    $reply = create(\App\Models\Reply::class, ['user_id' => $user->id]);
    $this->signIn()->favoriteReplies()->attach($reply);

    \PHPUnit\Framework\assertEquals(7, $user->fresh()->points);
});

test('user lose point when their reply was unfavorited ', function () {
    $user = create(\App\Models\User::class);
    $reply = create(\App\Models\Reply::class, ['user_id' => $user->id]);
    $this->signIn()->favoriteReplies()->attach($reply);

    \PHPUnit\Framework\assertEquals(7, $user->fresh()->points);

    auth()->user()->favoriteReplies()->detach($reply);

    \PHPUnit\Framework\assertEquals(2, $user->fresh()->points);
});
