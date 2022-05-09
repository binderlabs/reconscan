<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNucleisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nucleis', function (Blueprint $table) {
            $table->id();
            $table->integer('domain_id');
            $table->integer('subdomain_id');
            $table->text('template_id')->nullable();
            $table->text('name')->nullable();
            $table->text('severity')->nullable();
            $table->text('type')->nullable();
            $table->text('host')->nullable();
            $table->text('matched_at')->nullable();
            $table->text('version_info')->nullable();
            $table->text('description')->nullable();
            $table->text('matcher_name')->nullable();
            $table->text('curl_command')->nullable();
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
        Schema::dropIfExists('nucleis');
    }
}
