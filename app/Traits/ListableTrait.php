<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 22.07.2019
 * Time: 11:28
 */

namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

trait ListableTrait {
    /**
     * @return string
     * @throws \Exception
     */
    public function getDefaultOrderingField(): string {
        throw new \Exception('Вы должны переопределить этот метод');
        return 'fio';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDefaultOrderingDirField(): string {
        throw new \Exception('Вы должны переопределить этот метод');
        return 'asc';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getOrderingFields(): array {
        throw new \Exception('Вы должны переопределить этот метод');
        return [
            'fio' => [
                'title'  => 'ФИО',
                'handle' => function (Builder $query, $orderingDir, array $frd = null): Builder {
                    return $query->orderBy('f_name', $orderingDir)
                                 ->orderBy('l_name', $orderingDir)
                                 ->orderBy('m_name', $orderingDir);
                }
            ],
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getOrderingName(): string {
        throw new \Exception('Вы должны переопределить этот метод');
        return 'orderingList';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getOrderingDirName(): string {
        throw new \Exception('Вы должны переопределить этот метод');
        return 'orderingListDir';
    }

    /**
     * @param Builder $query
     * @param array   $frd
     * @return Paginator
     * @throws \Exception
     */
    public function scopeGetListPaginated(Builder $query, array $frd): Paginator {
        throw new \Exception('Вы должны переопределить этот метод');
        return $query->filter($frd)
                     ->ordering($frd)
                     ->with(['roles', 'avatars'])
                     ->paginate($frd['perPage'] ?? $this->getPerPage());
    }

    /**
     * @param Builder $query
     * @param array   $frd
     * @return Builder
     * @throws \Exception
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        throw new \Exception('Вы должны переопределить этот метод');
        $fillable = $this->fillable;
        foreach ($frd as $key => $value) {
            if ($value === '-')
                unset($frd[$key]);
        }
        foreach ($frd as $key => $value) {
            if ($value === null) {
                continue;
            }
            switch ($key) {
                case 'name':
                    {
                        $query->where('name', 'like', '%' . $value . '%');
                    }
                    break;
                default:
                    {
                        if (in_array($key, $fillable)) {
                            $query->where($key, $value);
                        }
                    }
                    break;
            }
        }
        return $query;
    }

    /**
     * @return array
     */
    public function getOrderingDirFields(): array {
        return ['asc', 'desc'];
    }

    /**
     * @param null $namespace
     * @return string
     */
    private function getNamespacePreOrdering($namespace = null): string {
        $configName = str_replace('\\', '.', self::class);
        $configName = mb_strtolower($configName);
        return (!empty($namespace)
                ? $namespace
                : 'listable.trait') . '.' . $configName;
    }

    /**
     * @param null $namespace
     * @return null|\stdClass
     */
    private function getCashedPreOrdering($namespace = null): ?\stdClass {
        return config($this->getNamespacePreOrdering($namespace));
    }

    /**
     * @param \stdClass $obj
     * @param null      $namespace
     */
    private function setCashedPreOrdering(\stdClass $obj, $namespace = null) {
        config([$this->getNamespacePreOrdering($namespace) => $obj]);
    }

    /**
     * @param array       $frd
     * @param string|null $name
     * @param string|null $nameDir
     * @param null        $namespace
     * @return \stdClass
     * @throws \Exception
     */
    public function preOrdering(array $frd, string $name = null, string $nameDir = null, $namespace = null): \stdClass {
        $obj = $this->getCashedPreOrdering($namespace);
        if (isset($obj)) {
            return $obj;
        }
        $obj = new \stdClass();
        if ($name === null) {
            $name = $this->getOrderingName();
        }
        if ($nameDir === null) {
            $nameDir = $this->getOrderingDirName();
        }
        if (!isset($frd[$name])) {
            $ordering = $this->getDefaultOrderingField();
        } else {
            $ordering = $frd[$name];
        }
        if (!isset($frd[$nameDir])) {
            $orderingDir = $this->getDefaultOrderingDirField();
        } else {
            $orderingDir = $frd[$nameDir];
        }
        $orderingDir = mb_strtolower($orderingDir);
        $orderingFields = $this->getOrderingFields();
        $orderingFieldsKeys = array_keys($orderingFields);
        if (!in_array($ordering, $orderingFieldsKeys)) {
            $ordering = $this->getDefaultOrderingField();
        }
        if (!in_array($orderingDir, $this->getOrderingDirFields())) {
            $orderingDir = $this->getDefaultOrderingDirField();
        }
        $obj->ordering = $ordering;
        $obj->orderingDir = $orderingDir;
        $obj->orderingFields = $orderingFields;
        $this->setCashedPreOrdering($obj, $namespace);
        return $obj;
    }

    /**
     * @param Builder     $query
     * @param array       $frd
     * @param string|null $name
     * @param string|null $nameDir
     * @param string|null $namespace
     * @return Builder
     * @throws \Exception
     */
    public function scopeOrdering(Builder $query, array $frd, string $name = null, string $nameDir = null, string $namespace = null): Builder {
        $obj = $this->preOrdering($frd, $name, $nameDir, $namespace);
        $ordering = $obj->ordering;
        $orderingDir = $obj->orderingDir;
        $orderingFields = $obj->orderingFields;
        return $orderingFields[$ordering]['handle']($query, $orderingDir, $frd);
    }

    /**
     * @param             $frd
     * @param string|null $name
     * @param string|null $nameDir
     * @param string|null $namespace
     * @return array
     * @throws \Exception
     */
    public function getOrderingSetupForFront($frd, string $name = null, string $nameDir = null, string $namespace = null): array {
        $obj = $this->preOrdering($frd, $name, $nameDir, $namespace);
        $ordering = $obj->ordering;
        $orderingDir = $obj->orderingDir;
        $orderingFields = $obj->orderingFields;
        return [
            'name'             => $this->getOrderingName(),
            'nameDir'          => $this->getOrderingDirName(),
            'valueOrdering'    => $ordering,
            'valueOrderingDir' => $orderingDir,
            'fields'           => $orderingFields
        ];
    }

    /**
     * @param             $frd
     * @param string|null $name
     * @param string|null $nameDir
     * @param string|null $namespace
     * @return array
     * @throws \Exception
     */
    public static function getOrderingSetupForFrontStatic($frd, string $name = null, string $nameDir = null, string $namespace = null): array {
        return (new self)->getOrderingSetupForFront($frd, $name, $nameDir, $namespace);
    }
}
