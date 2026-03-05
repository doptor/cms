<?php

// ═══════════════════════════════════════════════════════════════════
// RyaanCMS — Complete Database Migrations
// Run: php artisan migrate --seed
// ═══════════════════════════════════════════════════════════════════

// ─── 2024_01_01_000001_create_users_table.php ─────────────────────

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();

            // AI API Keys (AES-256 encrypted)
            $table->text('claude_api_key')->nullable();
            $table->text('deepseek_api_key')->nullable();
            $table->enum('ai_routing_mode', ['auto', 'claude', 'deepseek'])->default('auto');

            // Security
            $table->boolean('is_active')->default(true);
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Preferences
            $table->string('timezone')->default('UTC');
            $table->string('locale')->default('en');
            $table->string('theme')->default('dark');

            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('is_active');
        });

        // ─── PROJECTS ─────────────────────────────────────────────
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['landing_page','blog','ecommerce','saas','portfolio','booking','custom'])->default('custom');
            $table->enum('status', ['draft','building','ready','deployed'])->default('draft');
            $table->string('framework')->default('laravel');

            // Deployment — FTP (shared hosting)
            $table->string('ftp_host')->nullable();
            $table->string('ftp_username')->nullable();
            $table->text('ftp_password')->nullable();   // encrypted
            $table->integer('ftp_port')->default(21);
            $table->string('ftp_remote_path')->default('public_html/');
            $table->string('domain')->nullable();

            // Database credentials (shared hosting MySQL)
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_name')->nullable();
            $table->string('db_username')->nullable();
            $table->text('db_password')->nullable();    // encrypted

            $table->timestamp('deployed_at')->nullable();
            $table->json('settings')->nullable();       // theme, colors, fonts
            $table->json('ai_context')->nullable();     // conversation context
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── PROJECT FILES ─────────────────────────────────────────
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');             // e.g. resources/views/home.blade.php
            $table->string('type')->nullable(); // blade, php, css, js, etc.
            $table->longText('content');
            $table->boolean('generated_by_ai')->default(true);
            $table->timestamps();

            $table->unique(['project_id', 'path']);
            $table->index('project_id');
        });

        // ─── CHAT MESSAGES ─────────────────────────────────────────
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->string('model_used')->nullable();    // claude-sonnet-4, deepseek-v3
            $table->integer('tokens_used')->default(0);
            $table->json('files_generated')->nullable(); // list of file paths
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });

        // ─── AI USAGE LOGS ─────────────────────────────────────────
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model');             // claude, deepseek
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->string('prompt_summary')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // ─── MARKETPLACE ITEMS ─────────────────────────────────────
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('short_description');
            $table->string('category');
            $table->enum('type', ['template', 'plugin', 'module'])->default('template');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('preview_url')->nullable();
            $table->integer('download_count')->default(0);
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('rating_count')->default(0);
            $table->json('tags')->nullable();
            $table->string('framework_version')->default('laravel-11');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('files_path')->nullable();
            $table->timestamps();

            $table->index(['is_approved', 'category']);
            $table->index('is_featured');
        });

        // ─── MARKETPLACE REVIEWS ───────────────────────────────────
        Schema::create('marketplace_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1–5
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['marketplace_item_id', 'user_id']);
        });

        // ─── DEPLOYMENTS LOG ───────────────────────────────────────
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            $table->json('steps')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('files_uploaded')->default(0);
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();
        });

        // ─── PASSWORD RESET ────────────────────────────────────────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ─── SESSIONS ─────────────────────────────────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('marketplace_items');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('users');
    }
};
