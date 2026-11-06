<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_package_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('commission_amount', 10, 2)->default(0)->comment('该员工分得的销售提成');
            $table->timestamps();

            $table->unique(['patient_package_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_package_sales');
    }
};
