<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];

    public function profession()
    {
        return $this->belongsTo(Profession::class)->withDefault([
            'title' => '(Sin profesión)'
        ]);
    }
}
