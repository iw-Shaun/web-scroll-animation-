<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User resource",
 *     @OA\Property(property="id", type="integer", description="User ID", example=1),
 *     @OA\Property(property="line_id", type="string", description="Line ID", example=""),
 *     @OA\Property(property="name", type="string", description="Line nickname", example="isweety"),
 *     @OA\Property(property="loggedin_at", type="string", format="date-time", description="User log in date", example="2023-04-19 12:00:00"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="User creation date", example="2023-04-19 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="User last update date", example="2023-04-19 12:00:00"),
 *     @OA\Property(property="is_bind_oa", type="tinyint", description="Line OA binding", example="1"),
 *     @OA\Property(property="score", type="int", description="game score (highest)", example="1000"),
 *     @OA\Property(property="last_score", type="int", description="game score (last time)", example="800"),
 *     @OA\Property(property="remain_rounds", type="int", description="remain rounds user could start game", example="1"),
 *     @OA\Property(property="history_rounds", type="int", description="total rounds user started game", example="1"),
 *     @OA\Property(property="last_played_time", type="string", format="date-time", description="User last played date", example="2023-04-19 12:00:00")
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
