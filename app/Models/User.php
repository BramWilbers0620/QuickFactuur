<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'is_admin',
        // Company profile
        'company_name',
        'company_address',
        'company_phone',
        'company_kvk',
        'company_iban',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Bepaal of gebruiker momenteel in een actieve trial zit.
     */
    public function onGenericTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    /**
     * Hulpmethode: is gebruiker actief (trial of betaald)?
     * Admin accounts hebben altijd toegang.
     */
    public function hasActiveAccess(): bool
    {
        return $this->is_admin || $this->onGenericTrial() || $this->subscribed('default');
    }

    /**
     * Check of gebruiker een admin/test account is.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the customers for the user.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the invoices for the user.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
