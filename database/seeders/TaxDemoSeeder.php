<?php

namespace Database\Seeders;

use App\Models\TaxModel;
use Illuminate\Database\Seeder;

class TaxDemoSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            // ── GST Percentage Slabs (India) ─────────────────────────────────
            [
                'tax_name'      => 'GST 0%',
                'tax_alias'     => 'GST0',
                'tax_type'      => 'percentage',
                'tax_rate'      => 0.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 1,
                'description'   => 'Nil GST rate. Applicable on exempt goods such as fresh fruits, vegetables, milk, eggs, bread, and other essential food items.',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'GST 5%',
                'tax_alias'     => 'GST5',
                'tax_type'      => 'percentage',
                'tax_rate'      => 5.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 2,
                'description'   => '5% GST slab. Applies to packaged food items, edible oils, sugar, tea, coffee, domestic LPG, fertilisers, and some medicines.',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'GST 12%',
                'tax_alias'     => 'GST12',
                'tax_type'      => 'percentage',
                'tax_rate'      => 12.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 3,
                'description'   => '12% GST slab. Applies to processed food, butter, cheese, mobile phones (under ₹12,000), umbrellas, and some textiles.',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'GST 18%',
                'tax_alias'     => 'GST18',
                'tax_type'      => 'percentage',
                'tax_rate'      => 18.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 4,
                'description'   => '18% GST slab. Largest revenue-generating slab. Applies to electronics, appliances, restaurant services, packaged software, hotel rooms (₹2,500–₹7,500/night).',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'GST 28%',
                'tax_alias'     => 'GST28',
                'tax_type'      => 'percentage',
                'tax_rate'      => 28.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 5,
                'description'   => '28% GST slab. Applies to luxury goods, demerit goods — cars, motorcycles, tobacco, aerated drinks, and 5-star hotels.',
                'status'        => 1,
            ],

            // ── IGST (Interstate) ─────────────────────────────────────────────
            [
                'tax_name'      => 'IGST 5%',
                'tax_alias'     => 'IGST5',
                'tax_type'      => 'percentage',
                'tax_rate'      => 5.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 6,
                'description'   => 'Integrated GST at 5% for interstate supply of goods and services in the 5% slab. Collected by Central Government and transferred to destination state.',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'IGST 18%',
                'tax_alias'     => 'IGST18',
                'tax_type'      => 'percentage',
                'tax_rate'      => 18.00,
                'applicable_to' => 'all',
                'tax_region'    => 'domestic',
                'priority'      => 7,
                'description'   => 'Integrated GST at 18% for interstate supply of electronics, services, and other goods in the 18% slab.',
                'status'        => 1,
            ],

            // ── Customs & Import ──────────────────────────────────────────────
            [
                'tax_name'      => 'Basic Customs Duty 10%',
                'tax_alias'     => 'BCD10',
                'tax_type'      => 'percentage',
                'tax_rate'      => 10.00,
                'applicable_to' => 'all',
                'tax_region'    => 'international',
                'priority'      => 8,
                'description'   => 'Basic Customs Duty (BCD) at 10% on imported electronics and general merchandise. Levied by Indian Customs on CIF value of imported goods.',
                'status'        => 1,
            ],
            [
                'tax_name'      => 'Basic Customs Duty 20%',
                'tax_alias'     => 'BCD20',
                'tax_type'      => 'percentage',
                'tax_rate'      => 20.00,
                'applicable_to' => 'all',
                'tax_region'    => 'international',
                'priority'      => 9,
                'description'   => 'Basic Customs Duty (BCD) at 20% on imported garments, footwear, and fashion accessories under the higher duty bracket.',
                'status'        => 1,
            ],

            // ── Shipping / Service ────────────────────────────────────────────
            [
                'tax_name'      => 'GST on Shipping 18%',
                'tax_alias'     => 'GSTSHIP',
                'tax_type'      => 'percentage',
                'tax_rate'      => 18.00,
                'applicable_to' => 'all',
                'tax_region'    => 'all',
                'priority'      => 10,
                'description'   => '18% GST applicable on courier and freight delivery charges. Applied to the shipping fee charged at checkout.',
                'status'        => 1,
            ],

            // ── Fixed / Flat-Fee Tax ──────────────────────────────────────────
            [
                'tax_name'      => 'Flat Fee ₹50 (Environmental Cess)',
                'tax_alias'     => 'ENV50',
                'tax_type'      => 'fixed',
                'tax_rate'      => 50.00,
                'applicable_to' => 'product',
                'tax_region'    => 'domestic',
                'priority'      => 11,
                'description'   => 'Fixed flat fee of ₹50 per order as Environmental Cess for disposal/recycling of electronic and plastic waste (e-waste compliance).',
                'status'        => 1,
            ],

            // ── Inclusive Tax ─────────────────────────────────────────────────
            [
                'tax_name'      => 'MRP Inclusive GST 18%',
                'tax_alias'     => 'MRPGST18',
                'tax_type'      => 'inclusive',
                'tax_rate'      => 18.00,
                'applicable_to' => 'product',
                'tax_region'    => 'domestic',
                'priority'      => 12,
                'description'   => 'Tax-inclusive pricing at 18% GST. The displayed MRP already includes GST. Used for FMCG and consumer packaged goods where MRP is printed inclusive of all taxes.',
                'status'        => 1,
            ],

            // ── Compound Tax ──────────────────────────────────────────────────
            [
                'tax_name'      => 'Compound Cess (Luxury Goods)',
                'tax_alias'     => 'COMPCESS',
                'tax_type'      => 'compound',
                'tax_rate'      => 15.00,
                'applicable_to' => 'product',
                'tax_region'    => 'domestic',
                'priority'      => 13,
                'description'   => 'Compensation Cess levied on top of 28% GST on luxury and sin goods (e.g. large cars, tobacco, aerated drinks). Calculated on post-GST value.',
                'status'        => 0,
            ],
        ];

        foreach ($taxes as $tax) {
            TaxModel::updateOrCreate(
                ['tax_alias' => $tax['tax_alias']],
                $tax
            );
        }

        $this->command->info('Tax slabs seeded: ' . count($taxes));
    }
}
