<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('recepts', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('recepts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
