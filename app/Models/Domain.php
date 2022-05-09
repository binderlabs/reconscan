<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;
    public function subdomains(){
        return $this->hasMany('App\Models\Subdomain');
    }
    public function nmaps(){
        return $this->hasMany('App\Models\Nmap');
    }
    public function nucleis(){
        return $this->hasMany('App\Models\Nuclei');
    }
    public function screenshots(){
        return $this->hasMany('App\Models\Screenshot');
    }
    public function http_enable_subdomains(){
        return $this->hasMany('App\Models\HttpEnableSubdomain');
    }
    public static function boot() {
        parent::boot();
        self::deleting(function($domains) { // before delete() method call this
             $domains->subdomains()->each(function($subdomains) {
                $subdomains->delete(); // <-- direct deletion
             });
             $domains->http_enable_subdomains()->each(function($http_enable_subdomains) {
                
                $http_enable_subdomains->delete(); // <-- direct deletion
             });
        });
    }
}
