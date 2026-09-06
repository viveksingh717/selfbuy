<?php

namespace Database\Seeders;

use App\Models\AdminNotification;
use App\Models\ContactUs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminNotificationSeeder extends Seeder
{
    public function run(): void
    {
        if (AdminNotification::exists()) {
            return;
        }

        // One notification per recent contact message, so the bell isn't empty in dev.
        ContactUs::latest()->take(8)->get()->each(function ($c) {
            AdminNotification::create([
                'type'       => 'contact',
                'title'      => 'New contact message from ' . $c->name,
                'body'       => Str::limit(strip_tags($c->message), 90),
                'url'        => '/admin/contact_us',
                'icon'       => 'fa-envelope',
                'data'       => ['contact_id' => $c->id],
                'read_at'    => $c->status === 'done' ? now() : null,
                'created_at' => $c->created_at,
                'updated_at' => $c->created_at,
            ]);
        });
    }
}
