<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Portfolio
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property int|null $user_id
 * @property int|null $template_id
 * @property string|null $designation
 * @property string|null $skill_level
 * @property string|null $field_data
 * @property string|null $hobby_data
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PortfolioGallery[] $galleryImages
 * @property-read int|null $gallery_images_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio newQuery()
 * @method static \Illuminate\Database\Query\Builder|Portfolio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio query()
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereFieldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereHobbyData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereSkillLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Portfolio withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Portfolio withoutTrashed()
 * @mixin \Eloquent
 * @property bool $confirmed
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereConfirmed($value)
 * @property int|null $confirmed_by
 * @property string|null $confirmed_at
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereConfirmedBy($value)
 * @property string|null $section_data
 * @property string|null $layout
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portfolio whereSectionData($value)
 */
class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_photo',
        'first_name',
        'last_name',
        'name',
        'email',
        'mobile',
        'user_id',
        'slug',
        'template_id',
        'designation',
        'skill_level',
        'section_data',
        'layout',
        'active',
        'confirmed',
        'confirmed_by',
        'confirmed_at',
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
        'active' => 'boolean',
        'confirmed' => 'boolean',
    ];

    // Belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Has many gallery images
    public function galleryImages()
    {
        return $this->hasMany(PortfolioGallery::class);
    }
}
