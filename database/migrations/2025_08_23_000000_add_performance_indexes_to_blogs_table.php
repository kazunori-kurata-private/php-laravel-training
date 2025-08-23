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
        Schema::table('blogs', function (Blueprint $table) {
            // ブログ一覧表示のパフォーマンス向上
            $table->index(['user_id', 'updated_at'], 'blogs_user_updated_index');
            $table->index(['category_id', 'updated_at'], 'blogs_category_updated_index');
            
            // 権限チェックのパフォーマンス向上
            $table->index(['user_id', 'category_id'], 'blogs_user_category_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_user_updated_index');
            $table->dropIndex('blogs_category_updated_index');
            $table->dropIndex('blogs_user_category_index');
        });
    }
};
