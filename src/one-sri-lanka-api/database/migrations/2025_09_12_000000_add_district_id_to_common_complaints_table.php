<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('common_complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('common_complaints', 'district_id')) {
                $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('common_complaints', function (Blueprint $table) {
            if (Schema::hasColumn('common_complaints', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropColumn('district_id');
            }
        });
    }
};
