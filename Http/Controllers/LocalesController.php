<?php

namespace Totocsa\DatabaseTranslationLocally\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Totocsa\Icseusd\Http\Controllers\IcseusdController;
use Totocsa\DatabaseTranslationLocally\Models\Locale;

class LocalesController extends IcseusdController
{
    public $modelClassName = Locale::class;

    public $sort = [
        'field' => 'locales-configname',
        'direction' => 'asc',
    ];

    public $orders = [
        'index' => [
            'filters' => ['locales-configname', 'locales-flag', 'locales-enabled', 'locales-name', 'locales-script', 'locales-native', 'locales-regional', 'locales-flag'],
            'sorts' => [
                'locales-configname' => ['locales-configname', 'locales-enabled', 'locales-name', 'locales-script', 'locales-native', 'locales-regional'],
                'locales-enabled' => ['locales-enabled', 'locales-configname',  'locales-name', 'locales-script', 'locales-native', 'locales-regional'],
                'locales-name' => ['locales-name', 'locales-configname', 'locales-enabled',   'locales-script', 'locales-native', 'locales-regional'],
                'locales-script' => ['locales-script', 'locales-configname', 'locales-enabled', 'locales-name',  'locales-native', 'locales-regional'],
                'locales-native' => ['locales-native', 'locales-configname', 'locales-enabled', 'locales-name', 'locales-script', 'locales-regional'],
                'locales-regional' => ['locales-regional', 'locales-configname', 'locales-enabled',  'locales-name', 'locales-script', 'locales-native'],
            ],
            'item' => [
                'fields' => ['locales-configname', 'locales-enabled', 'locales-name', 'locales-script', 'locales-native', 'locales-regional', 'locales-flag'],
            ],
            'itemButtons' => ['show', 'edit', 'destroy'],
        ],
        'form' => [
            'item' => [
                'fields' => ['locales-configname', 'locales-enabled', 'locales-name', 'locales-script', 'locales-native', 'locales-regional', 'locales-flag'],
            ],
        ],
        'show' => [
            'item' => [
                'fields' => ['locales-configname', 'locales-enabled', 'locales-name', 'locales-script', 'locales-native', 'locales-regional', 'locales-flag'],
            ],
        ],
    ];

    public $filters = [
        'locales-configname' => '',
        'locales-enabled' => '',
        'locales-name' => '',
        'locales-script' => '',
        'locales-native' => '',
        'locales-regional' => '',
        'locales-flag' => '',
    ];

    public $conditions = [
        'locales-configname' => [
            'operator' => 'ilike',
            'value' => "%{{locales-configname}}%",
            'boolean' => 'and',
        ],
        'locales-enabled' => [
            'operator' => 'ilike',
            'value' => "%{{locales-enabled}}%",
            'boolean' => 'and',
        ],
        'locales-name' => [
            'operator' => 'ilike',
            'value' => "%{{locales-name}}%",
            'boolean' => 'and',
        ],
        'locales-script' => [
            'operator' => 'ilike',
            'value' => "%{{locales-script}}%",
            'boolean' => 'and',
        ],
        'locales-native' => [
            'operator' => 'ilike',
            'value' => "%{{locales-native}}%",
            'boolean' => 'and',
        ],
        'locales-regional' => [
            'operator' => 'ilike',
            'value' => "%{{locales-regional}}%",
            'boolean' => 'and',
        ],
        'locales-flag' => [
            'operator' => 'ilike',
            'value' => "%{{locales-flag}}%",
            'boolean' => 'and',
        ],
    ];

    public function fields()
    {
        return [
            'locales-configname' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-enabled' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'checkbox',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-name' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-script' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-native' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-regional' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'locales-flag' => [
                'form' => [
                    'tag' => 'flag',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ];
    }

    public function indexQuery(): LengthAwarePaginator
    {
        $t0 = 'locales';

        $query = $this->modelClassName::query()
            ->select([
                "$t0.id",
                "$t0.configname as $t0-configname",
                "$t0.enabled as $t0-enabled",
                "$t0.name as $t0-name",
                "$t0.script as $t0-script",
                "$t0.native as $t0-native",
                "$t0.regional as $t0-regional",
                "$t0.flag as $t0-flag",
            ]);

        foreach ($this->conditions as $k => $v) {
            if ($this->filters[$k] > 0) {
                $cond = $this->conditions[$k];
                $value = strtr($cond['value'], $this->replaceFieldToValue());
                $query->where(str_replace('-', '.', $k), $cond['operator'], $value, $cond['boolean']);
            }
        }

        foreach ($this->orders['index']['sorts'][$this->sort['field']] as $v) {
            $query->orderBy($v, $this->sort['direction']);
        }

        $results = $query->paginate($this->paging['per_page'], ['*'], null, $this->paging['page']);

        return $results;
    }
}
