<?php

// app/Models/UnavailableDate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnavailableDate extends Model
{
    public $timestamps = false;
    protected $fillable = ['property_id', 'date'];
}

?>
