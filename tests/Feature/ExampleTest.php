<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_order(): void
    {
        $response = $this->get('/');

        // リダイレクトのステータスコードを確認
        $response->assertStatus(302);

        // リダイレクト先が /order であることを確認
        $response->assertRedirect('/order');
    }

    /**
     * Test the order page returns a successful response.
     */
    public function test_order_page_returns_a_successful_response(): void
    {
        $response = $this->get('/order');

        $response->assertStatus(200);
    }
}
