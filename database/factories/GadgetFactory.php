<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Gadget;
use Illuminate\Database\Eloquent\Factories\Factory;

class GadgetFactory extends Factory
{
    protected $model = Gadget::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word . ' Gadget',
            'slug' => $this->faker->slug,
            'year' => $this->faker->numberBetween(1980, 2020),
            'category_id' => Category::factory(),
            'description' => $this->faker->paragraph,
            'intro' => $this->faker->sentence,
            'legacy' => $this->faker->sentence,
            'unique_features' => $this->faker->words(3, true),
			'image_url' => 'https://via.placeholder.com/300x200',
        ];
    }
}
