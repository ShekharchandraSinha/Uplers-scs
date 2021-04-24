<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Esp
 *
 * @property int $id
 * @property string $icon
 * @property string $title
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Esp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Esp newQuery()
 * @method static \Illuminate\Database\Query\Builder|Esp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Esp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Esp withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Esp withoutTrashed()
 * @mixin \Eloquent
 */
class Esp extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'icon',
        'title',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean'
    ];
}
