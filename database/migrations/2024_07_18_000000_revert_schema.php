<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Change to a schema that can be used temporarily
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('parent_type')->default('App\Models\GeneralAssemblies\GeneralAssembly')->change();
            $table->string('has_long_answers')->default(false)->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'general_assembly_id');
        });

        Schema::create('event_triggers', function (Blueprint $table) {
            $table->text('name');
            $table->timestamp('date');
            $table->integer('signal');
            $table->text('comment')->nullable();

            $table->primary('signal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('general_assembly_id', 'parent_id');
        });

        Schema::dropIfExists('event_triggers');

        Schema::table('questions', function (Blueprint $table) {
            $table->string('parent_type')->default(null)->change();
            $table->string('has_long_answers')->default(null)->change();
        });
    }
};
