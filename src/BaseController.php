<?php

class BaseController {
    public function processRequest(string $method, string $action): void {
        switch ($method) {
            case 'GET':
                $this->$action();
                break;
            case 'POST':
                $request_body = (array) json_decode(file_get_contents("php://input"), true);
                $this->$action($request_body);
                break;
            default:
                throw new \Exception('Unexpected method');

        }
    }

    protected function validate(array $rules, array $parameters): void {
        $errors = [];
        foreach ($rules as $key => $rule) {
            $value = $parameters[$key];
            $errors = $this->validateRequired($rule['required'], $key, $value, $errors);
            $errors = $this->validateFormat($rule['format'], $key, $value, $errors);
            $errors = $this->validateIfContains($rule['contains'], $key, $value, $errors);
            $errors = $this->validateWithCallable($rule['callable'], $value, $errors);
        }

        if (count($errors) > 0) {
            $this->sendErrorResponse($bad_request_status_code = 400, implode(', ', $errors));
        }
    }

    private function sendErrorResponse(string $status_code, string $status_message): void {
        http_response_code($status_code);
        echo json_encode([
            "status_code" => $status_code,
            "status_message" => $status_message
        ]);
        exit;
    }

    private function validateRequired(bool $required, string $key, $value, array $errors): array {
        if ($required && is_null($value)) {
            $errors[] = "{$key} is required";
        }

        return $errors;
    }

    private function validateFormat($format, string $key, $value, array $errors): array {
        if ($format == 'integer') {
            if (!is_integer($value)) {
                $errors[] = "{$key} should be an integer";
            }
        }

        if ($format == 'boolean') {
            if (!(($value === 0) || ($value === 1))) {
                $errors[] = "{$key} should only contain boolean elements";
            }
        }

        if (is_array($format) && array_key_first($format) == 'array') {
            $errors = $this->validateArrayFormat($format['array'], $key, $value, $errors);
        }

        return $errors;
    }

    private function validateArrayFormat($array_format, string $key, $value, array $errors): array {
        if (!is_array($value)) {
            $errors[] = "{$key} should be an array";
            return $errors;
        }

        if (count($value) != $array_format['size']) {
            $errors[] = "{$key} should be of size {$array_format['size']}";
            return $errors;
        }

        if (isset($array_format['keys'])) {
            $expected_keys = $array_format['keys'];
            $actual_keys = array_keys($value);

            if (array_diff($expected_keys, $actual_keys)) {
                $keys_list = implode(', ', $expected_keys);
                $errors[] = "{$key} should contain the keys {$keys_list}";
            }
        }

        foreach ($value as $item) {
            $errors = $this->validateFormat($array_format['format'], $key, $item, $errors);
        }

        return $this->validateIfContains($array_format['contains'], $key, $item, $errors);
    }

    private function validateIfContains(?array $haystack, string $key, $value, array $errors): array {
        if (isset($haystack)) {
            if (!in_array($value, $haystack)) {
                $should_contain_string = implode(', ', $haystack);
                $errors[] = "{$key} should be one of {$should_contain_string}";
            }
        }

        return $errors;
    }

    private function validateWithCallable(?callable $callable, $value, array $errors): array {
        if (isset($callable)) {
            try {
                $callable($value);
            } catch (InvalidArgumentException $exception) {
                $errors[] = $exception->getMessage();
            }
        }
        return $errors;
    }
}
