<?php 
namespace App\Models; 

use Database\Factories\UserFactory; 
use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

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
        'password', 
        'role',  
        'google_id', // Đã được gộp chung vào mảng này
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
}