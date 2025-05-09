<?php

namespace Totocsa\DatabaseTranslationLocally\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Translationvariant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['locales_id', 'translationoriginals_id', 'subtitle'];

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
            'locales_id' => 'Config name',
            'translationoriginals_id' => 'Eredeti',
            'subtitle' => 'Subtitle',
        ];

        return parent::columnsAndlabels($labels);
    }

    public static function getLabel($field)
    {
        $labels = self::labels();
        $label = isset($labels['$field']) ? $labels['$field'] : $field;

        return $label;
    }

    public static function rules(/*Request $request*/): array
    {
        return [
            'locales_id' => ['required', 'integer',/* $uniqueLocale_idTranslationoriginal_idSubtitle*/],
            'translationoriginals_id' => ['required', 'integer', /*$uniqueLocale_idTranslationoriginal_idSubtitle*/],
            'subtitle' => ['string', 'max:1000', /*$uniqueLocale_idTranslationoriginal_idSubtitle*/],
        ];
    }

    public function rel_locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locales_id', 'id');
    }

    public function rel_translationoriginal(): BelongsTo
    {
        return $this->belongsTo(Translationoriginal::class, 'translationoriginals_id', 'id');
    }
}
