<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PreviewPortfolioGallery
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $source_id
 * @property int $temp_version_id
 * @property string $image_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereImageHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereTempVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolioGallery whereUpdatedAt($value)
 */
class PreviewPortfolioGallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id',
        'temp_version_id',
        'image_hash',
    ];
}
