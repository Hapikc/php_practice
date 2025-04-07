<?php

namespace Src\Validator;

use Model\User;
use Model\Role;
use Src\Request;

class UserValidator
{
    private array $errors = [];

    public function validate(Request $request, bool $isUpdate = false): array
    {
        $data = $request->all();
        $this->errors = [];

        // Валидация основных полей
        $this->validateField($data, 'surname', 'Фамилия', ['required', 'min:2', 'max:50']);
        $this->validateField($data, 'name', 'Имя', ['required', 'min:2', 'max:50']);
        $this->validateField($data, 'login', 'Логин', [
            'required',
            'min:3',
            'max:30',
            $isUpdate ? 'unique_except:users,login,'.$request->user_id : 'unique:users,login'
        ]);

        if (!$isUpdate || !empty($data['password'])) {
            $this->validateField($data, 'password', 'Пароль', ['required', 'min:6', 'max:100']);
        }

        $this->validateField($data, 'role_id', 'Роль', ['required', 'exists:roles,role_id']);

        // Валидация файла
        if ($request->hasFile('avatar')) {
            $this->validateFile($request->file('avatar'), 'avatar', 'Аватар', ['image', 'max_size:2048']);
        }

        if (!empty($this->errors)) {
            throw new \InvalidArgumentException(json_encode($this->errors));
        }

        return $data;
    }

    private function validateField(array $data, string $field, string $fieldName, array $rules): void
    {
        $value = $data[$field] ?? null;

        foreach ($rules as $rule) {
            if ($rule === 'required' && empty($value)) {
                $this->addError($field, "Поле {$fieldName} обязательно для заполнения");
            }

            if (strpos($rule, 'min:') === 0 && strlen($value) < substr($rule, 4)) {
                $this->addError($field, "{$fieldName} должна быть не менее ".substr($rule, 4)." символов");
            }

            if (strpos($rule, 'max:') === 0 && strlen($value) > substr($rule, 4)) {
                $this->addError($field, "{$fieldName} должна быть не более ".substr($rule, 4)." символов");
            }

            if ($rule === 'unique:users,login' && User::where('login', $value)->exists()) {
                $this->addError($field, "Пользователь с таким логином уже существует");
            }

            if (strpos($rule, 'unique_except:') === 0) {
                $parts = explode(',', substr($rule, 14));
                if (User::where('login', $value)->where('id', '!=', $parts[2])->exists()) {
                    $this->addError($field, "Пользователь с таким логином уже существует");
                }
            }

            if ($rule === 'exists:roles,role_id' && !Role::where('role_id', $value)->exists()) {
                $this->addError($field, "Выбрана несуществующая роль");
            }
        }
    }

    private function validateFile(array $file, string $field, string $fieldName, array $rules): void
    {
        foreach ($rules as $rule) {
            if ($rule === 'image') {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file['type'], $allowedTypes)) {
                    $this->addError($field, "{$fieldName} должен быть изображением (JPEG, PNG или GIF)");
                }
            }

            if (strpos($rule, 'max_size:') === 0) {
                $maxSize = substr($rule, 9) * 1024; // KB to bytes
                if ($file['size'] > $maxSize) {
                    $this->addError($field, "{$fieldName} не должен превышать ".substr($rule, 9)."KB");
                }
            }
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}