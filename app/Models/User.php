<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\AccountDetailsChangedMail;
use App\Mail\PasswordChangedMail;
use App\Mail\PasswordResetMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'phone_number',
        'address',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'role_type',
        'is_verified',
        'terms',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Phone numbers are stored without formatting so the unique index catches
     * "98765 43210" and "9876543210" as the same number. Blank becomes null
     * (many null rows are allowed by the unique index, many '' rows are not).
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/[\s\-().]/', '', (string) $phone);

        return $phone === '' ? null : $phone;
    }

    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['phone_number'] = static::normalizePhone($value);
    }

    /** Full URL of the uploaded profile photo, or null. */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->profile_photo
            ? asset('storage/users/' . $this->profile_photo)
            : null;
    }

    /** Up to two initials for a letter-avatar fallback. */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $parts = array_filter($parts);

        if (empty($parts)) {
            return 'A';
        }

        $first = mb_substr(array_shift($parts), 0, 1);
        $last  = $parts ? mb_substr(end($parts), 0, 1) : '';

        return mb_strtoupper($first . $last);
    }

    public function sendPasswordResetNotification($token): void
    {
        // Admins and storefront customers share this same users table (and
        // this same password_reset_tokens table) — role_type is what tells
        // this single shared method which reset flow to point the link at.
        $url = $this->role_type == 1
            ? route('admin.password.reset', ['token' => $token, 'email' => $this->email])
            : route('password.reset', ['token' => $token, 'email' => $this->email]);

        try {
            Mail::to($this->email)->send(new PasswordResetMail($url, $this->name));
            Log::info('Password reset: email dispatched', ['user_id' => $this->id, 'to' => $this->email]);
        } catch (\Throwable $e) {
            Log::error('Password reset: email send failed: '.$e->getMessage(), ['user_id' => $this->id, 'to' => $this->email]);
        }
    }

    /**
     * Sent whenever the password actually changes — both from the "My Account"
     * change-password form and, for symmetry, the forgot-password flow — so a
     * customer who didn't request either one still finds out.
     */
    public function sendPasswordChangedNotification(): void
    {
        try {
            Mail::to($this->email)->send(new PasswordChangedMail($this->name, now()->format('d M Y, h:i A')));
            Log::info('Password changed: notification email dispatched', ['user_id' => $this->id, 'to' => $this->email]);
        } catch (\Throwable $e) {
            Log::error('Password changed: notification email failed: '.$e->getMessage(), ['user_id' => $this->id, 'to' => $this->email]);
        }
    }

    /**
     * Sent whenever name/email/phone actually change via "My Account" — always
     * to the *original* email (passed in explicitly), not $this->email, so that
     * if the email itself was changed, the real owner still gets notified even
     * though $this->email is now the new address.
     *
     * @param  array<int, array{label: string, old: string, new: string}>  $changes
     */
    public function sendAccountDetailsChangedNotification(string $notifyEmail, array $changes): void
    {
        try {
            Mail::to($notifyEmail)->send(new AccountDetailsChangedMail($this->name, $changes, now()->format('d M Y, h:i A')));
            Log::info('Account details changed: notification email dispatched', ['user_id' => $this->id, 'to' => $notifyEmail, 'fields' => array_column($changes, 'label')]);
        } catch (Throwable $e) {
            Log::error('Account details changed: notification email failed: '.$e->getMessage(), ['user_id' => $this->id, 'to' => $notifyEmail]);
        }
    }

    /** Personal single-use welcome coupon issued when the account was first verified. */
    public function welcomeCoupon()
    {
        return $this->belongsTo(CouponModel::class, 'welcome_coupon_id');
    }
}
