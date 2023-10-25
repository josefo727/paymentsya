<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = Client::factory()->create([
            'contact_person' => 'Cristian Leon',
            'account' => 'massivespacenew',
            'store_name' => 'Massive Space',
            'vtex_domain' => 'https://massivespacenew.myvtex.com/',
            'store_domain' => 'https://massivespacenew.myvtex.com/',
            'payment_system' => '201,203',
            'is_production' => false,
        ]);

        $client->credential()->create([
            'dashboard' => 'https://merchant.paymentsway.co/',
            'email' => 'soporte@massivespace.rocks',
            'password' => 'L1zj\%J31x',
            'merchant_id' => 65,
            'terminal_id' => 1166,
            'form_id' => 1425,
            'payments_way_api_key' => 'N2Y5NGQyZWQ2NTljZmQyY2VjMGRhZjVkMTZjY2MxNmNkNzk1YmE5NWM0ZWM2OTg5YWUzMzQ5NDhmM2NjYzlkMDkyMTZhMWEzMTJkOGYyYWZjYjRhOWE3ZGUyODYwOWI5ZGQ0NmI3ZGRjZjVkOGI0NWMzNzgxOGEyZDgxZDEwNjY=',
            'vtex_api_app_key' => 'vtexappkey-massivespacenew-TXNDVP',
            'vtex_api_app_token' => 'EPYJLVTYILXHWGHPVFNPZKALRKJNRLNTJQVVPSROBYAJGIPGDJPQRAHKWVTGENBKANPBGCNCXGDGKGOQZPKFRSQNSGAYGYIDHYVHQJQETIOCNWADWFMWSKJIJPTDBSLW',
        ]);
    }
}
