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

        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->unique();
            $table->timestamps();
        });

        $sports = [
            'football', 'football américain', 'tennis', 'tennis de table', 'handball', 'volleyball',
            'basketball', 'pétanque', 'hockey', 'baseball', 'rugby', 'cricket', 'padel', 'golf',
            'mini-golf', 'gymnastique', 'gymnastique rythmique', 'course à pied', 'athlétisme',
            'cyclisme', 'natation', 'water-polo', 'voile', 'surf', 'aviron', 'canoë-kayak', 'lutte',
            'judo', 'karaté', 'aïkido', 'boxe', 'ski', 'luge', 'snowboard', 'roller', 'skateboard',
            'sports automobiles', 'e-sport', 'apnée', 'baby-foot', 'acrosport', 'badminton',
            'air hockey', 'biathlon', 'billard', 'escalade', 'bowling', 'BMX', 'bridge', 'cheerleading',
            'danse', 'dodgeball', 'échecs', 'équitation', 'escrime', 'futsal', 'sports de glisse',
            'triathlon', 'jiu jitsu', 'jonglerie', 'karting', 'kung fu', 'lancer', 'marathon', 'netball',
            'paintball', 'patinage', 'plongée', 'quidditch', 'saut', 'softball', 'squash', 'taekwondo',
            'tir', 'tir à l\'arc', 'ultimate', 'VTT'
        ];

        sort($sports);

        foreach ($sports as $sport) {
            DB::table('sports')->insert(
                ['name' => $sport]
            );
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports');
    }
};
