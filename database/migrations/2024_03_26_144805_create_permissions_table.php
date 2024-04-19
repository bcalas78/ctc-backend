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
        Schema::disableForeignKeyConstraints();

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $permissions = [
            'joueur_research_team',
            'joueur_request_to_join_team',
            'dirigeant_create_championship',
            'dirigeant_update_championship',
            'dirigeant_delete_championship',
            'dirigeant_create_game',
            'dirigeant_update_game',
            'dirigeant_delete_game',
            'dirigeant_create_team',
            'dirigeant_update_team',
            'dirigeant_delete_team',
            'dirigeant_accept_player_in_team',
            'dirigeant_rejected_player_in_team',
            'dirigeant_delete_player_in_team',
            'dirigeant_add_result',
            'dirigeant_update_result',
            'dirigeant_delete_result',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(
                ['name' => $permission]
            );
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
