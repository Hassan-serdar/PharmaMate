<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MedicineTypeEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // اسم الدواء
            $table->string('active_ingredient'); // المادة الفعالة
            $table->string('dosage'); // العيار (مثال: "500 mg")
            $table->enum('type', array_column(MedicineTypeEnum::cases(), 'value')); // النوع
            $table->string('image_path')->nullable(); // مسار الصورة (اختياري)
            $table->text('description')->nullable(); // وصف بسيط عن الدواء
            $table->string('manufacturer')->nullable(); // الشركة المصنعة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};