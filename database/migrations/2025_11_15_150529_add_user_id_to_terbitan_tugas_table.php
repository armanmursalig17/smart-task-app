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
        Schema::table('terbitan_tugas', function (Blueprint $table) {
            // Tambahkan kolom user_id sebagai foreign key
            $table->foreignId('user_id')
                  ->nullable() // Atur nullable jika Anda memiliki data lama tanpa user_id
                  ->constrained('users')
                  ->onDelete('cascade') // Hapus terbitan tugas jika user dihapus
                  ->after('id'); // Posisikan setelah 'id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terbitan_tugas', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['user_id']);
            // Drop column
            $table->dropColumn('user_id');
        });
    }
};