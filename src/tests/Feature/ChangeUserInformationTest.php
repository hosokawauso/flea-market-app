<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ChangeUserInformationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_profile_edit_form_displays_user_information_correctly()
    {
        $user = User::factory()->create([
            'profile_img' => 'profile_imgs/sample.png',
            'name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '香川県高松市浜ノ町',
            'building' => 'マリンタワー1120',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('storage/profile_imgs/sample.png');
        $response->assertSee('value="テスト太郎"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="香川県高松市浜ノ町"', false);
        $response->assertSee('value="マリンタワー1120"', false);
    }
}

