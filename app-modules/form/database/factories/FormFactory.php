<?php

namespace Assist\Form\Database\Factories;

use Assist\Form\Models\Form;
use Assist\Form\Models\FormItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Form $form) {
            if ($form->items()->doesntExist()) {
                $form->items()->createMany(FormItem::factory()->count(3)->make()->toArray());
            }
        });
    }
}
