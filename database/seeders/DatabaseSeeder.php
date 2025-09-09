<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pharmacy;
use App\Enums\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'superadmin@website.com',
            'password' => Hash::make('password'),
            'role' => Role::SUPER_ADMIN,
        ]);

        // -- 2. إنشاء الأدمنز --
        User::factory()->count(3)->create([
            'role' => Role::ADMIN,
        ]);

        // -- 3. إنشاء الصيدلانيين مع صيدلياتهم --
        // منشغّل دالة بتعمله صيدلية وبتربطها فيه مباشرةً
        User::factory()->count(10)->create([
            'role' => Role::PHARMACIST,
        ])->each(function ($pharmacist) {
            Pharmacy::factory()->create([
                'user_id' => $pharmacist->id,
            ]);
        });

        // -- 4. إنشاء باقي أنواع المستخدمين --
        User::factory()->count(10)->create([
            'role' => Role::USER,
        ]);

//        User::factory()->count(10)->create([
 //           'role' => Role::DELIVERY,
   //     ]);

     //   User::factory()->count(10)->create([
       //     'role' => Role::WAREHOUSE_OWNER,
       // ]);
    }
}

