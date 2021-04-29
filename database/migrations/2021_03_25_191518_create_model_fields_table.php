<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type');
            $table->string('database_name');
            $table->foreignId('project_model_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('references_id')->nullable()->constrained('project_models')->nullOnDelete()->cascadeOnUpdate();
            $table->boolean('in_view')->default(true);
            $table->boolean('in_edit')->default(true);
            $table->boolean('in_create')->default(true);
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
        Schema::dropIfExists('model_fields');
    }
}
