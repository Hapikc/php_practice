<?php

namespace Src\Validator;

use Illuminate\Database\Capsule\Manager as Capsule;

class UniqueValidator extends AbstractValidator
{
    protected string $message = 'Значение поля :field уже существует';

    public function rule(): bool
    {
        if (empty($this->value)) {
            return true;
        }

        $table = $this->args[0] ?? '';
        $column = $this->args[1] ?? $this->field;
        $ignoreId = $this->args[2] ?? null;

        $query = Capsule::table($table)->where($column, $this->value);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->count() === 0;
    }
}