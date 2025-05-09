<?php

namespace Totocsa\DatabaseTranslationLocally\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['configname', 'enabled'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        //
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            //
        ];
    }

    public static function labels(): array
    {
        $labels = [
            'configname' => 'Config name',
            'enabled' => 'Enabled',
        ];

        return parent::columnsAndlabels($labels);
    }

    public static function getLabel($field)
    {
        $labels = self::labels();
        $label = isset($labels['$field']) ? $labels['$field'] : $field;

        return $label;
    }

    public static function rules($id = null): array
    {
        return [
            'configname' => ['required', 'string', 'max:100', Rule::unique('locales')->ignore($id)],
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function translationvariants(): HasMany
    {
        return $this->hasMany(Translationvariant::class, 'locales_id', 'id');
    }
}
