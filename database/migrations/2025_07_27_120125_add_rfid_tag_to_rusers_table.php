<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rusers', function (Blueprint $table) {
            $table->string('rfid_tag')->unique()->nullable()->after('department');
            $table->string('contact_number')->nullable()->after('rfid_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rusers', function (Blueprint $table) {
            $table->dropColumn(['rfid_tag', 'contact_number']);
        });
    }
};
