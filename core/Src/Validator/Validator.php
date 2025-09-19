<?php

namespace Src\Validator;

class Validator
{
    private array $validators = [];
    private array $errors = [];
    private array $fields = [];
    private array $rules = [];
    private array $messages = [];

    public function __construct(array $fields, array $rules, array $messages = [])
    {
        $this->validators = $this->getDefaultValidators();
        $this->fields = $fields;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->validate();
    }

    private function getDefaultValidators(): array
    {
        return [
            'required' => \Src\Validator\RequiredValidator::class,
            'min' => \Src\Validator\MinValidator::class,
            'unique' => \Src\Validator\UniqueValidator::class,
            'exists' => \Src\Validator\ExistsValidator::class,
            'email' => \Src\Validator\EmailValidator::class,
            'numeric' => \Src\Validator\NumericValidator::class,
        ];
    }

    private function validate(): void
    {
        foreach ($this->rules as $fieldName => $fieldValidators) {
            if (!array_key_exists($fieldName, $this->fields)) {
                $this->fields[$fieldName] = null;
            }

            $this->validateField($fieldName, $fieldValidators);
        }
    }

    private function validateField(string $fieldName, array $fieldValidators): void
    {
        foreach ($fieldValidators as $validator) {
            $validatorParts = explode(':', $validator, 2);
            $validatorName = $validatorParts[0];
            $params = isset($validatorParts[1]) ? explode(',', $validatorParts[1]) : [];

            if (!isset($this->validators[$validatorName])) {
                continue;
            }

            $validatorClass = $this->validators[$validatorName];

            if (!class_exists($validatorClass)) {
                continue;
            }

            $validatorInstance = new $validatorClass(
                $fieldName,
                $this->fields[$fieldName] ?? null,
                $params,
                $this->messages
            );

            $error = $validatorInstance->validate();
            if (!empty($error)) {
                $this->errors[$fieldName][] = $error;
            }
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function getFirstError(): ?string
    {
        foreach ($this->errors as $errors) {
            if (!empty($errors)) {
                return $errors[0];
            }
        }
        return null;
    }

    public function getErrorsFlat(): array
    {
        $flatErrors = [];
        foreach ($this->errors as $errors) {
            foreach ($errors as $error) {
                $flatErrors[] = $error;
            }
        }
        return $flatErrors;
    }
}