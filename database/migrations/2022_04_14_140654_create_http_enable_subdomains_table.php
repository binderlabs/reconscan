<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHttpEnableSubdomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('http_enable_subdomains', function (Blueprint $table) {
            $table->id();
            $table->integer("domain_id");
            $table->integer("subdomain_id");
            $table->integer("project_id")->nullable();
            $table->boolean("httpenabled")->default(0);
            $table->text("title")->nullable();
            $table->text("url")->nullable();
            $table->text("location")->nullable();
            $table->text("host")->nullable(); //ip
            $table->text("server")->nullable(); // banner
            $table->text("statuscode")->nullable();
            $table->boolean("vhost")->default(0);
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
        Schema::dropIfExists('http_enable_subdomains');
    }
}
