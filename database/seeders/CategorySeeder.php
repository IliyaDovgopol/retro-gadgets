<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Консолі', 'Старі телефони', 'Ретро-комп’ютери', 'MP3-плеєри'];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
