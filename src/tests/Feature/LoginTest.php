<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_login_with_missing_email_shows_validation_error()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }

    public function test_user_login_with_missing_password_shows_validation_error()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('password');

        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }

    public function test_user_login_with_invalid_credentials_shows_error_message()
    {
        $response = $this->from('/login')->post('/login',[
            'email' => 'wrong@example.com',
            'password' => 'incorrectpassword'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->followRedirects($response)->assertSee('ログイン情報が登録されていません');
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $email = $this->faker->unique()->safeEmail;
        $password = 'password123';

        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}