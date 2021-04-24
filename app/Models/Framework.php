<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Framework
 *
 * @property int $id
 * @property string $icon
 * @property string $title
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Framework newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Framework newQuery()
 * @method static \Illuminate\Database\Query\Builder|Framework onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Framework query()
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Framework whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Framework withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Framework withoutTrashed()
 * @mixin \Eloquent
 */
class Framework extends Model
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
