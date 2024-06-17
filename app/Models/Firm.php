<?php

namespace Coyote;

use Coyote\Models\Asset;
use Coyote\Services\Eloquent\HasMany;
use Coyote\Services\Media\Factory as MediaFactory;
use Coyote\Services\Media\Logo;
use Coyote\Services\Media\SerializeClass;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $is_agency
 * @property int $user_id
 * @property bool $is_private
 * @property string $name
 * @property string $city
 * @property string $street
 * @property string $street_number
 * @property string $postcode
 * @property string $website
 * @property string $description
 * @property string $vat_id
 * @property int $country_id
 * @property \Coyote\Firm\Benefit[] $benefits
 * @property Asset[] $assets
 * @property Logo $logo
 * @property \Coyote\Country $country
 */
class Firm extends \Tests\Legacy\Services\Model
{
    use SoftDeletes, SerializeClass;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'website',
        'headline',
        'description',
        'employees',
        'founded',
        'is_agency',
        'country_id',
        'vat_id',
        'city',
        'street',
        'street_number',
        'postcode',
        'latitude',
        'longitude',
        'is_private',
        'youtube_url',
        'benefits',
        'country'
    ];

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:se';

    /**
     * Default fields values. Important for vue.js
     *
     * @var array
     */
    protected $attributes = [
        'is_agency' => false
    ];

    protected $casts = [
        'is_agency' => 'bool'
    ];

    /**
     * @return string[]
     */
    public static function getEmployeesList()
    {
        return [
            1 => '1-5',
            2 => '6-10',
            3 => '11-20',
            4 => '21-30',
            5 => '31-50',
            6 => '51-100',
            7 => '101-200',
            8 => '201-500',
            9 => '501-1000',
            10 => '1001-5000',
            11 => '5000+'
        ];
    }

    /**
     * @return HasMany
     */
    public function benefits()
    {
        $instance = new Firm\Benefit();

        return new HasMany($instance->newQuery(), $this, $instance->getTable() . '.' . $this->getForeignKey(), $this->getKeyName());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function assets()
    {
        return $this->morphMany(Asset::class, 'content');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @param string $name
     */
    public function setNameAttribute($name)
    {
        $name = trim($name);

        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str_slug($name, '_');
    }

    /**
     * @param string $value
     * @return \Coyote\Services\Media\MediaInterface
     */
    public function getLogoAttribute($value)
    {
        if (!($value instanceof Logo)) {
            $logo = app(MediaFactory::class)->make('logo', ['file_name' => $value]);
            $this->attributes['logo'] = $logo;
        }

        return $this->attributes['logo'];
    }

    public function setLogoAttribute($logo)
    {
        $this->attributes['logo'] = null;

        if ($logo) {
            $this->attributes['logo'] = trim(str_replace('/uploads', '', parse_url($logo, PHP_URL_PATH)), '/');
        }
    }

    public function setYoutubeUrlAttribute($value)
    {
        $this->attributes['youtube_url'] = $this->getEmbedUrl($value);
    }

    public function setBenefitsAttribute($benefits)
    {
        $benefits = array_filter(array_unique(array_map('trim', $benefits)));

        $models = [];

        foreach ($benefits as $benefit) {
            $models[] = new Firm\Benefit(['name' => $benefit]);
        }

        if ($models) {
            $this->setRelation('benefits', collect($models));
        }
    }

    /**
     * @param string|null $country
     */
    public function setCountryAttribute(?string $country)
    {
        $this->setAttribute(
            'country_id',
            $country ? (new Country())->where('name', $country)->orWhere('code', $country)->value('id') : null
        );
    }

    /**
     * @param array $attributes
     * @return $this|\Tests\Legacy\Services\Model
     */
    public function fill(array $attributes)
    {
        parent::fill($attributes);

        if ($this->is_agency) {
            foreach (['headline', 'latitude', 'longitude', 'country_id', 'street', 'city', 'street_number', 'postcode'] as $column) {
                $this->{$column} = null;
            }

            $this->benefits->flush();
        }

        return $this;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getEmbedUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        $components = parse_url($url);

        if (empty($components['query'])) {
            return $url;
        }

        parse_str($components['query'], $query);

        return 'https://www.youtube.com/embed/' . $query['v'];
    }
}
