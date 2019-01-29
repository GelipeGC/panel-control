<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //protected $table = 'users';
    use SoftDeletes;

    use Notifiable;

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
    ];

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
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

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return;
        }

        $query->where('name','like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search){
                $query->where('name','like', "%{$search}%");
            });
    }
}

