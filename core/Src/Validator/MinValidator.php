<?php

namespace Src\Validator;

class MinValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно содержать не менее :arg0 символов';

    public function rule(): bool
    {
        if (empty($this->value)) {
            return true;
        }

        $minLength = (int)($this->args[0] ?? 0);
        return strlen($this->value) >= $minLength;
    }
}