<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $realItems = [
            'Minyak Goreng Sania 2L',
            'Beras Ramos Super 5kg',
            'Gula Pasir Gulaku 1kg',
            'Kopi Kapal Api Special Mix 10s',
            'Susu Kental Manis Frisian Flag 370g',
            'Kecap Manis Bango 520ml',
            'Saus Sambal ABC 335ml',
            'Sardines ABC Saus Tomat 155g',
            'Tepung Terigu Segitiga Biru 1kg',
            'Baterai ABC Alkaline AA Isi 2',
        ];

        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'name' => $this->faker->randomElement($realItems) . ' ' . $this->faker->numberBetween(1, 99),
            'sku' => 'PRD-' . $this->faker->unique()->numberBetween(10000, 99999),
            'price' => $this->faker->numberBetween(3, 80) * 1000,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
    }
}