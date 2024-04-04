<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'championship_id'
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class, 'championship_id');
    }

    public function players()
    {
        return $this->belongsToMany(User::class, 'user_team');
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'team_game');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'result_id');
    }
}