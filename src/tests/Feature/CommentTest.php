<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


class CommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_comment_count_increase_when_logged_in_user_send_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => 'これがコメントです',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'これがコメントです',
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSeeText('コメント');
        $response->assertSeeText('(1)');
        $response->assertSeeText('これがコメントです');
    }

    public function test_guest_cannot_send_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => 'ゲストのコメントです',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'body' => 'ゲストのコメントです',
            'item_id' => $item->id,
        ]);
    }

    public function test_user_send_empty_comment_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => '',
        ]);
    }

    public function test_user_send_with_long_comment_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('こ', 256);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => $longComment,
        ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => $longComment,
        ]);
    }
}
