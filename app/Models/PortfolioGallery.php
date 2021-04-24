<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PortfolioGallery
 *
 * @property int $id
 * @property int $portfolio_id
 * @property string $image_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery query()
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery whereImageHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortfolioGallery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PortfolioGallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'portfolio_id',
        'image_hash',
    ];
}
