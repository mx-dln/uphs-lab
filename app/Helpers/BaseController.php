<?php

namespace App\Helpers;

abstract class BaseController
{
    protected function view(string $view, array $data = []): void
    {
        view($view, $data);
    }

    protected function redirect(string $path): void
    {
        redirect($path);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $ruleList = is_array($ruleSet) ? $ruleSet : explode('|', $ruleSet);
            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }
                $value = $data[$field] ?? '';

                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "{$field} is required.";
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "{$field} must be a valid email.";
                        }
                        break;
                    case 'min':
                        if (strlen($value) < (int)($params[0] ?? 0)) {
                            $errors[$field][] = "{$field} must be at least {$params[0]} characters.";
                        }
                        break;
                    case 'max':
                        if (strlen($value) > (int)($params[0] ?? 0)) {
                            $errors[$field][] = "{$field} must not exceed {$params[0]} characters.";
                        }
                        break;
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "{$field} must be numeric.";
                        }
                        break;
                }
            }
        }
        return $errors;
    }

    protected function flash(string $key, string $value): void
    {
        session_flash($key, $value);
    }

    protected function old(string $key, $default = null)
    {
        return old($key, $default);
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
