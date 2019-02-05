<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'people';

    /**
     * Get the membership record associated with the person.
     */
    public function membership()
    {
        return $this->hasOne('App\Membership');
    }

    public function scopeFilter($query, $filter){
        return $query
            ->where($filter)
            ->with('membership');
    }
}
