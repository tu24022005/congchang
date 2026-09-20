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
        'phone',
        'password', 
        'role',  
        'google_id', // Đã được gộp chung vào mảng này
        'loyalty_points',
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
        ]; 
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

    public static function membershipTierFor(float|int $spend): array
    {
        return match (true) {
            $spend >= 20000000 => ['name' => 'Kim cương', 'class' => 'text-info'],
            $spend >= 10000000 => ['name' => 'Bạch kim', 'class' => 'text-secondary'],
            $spend >= 5000000 => ['name' => 'Vàng', 'class' => 'text-warning'],
            $spend >= 2000000 => ['name' => 'Bạc', 'class' => 'text-secondary'],
            $spend >= 1000000 => ['name' => 'Thành viên', 'class' => 'text-success'],
            default => ['name' => 'Mới tham gia', 'class' => 'text-muted'],
        };
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