<?php

use App\Enums\FeedbackTypeEnum;
use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackPriorityEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', array_column(FeedbackTypeEnum::cases(), 'value'));
            $table->string('subject');
            $table->text('message');
            $table->enum('status', array_column(FeedbackStatusEnum::cases(), 'value'))->default(FeedbackStatusEnum::NEW->value);
            $table->enum('priority', array_column(FeedbackPriorityEnum::cases(), 'value'))->default(FeedbackPriorityEnum::LOW->value);
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
