<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_register_with_missing_name_shows_validation_error()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors(['name']);

        $this->followRedirects($response)->assertSee('お名前を入力してください');
    }

    public function test_user_register_with_missing_email_shows_validation_error()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors(['email']);

        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }

    public function test_user_register_with_missing_password_shows_validation_error()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'てすと太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }

    public function test_user_register_with_short_password_shows_validation_error()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_user_register_with_mismatched_password_confirmation_show_validation_error()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors(['password']);

        $this->followRedirects($response)->assertSee('パスワードと一致しません');
    }

    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');
        $this->assertAuthenticated();
    }
}
