<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 5000);
        $vatRate = fake()->randomElement([0, 9, 21]);
        $vatAmount = round($amount * ($vatRate / 100), 2);
        $total = $amount + $vatAmount;

        return [
            'user_id' => User::factory(),
            'invoice_number' => 'FAC' . fake()->unique()->numerify('####'),
            'company_name' => fake()->company(),
            'company_email' => fake()->companyEmail(),
            'company_address' => fake()->address(),
            'company_phone' => fake()->phoneNumber(),
            'company_kvk' => fake()->numerify('########'),
            'company_iban' => fake()->iban('NL'),
            'customer_name' => fake()->company(),
            'customer_email' => fake()->safeEmail(),
            'customer_address' => fake()->address(),
            'customer_phone' => fake()->phoneNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'payment_terms' => '30',
            'description' => fake()->sentence(),
            'items' => [
                [
                    'description' => fake()->sentence(3),
                    'quantity' => fake()->numberBetween(1, 10),
                    'price' => fake()->randomFloat(2, 50, 500),
                    'total' => $amount,
                ],
            ],
            'amount' => $amount,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'vat_rate' => $vatRate,
            'status' => 'concept',
            'template' => 'modern',
            'brand_color' => '#2563eb',
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verzonden',
            'sent_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'betaald',
            'sent_at' => now()->subDays(7),
            'paid_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verzonden',
            'sent_at' => now()->subDays(45),
            'due_date' => now()->subDays(15),
        ]);
    }
}
