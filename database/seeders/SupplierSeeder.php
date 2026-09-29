<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Indofood Sukses Makmur Tbk',
                'phone' => '021-57958822',
                'address' => 'Sudirman Plaza, Indofood Tower, Jl. Jend. Sudirman Kav. 76-78, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Unilever Indonesia Tbk',
                'phone' => '021-80827000',
                'address' => 'BSD Green Office Park, Jl. BSD Boulevard Barat, Tangerang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Mayora Indah Tbk',
                'phone' => '021-80637777',
                'address' => 'Gedung Mayora, Jl. Tomang Raya No. 21-23, Jakarta Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}