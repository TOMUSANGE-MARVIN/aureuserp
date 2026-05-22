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
        Schema::create('payroll_salary_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payroll_salary_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('structure_id')->constrained('payroll_salary_structures')->onDelete('cascade');
            $table->string('name');
            $table->string('code', 50);
            $table->enum('type', ['earning', 'deduction'])->default('earning');
            $table->enum('amount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sequence')->default(10);
            $table->timestamps();
        });

        Schema::create('payroll_payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('structure_id')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->enum('status', ['draft', 'confirmed', 'paid'])->default('draft');
            $table->text('note')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->foreign('structure_id')->references('id')->on('payroll_salary_structures')->onDelete('set null');
        });

        if (Schema::hasTable('employees_employees')) {
            Schema::table('payroll_payslips', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('employees_employees')->onDelete('cascade');
            });
        }

        Schema::create('payroll_payslip_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained('payroll_payslips')->onDelete('cascade');
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->string('name');
            $table->string('code', 50);
            $table->enum('type', ['earning', 'deduction']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sequence')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payslip_lines');
        Schema::dropIfExists('payroll_payslips');
        Schema::dropIfExists('payroll_salary_rules');
        Schema::dropIfExists('payroll_salary_structures');
    }
};
