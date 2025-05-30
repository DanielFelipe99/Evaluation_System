<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('permissions', function (Blueprint $table) {
        $table->string('description')->nullable();
        $table->string('module')->nullable();
    });
}

public function down()
{
    Schema::table('permissions', function (Blueprint $table) {
        $table->dropColumn(['description', 'module']);
    });
}
};
