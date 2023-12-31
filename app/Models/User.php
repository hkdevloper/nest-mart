<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    protected $table = 'users';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'avatar_url',
        'is_admin',
        'is_vendor',
        'is_user',
        'profile_photo_url',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_vendor' => 'boolean',
        'is_user' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
    public function getProfilePhotoUrlAttribute() : string
    {
        return $this->profile_photo_path !== null;
    }

    public function addresses() : HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function vendorDetails() : HasOne
    {
        return $this->hasOne(VendorDetails::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function wishLists() : HasMany
    {
        return $this->hasMany(WishList::class, 'user_id', 'id');
    }

    public function carts() : HasMany
    {
        return $this->hasMany(Cart::class)->where('is_checkout', false);
    }

    public function hasWishList($user_id) : bool
    {
        return $this->wishLists()->where('user_id', $user_id)->exists();
    }

    public function hasCart($user_id) : bool
    {
        return $this->carts()->where('user_id', $user_id)->exists();
    }

    public function invoices() : HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function userInvoice($invNumber) : array | object
    {
        return $this->invoices()->where('invoice_number', $invNumber)->get();
    }
}
