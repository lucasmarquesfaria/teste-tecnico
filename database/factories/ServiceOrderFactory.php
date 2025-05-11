<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pendente', 'em_andamento', 'concluida']),
            'client_id' => User::factory()->client(),
            'technician_id' => User::factory()->technician(),
        ];
    }

    /**
     * Define a ordem como pendente.
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendente',
        ]);
    }

    /**
     * Define a ordem como em andamento.
     */
    public function emAndamento(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'em_andamento',
        ]);
    }

    /**
     * Define a ordem como concluída.
     */
    public function concluida(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'concluida',
        ]);
    }

    /**
     * Define o cliente e técnico específicos para a ordem.
     */
    public function comUsuarios(User $client, User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);
    }
}
