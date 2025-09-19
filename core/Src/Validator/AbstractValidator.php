<?php

namespace Src\Validator;

abstract class AbstractValidator
{
    protected string $field = '';
    protected $value;
    protected array $args = [];
    protected array $messageKeys = [];
    protected string $message = '';
    protected array $customMessages = [];

    public function __construct(string $fieldName, $value, $args = [], array $customMessages = [])
    {
        $this->field = $fieldName;
        $this->value = $value;
        $this->args = $args;
        $this->customMessages = $customMessages;

        $this->messageKeys = [
            ":value" => $this->value,
            ":field" => $this->field
        ];

        // Добавляем параметры в messageKeys
        foreach ($args as $index => $arg) {
            $this->messageKeys[":arg$index"] = $arg;
        }
    }

    public function validate(): string
    {
        if (!$this->rule()) {
            return $this->messageError();
        }
        return '';
    }

    private function messageError(): string
    {
        $message = $this->getMessage();

        foreach ($this->messageKeys as $key => $value) {
            $message = str_replace($key, (string)$value, $message);
        }

        return $message;
    }

    protected function getMessage(): string
    {
        $validatorName = $this->getValidatorName();
        return $this->customMessages[$validatorName] ?? $this->message;
    }

    protected function getValidatorName(): string
    {
        $class = get_class($this);
        return strtolower(substr($class, strrpos($class, '\\') + 1, -10));
    }

    abstract public function rule(): bool;
}