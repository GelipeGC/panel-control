<?php

namespace App;

use App\UserFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //protected $table = 'users';
    use Notifiable, SoftDeletes;


   protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    public function newEloquentBuilder($query)
    {
        return new UserQuery($query);
    }

    

   public function team()
   {
       return $this->belongsTo(Team::class)->withDefault();
   }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function isAdmin()
    {
        return $this->is_admin == 'admin';
    }
    public function scopeFilterBy($query,QueryFilter $filters, array $data)
    {
        return $filters->applyTo($query, $data);

    }
    public function setStateAttribute($value)
    {
        $this->attributes['active'] = $value == 'active';
    }

    public function getStateAttribute()
    {
        if ($this->active !== null) {
            return $this->active ? 'active' : 'inactive';
        }
    }
    
}

