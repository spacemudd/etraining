<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLastInboundAtToWhatsappConversationsTable extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->timestamp('last_inbound_at')->nullable()->after('last_message_at');
        });

        // Best-effort backfill from the latest inbound customer message per phone.
        if (Schema::hasTable('whatsapp_messages')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("
                    UPDATE whatsapp_conversations AS c
                    INNER JOIN (
                        SELECT phone, MAX(COALESCE(sent_at, created_at)) AS last_inbound_at
                        FROM whatsapp_messages
                        WHERE direction = 'inbound'
                          AND (is_note = 0 OR is_note IS NULL)
                        GROUP BY phone
                    ) AS m ON m.phone = c.phone
                    SET c.last_inbound_at = m.last_inbound_at
                ");
            }
        }
    }

    public function down(): void
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropColumn('last_inbound_at');
        });
    }
}
