<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number_teams',
        'game_date',
        'game-time',
        'place',
        'adress',
        'postal_code',
        'city',
        'user_id',
        'championship_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class, 'championship_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'game_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_game');
    }
}