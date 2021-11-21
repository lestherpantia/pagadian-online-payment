<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collne extends Model
{
    use HasFactory;

    /* connection */
    protected $connection = 'pgsql_2';

    /* database */
    protected $table = 'collne';

    /* prevent saving timestamp */
    public $timestamps = false;

    /* prevent saving primary keys */
    public $incrementing = false;
    protected $primaryKey = null;

}
