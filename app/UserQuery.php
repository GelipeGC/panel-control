<?php

namespace App;

use App\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class UserQuery extends QueryBuilder
{
    public function findByEmail($email)
    {
        return $this->where(compact('email'))->first();
    }

    public function WithLastLogin()
    {
        $subselect = Login::select('logins.created_at')
                ->whereColumn('logins.user_id', 'users.id')
                ->latest()
                ->limit(1);

        $this->addSelect(['last_login_at' => $subselect]);
        
        return $this;
    }
}
