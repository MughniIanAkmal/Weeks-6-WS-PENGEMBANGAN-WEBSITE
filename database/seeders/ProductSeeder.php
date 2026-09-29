<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel kosong sebelum seeding
        DB::table('products')->truncate();

        $catMakanan = Category::where('name', 'Makanan')->first()->id ?? 1;
        $catMinuman = Category::where('name', 'Minuman')->first()->id ?? 2;
        $catRumah   = Category::where('name', 'Kebutuhan Rumah Tangga')->first()->id ?? 3;
        $catObat    = Category::where('name', 'Obat-obatan')->first()->id ?? 4;

        $products = [
            // Makanan
            ['category_id' => $catMakanan, 'name' => 'Indomie Goreng Spesial 85g', 'sku' => 'MKN-001', 'price' => 3500, 'stock' => 120],
            ['category_id' => $catMakanan, 'name' => 'Indomie Kuah Ayam Bawang 75g', 'sku' => 'MKN-002', 'price' => 3500, 'stock' => 95],
            ['category_id' => $catMakanan, 'name' => 'Mie Sedaap Goreng Original 90g', 'sku' => 'MKN-003', 'price' => 3500, 'stock' => 80],
            ['category_id' => $catMakanan, 'name' => 'Biskuit Roma Kelapa 300g', 'sku' => 'MKN-004', 'price' => 11500, 'stock' => 45],
            ['category_id' => $catMakanan, 'name' => 'Chitato Sapi Panggang 68g', 'sku' => 'MKN-005', 'price' => 12000, 'stock' => 30],
            ['category_id' => $catMakanan, 'name' => 'Oreo Vanilla Cream 133g', 'sku' => 'MKN-006', 'price' => 9500, 'stock' => 60],
            ['category_id' => $catMakanan, 'name' => 'SilverQueen Almond 62g', 'sku' => 'MKN-007', 'price' => 17500, 'stock' => 25],
            ['category_id' => $catMakanan, 'name' => 'Beng-Beng Chocolate Box 20g', 'sku' => 'MKN-008', 'price' => 2500, 'stock' => 150],
            ['category_id' => $catMakanan, 'name' => 'Taro Net Seaweed 65g', 'sku' => 'MKN-009', 'price' => 6000, 'stock' => 40],
            ['category_id' => $catMakanan, 'name' => 'Nabati Wafer Keju 168g', 'sku' => 'MKN-010', 'price' => 8000, 'stock' => 50],
            ['category_id' => $catMakanan, 'name' => 'Roti Tawar Kupas Sari Roti', 'sku' => 'MKN-011', 'price' => 16000, 'stock' => 20],
            ['category_id' => $catMakanan, 'name' => 'Kacang Garuda Rosta 100g', 'sku' => 'MKN-012', 'price' => 10500, 'stock' => 35],
            ['category_id' => $catMakanan, 'name' => 'Khong Guan Biscuit Red Box 1600g', 'sku' => 'MKN-013', 'price' => 115000, 'stock' => 15],

            // Minuman
            ['category_id' => $catMinuman, 'name' => 'Aqua Air Mineral Botol 600ml', 'sku' => 'MNM-001', 'price' => 4000, 'stock' => 180],
            ['category_id' => $catMinuman, 'name' => 'Le Minerale 600ml', 'sku' => 'MNM-002', 'price' => 3500, 'stock' => 140],
            ['category_id' => $catMinuman, 'name' => 'Teh Botol Sosro Kotak 250ml', 'sku' => 'MNM-003', 'price' => 4000, 'stock' => 90],
            ['category_id' => $catMinuman, 'name' => 'Pocari Sweat Botol 500ml', 'sku' => 'MNM-004', 'price' => 8500, 'stock' => 65],
            ['category_id' => $catMinuman, 'name' => 'Ultra Milk Cokelat 250ml', 'sku' => 'MNM-005', 'price' => 6500, 'stock' => 75],
            ['category_id' => $catMinuman, 'name' => 'Good Day Cappuccino Botol 250ml', 'sku' => 'MNM-006', 'price' => 7000, 'stock' => 55],
            ['category_id' => $catMinuman, 'name' => 'Coca Cola Slim Can 330ml', 'sku' => 'MNM-007', 'price' => 6500, 'stock' => 50],
            ['category_id' => $catMinuman, 'name' => 'Floridina Orange Juice 350ml', 'sku' => 'MNM-008', 'price' => 3500, 'stock' => 85],
            ['category_id' => $catMinuman, 'name' => 'Bear Brand Susu Steril 189ml', 'sku' => 'MNM-009', 'price' => 10500, 'stock' => 70],
            ['category_id' => $catMinuman, 'name' => 'Nescafe Classic Kopi Can 240ml', 'sku' => 'MNM-010', 'price' => 9000, 'stock' => 40],
            ['category_id' => $catMinuman, 'name' => 'Teh Pucuk Harum 350ml', 'sku' => 'MNM-011', 'price' => 4000, 'stock' => 110],
            ['category_id' => $catMinuman, 'name' => 'Kratingdaeng Minuman Energi 150ml', 'sku' => 'MNM-012', 'price' => 7000, 'stock' => 35],

            // Kebutuhan Rumah Tangga
            ['category_id' => $catRumah, 'name' => 'Sabun Mandi Lifebuoy Total 10 85g', 'sku' => 'KRT-001', 'price' => 4500, 'stock' => 80],
            ['category_id' => $catRumah, 'name' => 'Pasta Gigi Pepsodent Pencegah Gigi Berlubang 190g', 'sku' => 'KRT-002', 'price' => 15500, 'stock' => 60],
            ['category_id' => $catRumah, 'name' => 'Shampoo Sunsilk Black Shine 160ml', 'sku' => 'KRT-003', 'price' => 22000, 'stock' => 30],
            ['category_id' => $catRumah, 'name' => 'Deterjen Bubuk Rinso Anti Noda 770g', 'sku' => 'KRT-004', 'price' => 21500, 'stock' => 40],
            ['category_id' => $catRumah, 'name' => 'Sunlight Jeruk Nipis Pencuci Piring 750ml', 'sku' => 'KRT-005', 'price' => 16000, 'stock' => 50],
            ['category_id' => $catRumah, 'name' => 'Minyak Goreng Bimoli Pouch 2 Liter', 'sku' => 'KRT-006', 'price' => 38500, 'stock' => 35],
            ['category_id' => $catRumah, 'name' => 'Tissue Paseo Smart Facial 250 Sheets', 'sku' => 'KRT-007', 'price' => 14000, 'stock' => 45],
            ['category_id' => $catRumah, 'name' => 'So Klin Pewangi Pakaian Pink 800ml', 'sku' => 'KRT-008', 'price' => 12500, 'stock' => 40],
            ['category_id' => $catRumah, 'name' => 'Bayclin Pemutih Regular 500ml', 'sku' => 'KRT-009', 'price' => 9500, 'stock' => 30],
            ['category_id' => $catRumah, 'name' => 'Mama Lemon Jeruk Nipis 780ml', 'sku' => 'KRT-010', 'price' => 14500, 'stock' => 45],
            ['category_id' => $catRumah, 'name' => 'Super Pell Pembersih Lantai Apel 770ml', 'sku' => 'KRT-011', 'price' => 15000, 'stock' => 35],
            ['category_id' => $catRumah, 'name' => 'Baygon Aerosol Anti Nyamuk 600ml', 'sku' => 'KRT-012', 'price' => 42000, 'stock' => 25],

            // Obat-obatan
            ['category_id' => $catObat, 'name' => 'Paracetamol 500mg Strip 10 Tablet', 'sku' => 'OBT-001', 'price' => 6000, 'stock' => 100],
            ['category_id' => $catObat, 'name' => 'Panadol Extra Merah Strip 10 Kaplet', 'sku' => 'OBT-002', 'price' => 14500, 'stock' => 85],
            ['category_id' => $catObat, 'name' => 'Bodrex Sakit Kepala Strip 10 Tablet', 'sku' => 'OBT-003', 'price' => 5500, 'stock' => 90],
            ['category_id' => $catObat, 'name' => 'Tolak Angin Cair Box 12 Sachet', 'sku' => 'OBT-004', 'price' => 48000, 'stock' => 40],
            ['category_id' => $catObat, 'name' => 'Promag Obat Maag Strip 10 Tablet', 'sku' => 'OBT-005', 'price' => 9500, 'stock' => 70],
            ['category_id' => $catObat, 'name' => 'Minyak Kayu Putih Cap Lang 60ml', 'sku' => 'OBT-006', 'price' => 24000, 'stock' => 50],
            ['category_id' => $catObat, 'name' => 'Betadine Obat Luka Antiseptik 30ml', 'sku' => 'OBT-007', 'price' => 28500, 'stock' => 35],
            ['category_id' => $catObat, 'name' => 'Salonpas Koyo Pereda Nyeri Isi 10 Lembar', 'sku' => 'OBT-008', 'price' => 8500, 'stock' => 80],
            ['category_id' => $catObat, 'name' => 'Hansaplast Plester Luka Kain Elastis Isi 10', 'sku' => 'OBT-009', 'price' => 6500, 'stock' => 95],
            ['category_id' => $catObat, 'name' => 'Decolgen Flu & Batuk Strip 4 Tablet', 'sku' => 'OBT-010', 'price' => 4500, 'stock' => 60],
            ['category_id' => $catObat, 'name' => 'Antimo Obat Mabuk Perjalanan Strip 10 Tablet', 'sku' => 'OBT-011', 'price' => 6000, 'stock' => 75],
            ['category_id' => $catObat, 'name' => 'Mylanta Cair Obat Sakit Maag 50ml', 'sku' => 'OBT-012', 'price' => 21000, 'stock' => 30],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
