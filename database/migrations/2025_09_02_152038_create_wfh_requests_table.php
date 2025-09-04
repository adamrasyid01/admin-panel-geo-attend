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
        Schema::create('wfh_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('notes_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Jika user admin dihapus, ID-nya jadi null

            // 2. Kolom untuk menyimpan ISI catatannya
            $table->text('admin_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wfh_requests');
        Schema::table('wfh_requests', function (Blueprint $table) {
            $table->drop(['approved_by']);
        });
    }
};
