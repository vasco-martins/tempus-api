<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginProjectTable extends Migration
{
    public function up()
    {
        Schema::create('plugin_project', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('plugin_id')->constrained();
            $table->foreignId('project_id')->constrained();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plugin_project');
    }
}
