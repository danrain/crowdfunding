<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Database\Traits\Validation as ValidationTrait;
use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletesTrait;
use Hash;

/**
 * User model
 */
class User extends Model
{
    use ValidationTrait,
        SoftDeletesTrait;
    
    public $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'email'     => 'required|email|unique',
        'password'  => 'required:create|confirmed',
        'roles' => 'array|exists:roles,code'
    ];
    
    protected $fillable = ['firstname', 'lastname', 'email', 'password', 'password_confirmation', 'roles'];
    
    protected $onlyValidationFields = ['password_confirmation', 'roles'];
    
    protected $hidden = ['password'];
    
    /**
     * Register callback generate hash password before save
     */
    protected function bootIfNotBooted()
    {
        parent::bootIfNotBooted();
        
        static::saving(function ($model) {
            $model->hashPassword();
        });
    }
    
    /**
     * Create hash password before save
     */
    public function hashPassword()
    {
        $needHash = true;
        if ($this->exists && ! $this->isDirty('password')) {
            $needHash = false;
        }
        
        if ($needHash) {
            $plantext = $this->getAttribute('password');
            $hash = Hash::make($plantext);
            $this->setAttribute('password', $hash);
        }
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }
}
