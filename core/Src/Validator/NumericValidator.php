<?php

namespace Src\Validator;

class NumericValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно быть числом';

    public function rule(): bool
    {
        if (empty($this->value)) {
            return true;
        }

        return is_numeric($this->value);
    }
}