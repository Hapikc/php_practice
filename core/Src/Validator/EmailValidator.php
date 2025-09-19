<?php

namespace Src\Validator;

class EmailValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно содержать корректный email адрес';

    public function rule(): bool
    {
        if (empty($this->value)) {
            return true;
        }

        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }
}