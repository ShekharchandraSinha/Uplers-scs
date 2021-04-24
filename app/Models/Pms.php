<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Pms
 *
 * @property int $id
 * @property string $icon
 * @property string $title
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Pms newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pms newQuery()
 * @method static \Illuminate\Database\Query\Builder|Pms onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pms query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pms whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Pms withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Pms withoutTrashed()
 * @mixin \Eloquent
 */
class Pms extends Model
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
