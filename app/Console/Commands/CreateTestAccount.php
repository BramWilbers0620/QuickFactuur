<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestAccount extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:testaccount
                            {email? : Email adres voor het test account}
                            {--password= : Wachtwoord (standaard: test1234)}';

    /**
     * The console command description.
     */
    protected $description = 'Maak een test/admin account met volledige toegang';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? 'test@quickfactuur.nl';
        $password = $this->option('password') ?? 'test1234';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($existingUser->is_admin) {
                $this->info("Test account '{$email}' bestaat al en is al een admin.");
                return Command::SUCCESS;
            }

            // Make existing user an admin
            $existingUser->update(['is_admin' => true]);
            $this->info("Bestaand account '{$email}' is nu een admin/test account.");
            return Command::SUCCESS;
        }

        // Create new test account
        $user = User::create([
            'name' => 'Test Account',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        $this->newLine();
        $this->info('Test account succesvol aangemaakt!');
        $this->newLine();
        $this->table(
            ['Veld', 'Waarde'],
            [
                ['Email', $email],
                ['Wachtwoord', $password],
                ['Admin', 'Ja'],
                ['Volledige toegang', 'Ja'],
            ]
        );
        $this->newLine();
        $this->info('Je kunt nu inloggen met dit account om te testen.');

        return Command::SUCCESS;
    }
}
