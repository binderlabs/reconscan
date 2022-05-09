<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubdomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subdomains', function (Blueprint $table) {
            $table->id();
            $table->integer("domain_id");
            $table->integer("project_id")->nullable();
            $table->text("subdomain_name");
            $table->text("path")->nullable();
            $table->boolean("httpenabled")->default(0);
            $table->text("title")->nullable();
            $table->text("url")->nullable();
            $table->text("location")->nullable();
            $table->text("host")->nullable(); //ip
            $table->text("server")->nullable(); // banner
            $table->text("statuscode")->nullable();
            $table->boolean("vhost")->default(0);
            $table->text("nuclei_jobid")->nullable();
            $table->text("nmap_jobid")->nullable();
            $table->text("dir_jobid")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subdomains');
    }
}
