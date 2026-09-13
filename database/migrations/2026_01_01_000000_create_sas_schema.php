<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SAS — School Administration SaaS. Full multi-tenant schema (MySQL/SQLite).
// Every school-owned table carries school_id.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->string('code')->unique();
            $t->integer('price_monthly')->default(0);
            $t->string('currency', 8)->default('NGN');
            $t->integer('max_students')->nullable();
            $t->integer('max_teachers')->nullable();
            $t->integer('max_staff')->nullable();
            $t->integer('storage_mb')->nullable();
            $t->text('features')->default('[]');
            $t->boolean('is_custom')->default(false);
            $t->boolean('is_active')->default(true);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('schools', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->string('short_name')->nullable();
            $t->string('slug')->unique();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('address')->nullable();
            $t->string('website')->nullable();
            $t->string('logo_url')->nullable();
            $t->string('favicon_url')->nullable();
            $t->string('primary_color', 16)->default('#16a34a');
            $t->string('secondary_color', 16)->default('#15803d');
            $t->string('status', 16)->default('active');
            $t->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('plan_id');
            $t->string('status', 16)->default('trial');
            $t->string('billing_cycle', 16)->default('monthly');
            $t->timestamp('trial_ends_at')->nullable();
            $t->timestamp('current_period_end')->nullable();
            $t->timestamps();
        });

        Schema::create('users', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->nullable()->index();
            $t->string('role', 24);
            $t->string('first_name');
            $t->string('last_name')->nullable();
            $t->string('email')->index();
            $t->string('phone')->nullable();
            $t->string('password');
            $t->string('photo_url')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('email_verified')->default(false);
            $t->timestamp('last_login_at')->nullable();
            $t->rememberToken();
            $t->timestamps();
            $t->unique(['school_id', 'email']);
        });

        Schema::create('sessions_academic', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('name');
            $t->boolean('is_current')->default(false);
            $t->timestamps();
        });

        Schema::create('terms', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('session_id')->nullable();
            $t->string('name');
            $t->boolean('is_current')->default(false);
            $t->timestamps();
        });

        Schema::create('classes', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('name');
            $t->string('level')->nullable();
            $t->string('teacher_id')->nullable();
            $t->integer('capacity')->nullable();
            $t->timestamps();
        });

        Schema::create('subjects', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('name');
            $t->string('code')->nullable();
            $t->timestamps();
        });

        Schema::create('students', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('user_id')->nullable();
            $t->string('admission_no')->nullable();
            $t->string('first_name');
            $t->string('last_name')->nullable();
            $t->string('gender', 8)->nullable();
            $t->string('date_of_birth')->nullable();
            $t->string('class_id')->nullable()->index();
            $t->string('guardian_name')->nullable();
            $t->string('guardian_phone')->nullable();
            $t->string('status', 16)->default('active');
            $t->timestamps();
        });

        Schema::create('teachers', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('user_id')->nullable();
            $t->string('first_name');
            $t->string('last_name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('subject')->nullable();
            $t->string('employee_no')->nullable();
            $t->string('status', 16)->default('active');
            $t->timestamps();
        });

        Schema::create('student_attendance', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('student_id');
            $t->string('class_id')->nullable();
            $t->string('date');
            $t->string('status', 16)->default('present');
            $t->timestamps();
        });

        Schema::create('clock_logs', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('user_id');
            $t->string('clock_in')->nullable();
            $t->string('clock_out')->nullable();
            $t->float('hours')->nullable();
            $t->string('device')->nullable();
            $t->string('location')->nullable();
            $t->string('date');
            $t->timestamps();
        });

        Schema::create('results', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('student_id');
            $t->string('subject_id')->nullable();
            $t->string('class_id')->nullable();
            $t->string('term_id')->nullable();
            $t->float('ca_score')->default(0);
            $t->float('exam_score')->default(0);
            $t->float('total_score')->default(0);
            $t->string('grade', 8)->nullable();
            $t->string('remark')->nullable();
            $t->string('status', 16)->default('draft');
            $t->string('entered_by')->nullable();
            $t->timestamps();
        });

        Schema::create('exams', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('title');
            $t->string('type', 24)->default('cbt');
            $t->string('subject_id')->nullable();
            $t->string('class_id')->nullable();
            $t->text('instructions')->nullable();
            $t->integer('duration_mins')->default(30);
            $t->integer('question_count')->default(0);
            $t->integer('pass_mark')->default(50);
            $t->integer('attempts')->default(1);
            $t->boolean('randomize')->default(false);
            $t->float('negative_mark')->default(0);
            $t->string('starts_at')->nullable();
            $t->string('ends_at')->nullable();
            $t->string('status', 16)->default('draft');
            $t->string('created_by')->nullable();
            $t->timestamps();
        });

        Schema::create('exam_questions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('exam_id')->index();
            $t->string('type', 16)->default('mcq');
            $t->text('text');
            $t->text('options')->default('[]');
            $t->text('correct')->default('[]');
            $t->integer('marks')->default(1);
            $t->integer('sort_order')->default(0);
        });

        Schema::create('exam_attempts', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('exam_id');
            $t->string('student_id');
            $t->text('answers')->default('{}');
            $t->float('score')->nullable();
            $t->string('status', 16)->default('in_progress');
            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable();
        });

        Schema::create('assignments', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('class_id')->nullable();
            $t->string('subject_id')->nullable();
            $t->string('due_date')->nullable();
            $t->string('created_by')->nullable();
            $t->timestamps();
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('title');
            $t->text('body');
            $t->string('audience', 16)->default('all');
            $t->string('created_by')->nullable();
            $t->timestamps();
        });

        Schema::create('fee_structures', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('name');
            $t->string('category', 24)->default('tuition');
            $t->integer('amount')->default(0);
            $t->string('class_id')->nullable();
            $t->string('term_id')->nullable();
            $t->timestamps();
        });

        Schema::create('student_fees', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('student_id');
            $t->string('fee_id')->nullable();
            $t->integer('amount')->default(0);
            $t->integer('amount_paid')->default(0);
            $t->string('status', 16)->default('unpaid');
            $t->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('student_fee_id')->nullable();
            $t->string('student_id')->nullable();
            $t->integer('amount')->default(0);
            $t->string('method', 16)->default('cash');
            $t->string('reference')->nullable();
            $t->timestamps();
        });

        Schema::create('transactions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('type', 16);
            $t->string('category')->nullable();
            $t->string('description')->nullable();
            $t->integer('amount')->default(0);
            $t->string('date');
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('name');
            $t->string('sku')->nullable();
            $t->string('category')->nullable();
            $t->integer('purchase_price')->default(0);
            $t->integer('selling_price')->default(0);
            $t->integer('stock_qty')->default(0);
            $t->integer('low_stock')->default(5);
            $t->timestamps();
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('reference')->nullable();
            $t->integer('total')->default(0);
            $t->string('payment_method', 16)->default('cash');
            $t->string('customer')->nullable();
            $t->string('sold_by')->nullable();
            $t->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('sale_id')->index();
            $t->string('product_id')->nullable();
            $t->string('name');
            $t->integer('qty')->default(1);
            $t->integer('price')->default(0);
            $t->integer('subtotal')->default(0);
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->nullable()->index();
            $t->string('user_id')->nullable();
            $t->string('action');
            $t->string('resource')->nullable();
            $t->string('resource_id')->nullable();
            $t->text('old_value')->nullable();
            $t->text('new_value')->nullable();
            $t->string('ip')->nullable();
            $t->string('device')->nullable();
            $t->timestamps();
        });

        // Laravel session/cache tables (database driver) — created here for shared hosting.
        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        foreach ([
            'sessions','audit_logs','sale_items','sales','products','transactions',
            'fee_payments','student_fees','fee_structures','announcements','assignments',
            'exam_attempts','exam_questions','exams','results','clock_logs','student_attendance',
            'teachers','students','subjects','classes','terms','sessions_academic','users',
            'subscriptions','schools','subscription_plans',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
