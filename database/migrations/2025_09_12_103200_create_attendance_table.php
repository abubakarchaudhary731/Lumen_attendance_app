<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Enums\Attendance\AttendanceStatus;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('check_in');
            $table->text('notes')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->boolean('is_missed_checkout')->default(false);
            $table->enum('status', AttendanceStatus::values())->default(AttendanceStatus::CHECKED_IN->value);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('check_in');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
