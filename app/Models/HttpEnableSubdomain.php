<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HttpEnableSubdomain extends Model
{
    protected $fillable = ['domain_id', 'subdomain_name', 'httpenabled','title','url','location','host','server','statuscode','vhost'];
    use HasFactory;
    public function project(){
        return $this->belongsTo('App\Models\Project');
    }
    public function domain(){
        return $this->belongsTo('App\Models\Domain');
    }
    public function subdomain(){
        return $this->belongsTo('App\Models\Subdomain');
    }
    public function nmap(){
        return $this->belongsTo('App\Models\Nmap');
    }
    public function nuclei(){
        return $this->belongsTo('App\Models\Nuclei');
    }
    public function screenshot(){
        return $this->belongsTo('App\Models\Screenshot');
    }
    public static function boot() {
        parent::boot();
        self::deleting(function($domains) { // before delete() method call this
             $domains->screenshot()->each(function($screenshot) {
                $screenshot->delete(); // <-- direct deletion
             });
             // do the rest of the cleanup...
        });
    }
}
