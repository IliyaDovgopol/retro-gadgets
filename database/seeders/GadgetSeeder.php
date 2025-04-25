<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gadget;
use App\Models\Category;
use Illuminate\Support\Str;

class GadgetSeeder extends Seeder
{
    public function run()
    {
        $gadgets = [
            ['name' => 'Nokia 3310', 'year' => 2000, 'category' => 'Старі телефони'],
            ['name' => 'Sony PlayStation 1', 'year' => 1994, 'category' => 'Консолі'],
            ['name' => 'Intel Pentium II', 'year' => 1997, 'category' => 'Ретро-комп’ютери'],
            ['name' => 'Apple iPod Classic', 'year' => 2001, 'category' => 'MP3-плеєри'],
        ];

        foreach ($gadgets as $gadget) {
            $category = Category::firstOrCreate(
                ['name' => $gadget['category']],
                ['slug' => Str::slug($gadget['category'])]
            );

            Gadget::create([
                'name' => $gadget['name'],
                'slug' => Str::slug($gadget['name']),
                'year' => $gadget['year'],
                'category_id' => $category->id,
                'description' => 'Опис ' . $gadget['name'],
                'intro' => 'Це вступ для ' . $gadget['name'],
                'legacy' => 'Історична роль ' . $gadget['name'],
                'unique_features' => 'Особливості ' . $gadget['name'],
                'image_url' => 'https://via.placeholder.com/300'
            ]);
        }
    }
}
