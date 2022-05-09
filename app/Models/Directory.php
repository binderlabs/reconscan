<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;
    public function subdomain(){
        return $this->belongsTo('App\Models\Subdomain');
    }
}
