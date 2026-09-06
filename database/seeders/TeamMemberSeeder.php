<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        if (TeamMember::exists()) {
            return;
        }

        $members = [
            [
                'name'        => 'Vivek Singh',
                'designation' => 'Founder & Owner',
                'bio'         => 'Built SelfBuy from the ground up in 2026 — from the product catalog to checkout to customer support.',
                'sort_order'  => 1,
                'status'      => 1,
            ],
            [
                'name'        => 'Team Member',
                'designation' => 'Customer Support',
                'bio'         => 'Here to help with orders, returns and anything else you need.',
                'sort_order'  => 2,
                'status'      => 1,
            ],
            [
                'name'        => 'Team Member',
                'designation' => 'Operations',
                'bio'         => 'Keeps the catalog, orders and deliveries running smoothly.',
                'sort_order'  => 3,
                'status'      => 1,
            ],
        ];

        foreach ($members as $m) {
            TeamMember::create($m);
        }
    }
}
