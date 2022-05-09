<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdomain extends Model
{
    protected $fillable = ['domain_id', 'subdomain_name'];
    
    use HasFactory;
    public function domain(){
        return $this->belongsTo('App\Models\Domain');
    }
    public function nmap(){
        return $this->belongsTo('App\Models\Nmap');
    }
    public function nuclei(){
        return $this->belongsTo('App\Models\Nuclei');
    }
    public function screenshots(){
        return $this->belongsTo('App\Models\Screenshot');
    }
    public function http_enable_subdomains(){
        return $this->hasMany('App\Models\HttpEnableSubdomain');
    }
    public function directory(){
        return $this->hasMany('App\Models\Directory');
    }
    public static function boot() {
        parent::boot();
        self::deleting(function($domains) { // before delete() method call this
             $domains->http_enable_subdomains()->each(function($http_enable_subdomains) {
                $http_enable_subdomains->delete(); // <-- direct deletion
             });
             $domains->screenshots()->each(function($screenshots) {
                $screenshots->delete(); // <-- direct deletion
             });
             // do the rest of the cleanup...
        });
    }
}
