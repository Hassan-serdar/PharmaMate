<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SuggestionStatusEnum;
use App\Enums\MedicineTypeEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacist_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('active_ingredient');
            $table->string('dosage');
            $table->enum('type', array_column(MedicineTypeEnum::cases(), 'value'));

            $table->enum('status', array_column(SuggestionStatusEnum::cases(), 'value'))->default(SuggestionStatusEnum::PENDING->value);
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_suggestions');
    }
};
