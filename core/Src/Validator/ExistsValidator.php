<?php

namespace Src\Validator;

use Illuminate\Database\Capsule\Manager as Capsule;

class ExistsValidator extends AbstractValidator
{
    protected string $message = 'Указанное значение в поле :field не существует';

    public function rule(): bool
    {
        if (empty($this->value)) {
            return true;
        }

        $table = $this->args[0] ?? '';
        $column = $this->args[1] ?? 'id';

        return Capsule::table($table)->where($column, $this->value)->exists();
    }
}