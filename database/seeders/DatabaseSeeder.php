<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Goat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Customer)
        User::create([
            'name' => 'Admin Ari Farm',
            'email' => 'admin@arifarm.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Budi Prasetyo',
            'email' => 'user@arifarm.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'user',
            'remember_token' => Str::random(10),
        ]);

        // 2. Seed Categories
        $categories = [
            [
                'name' => 'Kambing Etawa',
                'slug' => 'kambing-etawa',
                'description' => 'Kambing berukuran besar dengan telinga panjang terkulai, terkenal dengan produksi susu dan dagingnya yang berkualitas tinggi.',
                'image' => 'uploads/categories/kambing_etawa.png'
            ],
            [
                'name' => 'Kambing Boer',
                'slug' => 'kambing-boer',
                'description' => 'Kambing pedaging unggulan asal Afrika Selatan dengan tubuh yang tebal, padat, dan pertumbuhan yang sangat cepat.',
                'image' => 'uploads/categories/kambing_boer.png'
            ],
            [
                'name' => 'Kambing Kacang',
                'slug' => 'kambing-kacang',
                'description' => 'Kambing lokal Indonesia yang sangat adaptif, subur, dan memiliki daya tahan tubuh luar biasa terhadap penyakit.',
                'image' => 'uploads/categories/kambing_kacang.png'
            ],
            [
                'name' => 'Domba Texel',
                'slug' => 'domba-texel',
                'description' => 'Domba ras unggul dengan postur tubuh padat, dada lebar, dan bulu wol halus yang menghasilkan kualitas daging premium.',
                'image' => 'uploads/categories/domba_texel.png'
            ],
            [
                'name' => 'Domba Dombos',
                'slug' => 'domba-dombos',
                'description' => 'Domba khas Wonosobo dengan bulu wol lebat berkualitas tinggi, tubuh besar, dan sangat diminati untuk peternakan maupun qurban.',
                'image' => 'uploads/categories/domba_dombos.png'
            ]
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[$cat['slug']] = Category::create($cat);
        }

        // 3. Seed Goats (Products)
        $goats = [
            [
                'category_id' => $createdCategories['kambing-etawa']->id,
                'name' => 'Etawa Super Kaligesing Jantan',
                'slug' => 'etawa-super-kaligesing-jantan',
                'description' => 'Kambing Etawa Super Kaligesing, jantan, berumur 18 bulan. Memiliki telinga melipat dengan rapi, postur dada bidang, bulu lebat, dan kepala hitam cembung khas Etawa kontes. Sangat cocok untuk pejantan pemacek maupun hewan qurban premium.',
                'price' => 5500000.00,
                'stock' => 1,
                'weight_kg' => 65.5,
                'age_months' => 18,
                'gender' => 'male',
                'breed' => 'Etawa Kaligesing',
                'health_status' => 'vaccine_completed',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['kambing-etawa']->id,
                'name' => 'Etawa Senduro Betina Produktif',
                'slug' => 'etawa-senduro-betina-produktif',
                'description' => 'Etawa Senduro betina berumur 14 bulan, sudah siap kawin (siap bunting). Postur tubuh panjang, puting susu normal dua buah besar, dan jinak. Ideal untuk memulai peternakan susu kambing Anda.',
                'price' => 3800000.00,
                'stock' => 1,
                'weight_kg' => 45.0,
                'age_months' => 14,
                'gender' => 'female',
                'breed' => 'Etawa Senduro',
                'health_status' => 'healthy',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['kambing-boer']->id,
                'name' => 'Boer F4 Premium Jantan',
                'slug' => 'boer-f4-premium-jantan',
                'description' => 'Kambing Boer kelas F4 (keturunan murni tinggi), jantan, umur 24 bulan. Memiliki kepala cokelat tua dengan badan putih tebal, kaki pendek kokoh, dan daging yang padat. Pejantan super unggul untuk menghasilkan anakan pedaging berkualitas cepat tumbuh.',
                'price' => 7500000.00,
                'stock' => 1,
                'weight_kg' => 80.0,
                'age_months' => 24,
                'gender' => 'male',
                'breed' => 'Boer',
                'health_status' => 'vaccine_completed',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1527380969291-9106141a1bc6?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['kambing-boer']->id,
                'name' => 'Anakan Boer Cross Jantan',
                'slug' => 'anakan-boer-cross-jantan',
                'description' => 'Cempe (anakan) Boer Cross jantan berumur 6 bulan. Lincah, nafsu makan tinggi, sudah lepas sapih, dan siap digemukkan (fattening). Kualitas genetik baik dengan harga ekonomis.',
                'price' => 2400000.00,
                'stock' => 3,
                'weight_kg' => 28.5,
                'age_months' => 6,
                'gender' => 'male',
                'breed' => 'Boer Cross',
                'health_status' => 'healthy',
                'vaccine_status' => false,
                'images' => json_encode(['https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['kambing-kacang']->id,
                'name' => 'Kambing Kacang Lokal Jantan Cerdik',
                'slug' => 'kambing-kacang-lokal-jantan-cerdik',
                'description' => 'Kambing Kacang lokal jantan dewasa berumur 16 bulan. Tanduk lurus mengarah ke belakang, sangat aktif, tahan cuaca ekstrem, dan cocok untuk qurban hemat dengan budget terjangkau.',
                'price' => 1950000.00,
                'stock' => 2,
                'weight_kg' => 32.0,
                'age_months' => 16,
                'gender' => 'male',
                'breed' => 'Kacang',
                'health_status' => 'healthy',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1533048347048-132aae4c7648?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['domba-texel']->id,
                'name' => 'Domba Texel Jantan Premium',
                'slug' => 'domba-texel-jantan-premium',
                'description' => 'Domba Texel jantan umur 14 bulan, memiliki postur tegap, dada bidang, dan bulu wol halus. Kualitas daging premium, ideal untuk qurban maupun pembibitan unggul.',
                'price' => 3100000.00,
                'stock' => 2,
                'weight_kg' => 48.0,
                'age_months' => 14,
                'gender' => 'male',
                'breed' => 'Texel',
                'health_status' => 'vaccine_completed',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1484557985045-edf25e08da73?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ],
            [
                'category_id' => $createdCategories['domba-dombos']->id,
                'name' => 'Domba Dombos Wonosobo Jantan',
                'slug' => 'domba-dombos-wonosobo-jantan',
                'description' => 'Domba Dombos (Domba Wonosobo) jantan dewasa berumur 18 bulan. Bulu wol putih lebat khas pegunungan Dieng, tubuh sangat bongsor dan berbobot mantap. Sangat gagah untuk hewan qurban premium atau indukan pejantan.',
                'price' => 4500000.00,
                'stock' => 1,
                'weight_kg' => 58.5,
                'age_months' => 18,
                'gender' => 'male',
                'breed' => 'Dombos',
                'health_status' => 'healthy',
                'vaccine_status' => true,
                'images' => json_encode(['https://images.unsplash.com/photo-1484557985045-edf25e08da73?w=800&auto=format&fit=crop']),
                'status' => 'available'
            ]
        ];

        foreach ($goats as $g) {
            $goat = Goat::create($g);
            
            // Seed 3 weighings for each goat (e.g. 2 months ago, 1 month ago, and current weight)
            $currentWeight = (float)$g['weight_kg'];
            \App\Models\GoatWeighing::create([
                'goat_id' => $goat->id,
                'weight_kg' => $currentWeight - 6.5,
                'weighed_at' => now()->subMonths(2),
                'notes' => 'Penimbangan rutin bulanan, kondisi sehat bugar.'
            ]);
            \App\Models\GoatWeighing::create([
                'goat_id' => $goat->id,
                'weight_kg' => $currentWeight - 3.2,
                'weighed_at' => now()->subMonth(),
                'notes' => 'Nafsu makan tinggi, kenaikan berat stabil.'
            ]);
            \App\Models\GoatWeighing::create([
                'goat_id' => $goat->id,
                'weight_kg' => $currentWeight,
                'weighed_at' => now(),
                'notes' => 'Berat badan terbaru sebelum dipasarkan.'
            ]);
        }
    }
}
