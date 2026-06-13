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
        Schema::create('activity_logs', function (Blueprint $table) {
            // id: bigint unsigned, PRI, auto_increment
            $table->id(); 
            
            // user_id: bigint unsigned, Nullable (YES)
            $table->bigInteger('user_id')->unsigned()->nullable(); 
            
            // role: varchar(255), Nullable (YES)
            $table->string('role', 255)->nullable(); 
            
            // activity: varchar(255), Nullable (NO)
            $table->string('activity', 255); 
            
            // description: text, Nullable (YES)
            $table->text('description')->nullable(); 
            
            // ip_address: varchar(255), Nullable (YES)
            $table->string('ip_address', 255)->nullable(); 
            
            // user_agent: varchar(255), Nullable (YES)
            $table->string('user_agent', 255)->nullable(); 
            
            // created_at & updated_at: timestamp, Nullable (YES)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
