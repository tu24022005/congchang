<?php 
namespace App\Models; 

use Database\Factories\UserFactory; 
use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract 
{ 
    /** @use HasFactory<UserFactory> */ 
    use HasFactory, Notifiable, CanResetPassword;

    /** 
     * The attributes that are mass assignable.  
     * 
     * @var list<string> 
     */ 
    protected $fillable = [ 
        'name', 
        'email', 
        'avatar_path',
        'phone',
        'password', 
        'role',  
        'google_id', // Đã được gộp chung vào mảng này
        'loyalty_points',
        'login_attempts',
        'login_locked_at',
    ]; 

    /** 
     * The attributes that should be hidden for serialization.  
     * 
     * @var list<string> 
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
            'password' => 'hashed', 
            'login_locked_at' => 'datetime',
        ]; 
    } 

    public function isLocked(): bool
    {
        return !is_null($this->login_locked_at);
    } 

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar_path)) {
            return null;
        }

        if (str_starts_with($this->avatar_path, 'http://') || str_starts_with($this->avatar_path, 'https://')) {
            return $this->avatar_path;
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar_path);
        }

        return asset('storage/' . ltrim($this->avatar_path, '/'));
    }

    public function getInitialsAttribute(): string
    {
        $nameParts = array_values(array_filter(explode(' ', trim($this->name))));
        if (count($nameParts) >= 2) {
            return mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1));
        }
        return mb_strtoupper(mb_substr($this->name, 0, 2));
    } 

    // ==========================================
    // CẦU NỐI VỚI BẢNG TIN NHẮN 
    // ==========================================
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function stockAlertSubscriptions()
    {
        return $this->hasMany(StockAlertSubscription::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    public function collectedVouchers()
    {
        return $this->belongsToMany(Voucher::class, 'voucher_user')->withTimestamps();
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems(): HasManyThrough
    {
        return $this->hasManyThrough(CartItem::class, Cart::class);
    }

    public function loyaltyPointTransactions()
    {
        return $this->hasMany(LoyaltyPointTransaction::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'actor_id');
    }

    public static function membershipTierFor(float|int $spend = 0, int|float $points = 0): array
    {
        // Hệ thống quy định: 10.000đ mua hàng hoàn thành = 1 điểm tích lũy
        // Xét hạng dựa trên ĐIỀU KIỆN CAO HƠN giữa Doanh số thực chi và Điểm tích lũy (bao gồm cả điểm được Admin buff/thưởng)
        $effectiveValue = max((float) $spend, (float) ($points * 10000));

        return match (true) {
            $effectiveValue >= 20000000 => ['name' => 'Kim cương', 'icon' => '👑', 'class' => 'text-info',      'points_threshold' => 2000, 'spend_threshold' => 20000000],
            $effectiveValue >= 10000000 => ['name' => 'Bạch kim',  'icon' => '💎', 'class' => 'text-secondary', 'points_threshold' => 1000, 'spend_threshold' => 10000000],
            $effectiveValue >= 5000000  => ['name' => 'Vàng',      'icon' => '🥇', 'class' => 'text-warning',   'points_threshold' => 500,  'spend_threshold' => 5000000],
            $effectiveValue >= 2000000  => ['name' => 'Bạc',       'icon' => '🥈', 'class' => 'text-secondary', 'points_threshold' => 200,  'spend_threshold' => 2000000],
            $effectiveValue >= 1000000  => ['name' => 'Thành viên','icon' => '🥉', 'class' => 'text-success',   'points_threshold' => 100,  'spend_threshold' => 1000000],
            default                     => ['name' => 'Mới tham gia','icon'=> '🌱','class' => 'text-muted',     'points_threshold' => 0,    'spend_threshold' => 0],
        };
    }


    public function getMembershipTierAttribute(): array
    {
        $spend = (float) $this->orders()->whereIn('status', ['paid', 'completed'])->sum('total');
        return self::membershipTierFor($spend, (int) $this->loyalty_points);
    }

    public function roleLabel(): string
    {
        return [
            'admin' => 'Quản trị viên',
            'manager' => 'Quản lý',
            'warehouse_staff' => 'Nhân viên kho',
            'customer_service' => 'Nhân viên CSKH',
            'customer' => 'Khách hàng',
            'user' => 'Khách hàng',
        ][$this->role] ?? $this->role;
    }
}