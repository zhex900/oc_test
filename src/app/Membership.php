<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'membership';
    protected $fillable = ['person_id','expiry','expiry'];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
