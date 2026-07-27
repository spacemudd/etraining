<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAndIsNoteToWhatsappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_messages', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('trainee_id');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('whatsapp_messages', 'is_note')) {
                $table->boolean('is_note')->default(false)->after('direction');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_messages', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('whatsapp_messages', 'is_note')) {
                $table->dropColumn('is_note');
            }
        });
    }
}
