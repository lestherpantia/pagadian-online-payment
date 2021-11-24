<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailDetails extends Model
{
    use HasFactory;

    public $bill_num;
    public $amount;

}
