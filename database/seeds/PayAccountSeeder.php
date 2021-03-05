<?php

use App\PayAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PayAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pay_accounts')->delete();

        PayAccount::create([
            'email' => 'admin@a.a',
            'password' => Hash::make('password'),
        ]);
    }
}
