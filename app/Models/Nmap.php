<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nmap extends Model
{
    use HasFactory;
    public function subdomains(){
        return $this->belongsTo('App\Models\Subdomain');
    }
}
