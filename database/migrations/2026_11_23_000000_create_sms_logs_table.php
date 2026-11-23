<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

class CreateSmsLogsTable extends Migration
{
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('driver', 50);
            $table->string('operation', 50);
            $table->string('recipient', 255)->nullable();
            $table->string('status', 20)->index();
            $table->string('provider_message_id', 100)->nullable();
            $table->string('response_code', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['driver', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
}
