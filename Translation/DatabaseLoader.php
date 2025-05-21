<?php

namespace Totocsa\DatabaseTranslationLocally\Translation;

use Illuminate\Translation\FileLoader;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Totocsa\DatabaseTranslationLocally\Models\Locale;
use Totocsa\DatabaseTranslationLocally\Models\Translationoriginal;
use Totocsa\DatabaseTranslationLocally\Models\Translationvariant;

class DatabaseLoader extends FileLoader implements Loader
{
    const _appPrefix = 'app';

    public $likeOrIlike;
    public $localesTableName;
    public $originalTableName;
    public $translationTableName;

    public function __construct(Filesystem $files, array|string $path)
    {
        parent::__construct($files, $path);

        $this->likeOrIlike = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';

        $this->localesTableName = (new Locale())->getTable();
        $this->originalTableName = (new Translationoriginal())->getTable();
        $this->translationTableName = (new Translationvariant())->getTable();
    }

    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return parent::load($locale, $group, $namespace);
        }

        if (is_null($namespace) || $namespace === '*') {
            $isGroup = Translationoriginal::query()
                ->where('category', $this->likeOrIlike, $this::_appPrefix . ".$group%")
                ->exists();

            if (is_null($group) || $group === '*' || $isGroup) {
                return $this->loadAllGroupAllNamespace($locale);
            } else {
                return parent::load($locale, $group, $namespace);
            }
        }

        //return $this->loadNamespaced($locale, $group, $namespace);
        return parent::load($locale, $group, $namespace);
    }

    public function loadAllGroupAllNamespace($locale): array
    {
        $prefix = $this::_appPrefix . '.validation';

        $results = DB::query()
            ->select(['t0.category', 't0.subtitle as original', 't1.subtitle as translation'])
            ->from($this->originalTableName, 't0')
            ->leftJoin("$this->translationTableName as t1", 't1.translationoriginals_id', '=', 't0.id')
            ->leftJoin("$this->localesTableName as t2", 't2.id', '=', 't1.locales_id')
            ->where('category', $this->likeOrIlike, "$prefix%")
            ->where('t2.configname', $locale)
            ->get()->toArray();

        $translations = $this->buidArray($results, $prefix);
        return $translations;
    }

    public function buidArray(array $items, string $prefix): array
    {
        $translations = [];
        foreach ($items as $m) {
            $key = substr($m->category, strlen($prefix) + 1);

            $pos = strpos($key, '.');
            if ($pos === false) {
                $translations[$key] = $m->translation ?: $m->original;
            } else {
                //$node = $this->bulidNode('a.b.c.d.e.f.g.h.i.j', 'érték');
                $v = $m->translation ?: $m->original;
                $node = $this->bulidNode($key, $v);
                $translations = array_merge_recursive($translations, $node);
            }
        }

        if (!isset($translations['attributes'])) {
            $translations['attributes'] = [];
        }

        return $translations;
    }

    public function bulidNode($key, $value,  &$node = [])
    {
        $keyArray = explode('.', $key);
        if (count($node) === 0) {
            $node = [last($keyArray) => $value];
        } else {
            $node = [last($keyArray) => $node];
        }

        array_pop($keyArray);
        if (count($keyArray) !== 0) {
            $k = implode('.', $keyArray);
            $this->bulidNode($k, $value, $node);
        }

        return $node;
    }

    public function refreshAll($locale = 'en')
    {
        foreach ($this->paths as $v) {
            $p = $v . DIRECTORY_SEPARATOR . $locale;
            if (is_dir($p)) {
                $iterator = new \DirectoryIterator($p);
                while ($iterator->valid()) {
                    $current = $iterator->current();

                    if ($current->isFile()) {
                        $fileName = $current->getFilename();
                        $extension = $current->getExtension();

                        if ($extension === 'php') {
                            $groupName = substr($fileName, 0, -4);
                            $this->refresh($groupName);
                        }
                    }

                    $iterator->next();
                }
            }
        }

        return;
    }

    public function refresh($group)
    {
        $source = parent::load('en', $group, '*');

        $items = $this->getItems($source, $this::_appPrefix . ".$group");

        try {
            DB::beginTransaction();
            Translationoriginal::upsert($items['originals'], uniqueBy: ['category', 'subtitle']);

            $locales_id = Locale::query()->where('configname', 'en')->first()->id;
            foreach ($items['translations'] as $k => $v) {
                $original = Translationoriginal::query()
                    ->where('category', $items['originals'][$k]['category'])
                    ->where('subtitle', $items['originals'][$k]['subtitle'])
                    ->first();

                $variant = Translationvariant::query()
                    ->where('locales_id', $locales_id)
                    ->where('translationoriginals_id', $original->id)
                    ->first();

                $variant->subtitle = $v['subtitle'];
                $variant->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            return $th;
        }

        return true;
    }

    private function getItems($source, $category)
    {
        $originals = [];
        $translations = [];
        foreach ($source as $k => $v) {
            if (is_array($v)) {
                $it = $this->getItems($v, "$category.$k");
                $originals = array_merge($originals, $it['originals']);
                $translations = array_merge($translations, $it['translations']);
            } else {
                $originals[] =  [
                    'category' => "$category.$k",
                    'subtitle' => $v,
                ];

                $translations[] = [
                    'subtitle' => $v,
                ];
            }
        }

        return ['originals' => $originals, 'translations' => $translations];
    }
}
