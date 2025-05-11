<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // A resposta pode ser 200 (sucesso) ou 302 (redirecionamento para login)
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            'A resposta deve ser 200 (sucesso) ou 302 (redirecionamento)'
        );
    }
}
