<?php

namespace Src\Traits;

use Src\Request;

trait Filterable
{
    protected function applyFilters($query, Request $request, array $filtersConfig)
    {
        foreach ($filtersConfig as $field => $config) {
            $value = $request->get($field);

            if ($value === null || $value === '') {
                continue;
            }

            $type = $config['type'] ?? 'equal';
            $dbField = $config['field'] ?? $field;

            switch ($type) {
                case 'like':
                    $query->where($dbField, 'like', "%{$value}%");
                    break;
                case 'equal':
                    $query->where($dbField, $value);
                    break;
                case 'relation':
                    $relation = $config['relation'];
                    $foreignKey = $config['foreign_key'] ?? $field;
                    $query->whereHas($relation, function($q) use ($foreignKey, $value) {
                        $q->where($foreignKey, $value);
                    });
                    break;
                case 'date':
                    $query->whereDate($dbField, $value);
                    break;
            }
        }

        return $query;
    }

    protected function applySorting($query, Request $request, $defaultSort = 'id', $defaultOrder = 'asc')
    {
        $sort = $request->get('sort', $defaultSort);
        $order = $request->get('order', $defaultOrder);

        $sortableFields = $this->sortableFields ?? ['id'];

        if (in_array($sort, $sortableFields) && in_array($order, ['asc', 'desc'])) {
            $query->orderBy($sort, $order);
        }

        return compact('sort', 'order');
    }

    protected function buildFilterParams(Request $request, array $filtersConfig): array
    {
        $params = [];
        foreach ($filtersConfig as $field => $config) {
            $params[$field] = $request->get($field);
        }
        return $params;
    }
}