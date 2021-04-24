<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PreviewPortfolio
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $temp_version_id
 * @property int $portfolio_id
 * @property string $first_name
 * @property string $last_name
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property int|null $template_id
 * @property string|null $designation
 * @property string|null $skill_level
 * @property string|null $field_data
 * @property string|null $hobby_data
 * @property bool $active
 * @property bool $confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PreviewPortfolioGallery[] $galleryImages
 * @property-read int|null $gallery_images_count
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereFieldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereHobbyData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereSkillLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereTempVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereUpdatedAt($value)
 * @property string|null $section_data
 * @method static \Illuminate\Database\Eloquent\Builder|PreviewPortfolio whereSectionData($value)
 */
class PreviewPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'temp_version_id',
        'portfolio_id',
        'profile_photo',
        'first_name',
        'last_name',
        'name',
        'email',
        'mobile',
        'slug',
        'template_id',
        'designation',
        'skill_level',
        'section_data',
        'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $casts = [
        'active' => 'boolean',
        'confirmed' => 'boolean',
    ];

    // Has many gallery images
    public function galleryImages()
    {
        return $this->hasMany(PreviewPortfolioGallery::class, 'temp_version_id', 'temp_version_id');
    }
}
