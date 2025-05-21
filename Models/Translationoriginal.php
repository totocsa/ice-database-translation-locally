<?php

namespace Totocsa\DatabaseTranslationLocally\Models;

use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Totocsa\UniqueMultiple\UniqueMultiple;

class Translationoriginal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['category', 'subtitle'];

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
            'category' => 'Category',
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

    public static function rules(Request $request)
    {
        $tableName = (new Translationoriginal())->getTable();
        $attributes = ['category', 'subtitle'];

        return [
            'category' => ['required', 'string', 'max:100', new UniqueMultiple($request, $tableName, $attributes, ['id'])],
            'subtitle' => ['required', 'string', 'max:668', new UniqueMultiple($request, $tableName, $attributes, ['id'])],
        ];
    }

    public function translationvariants(): HasMany
    {
        return $this->hasMany(Translationvariant::class, 'translationoriginals_id', 'id');
    }
}
