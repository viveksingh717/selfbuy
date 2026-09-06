<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    public function run(): void
    {
        if (ContactUs::exists()) {
            return; // don't pile up duplicates on re-run
        }

        // A few hand-written realistic ones...
        $samples = [
            [
                'name' => 'Aarav Sharma', 'email' => 'aarav.sharma@example.com', 'phone' => '+91 90040 11223',
                'subject' => 'Where is my delivery?', 'status' => 'unread',
                'message' => "Hi, I placed order #SB10231 four days ago and the tracking hasn't updated. Could you check what's going on?",
            ],
            [
                'name' => 'Priya Nair', 'email' => 'priya.nair@example.com', 'phone' => '+91 88991 55600',
                'subject' => 'Coupon code not working', 'status' => 'read',
                'message' => "The code WELCOME10 gives an 'invalid coupon' error at checkout. Is it expired?",
            ],
            [
                'name' => 'Rohan Mehta', 'email' => 'rohan.mehta@example.com', 'phone' => null,
                'subject' => 'Bulk / wholesale enquiry', 'status' => 'done', 'is_starred' => true,
                'message' => "We run a small retail chain and would like to discuss wholesale pricing for regular orders.",
                'admin_reply' => "Thanks for reaching out! I've forwarded your details to our B2B team who will contact you within 2 business days.",
                'replied_at' => now()->subDays(2), 'replied_by' => 'admin@selfbuy.com',
            ],
        ];

        foreach ($samples as $s) {
            ContactUs::create($s);
        }

        // ...plus a batch of random dummy data for the listing.
        ContactUs::factory()->count(25)->create();
    }
}
