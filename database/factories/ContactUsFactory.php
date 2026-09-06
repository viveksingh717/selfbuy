<?php

namespace Database\Factories;

use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactUsFactory extends Factory
{
    protected $model = ContactUs::class;

    public function definition(): array
    {
        $subjects = [
            'Question about my order',
            'Where is my delivery?',
            'Return / refund request',
            'Product availability',
            'Payment failed but amount deducted',
            'Coupon code not working',
            'Wrong item received',
            'Bulk / wholesale enquiry',
            'Feedback about the website',
            'Cancel my order',
        ];

        $status    = $this->faker->randomElement(['unread', 'unread', 'read', 'read', 'done']);
        $createdAt = $this->faker->dateTimeBetween('-45 days', 'now');
        $replied   = $status === 'done';

        return [
            'name'        => $this->faker->name(),
            'email'       => $this->faker->unique()->safeEmail(),
            'phone'       => $this->faker->optional(0.8)->numerify('+91 9#### #####'),
            'subject'     => $this->faker->randomElement($subjects),
            'message'     => $this->faker->paragraphs($this->faker->numberBetween(1, 3), true),
            'status'      => $status,
            'is_starred'  => $this->faker->boolean(15),
            'admin_reply' => $replied ? $this->faker->paragraph() : null,
            'replied_at'  => $replied ? $this->faker->dateTimeBetween($createdAt, 'now') : null,
            'replied_by'  => $replied ? 'admin@selfbuy.com' : null,
            'ip_address'  => $this->faker->ipv4(),
            'created_at'  => $createdAt,
            'updated_at'  => $createdAt,
        ];
    }
}
