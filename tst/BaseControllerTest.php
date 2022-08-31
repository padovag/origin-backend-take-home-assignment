<?php
require __DIR__ . '/../src/BaseController.php';

class BaseControllerTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider getRequests
     */
    public function testProcessRequests(string $method, string $action, bool $success, $expected_response) {
        $controller = new BaseControllerForTests();

        if (!$success) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage($expected_response);
        }

        $controller->processRequest($method, $action);

        if ($success) {
            $this->assertSuccessResponse($expected_response);
        }
    }

    public function getRequests(): array {
        return [
            ['GET', 'get', true, "{\"id\":1234}"],
            ['POST', 'post', true, '{}'],
            ['UNKNOWN', 'get', false, 'Unexpected method']
        ];
    }

    /**
     * @dataProvider getRulesAndParametersToValidate
     */
    public function testValidate(array $rules, array $parameters, bool $success, ?string $message_response) {
        $controller = new BaseControllerForTests();

        $controller->validate($rules, $parameters);

        if ($success) {
            $this->expectNotToPerformAssertions();
            return;
        }

        $this->assertErrorResponse(400, $message_response);
    }

    public function getRulesAndParametersToValidate(): array {
        return [
            // rule                                                           parameters                          success   message
            [['required_rule' => ['required' => true]],                       ['required_rule' => 'valid'],       true,     null                                             ],
            [['required_rule' => ['required' => true]],                       [],                                 false,    "required_rule is required"                      ],
            [['integer_rule' => ['format' => 'integer']],                     ['integer_rule' => 'invalid'],      false,    "integer_rule should be an integer"              ],
            [['bool_rule' => ['format' => 'boolean']],                        ['bool_rule' => 'invalid'],         false,    "bool_rule should only contain boolean elements" ],
            [['contains_rule' => ['contains' => ['option1', 'option2']]],     ['contains_rule' => 'option3'],     false,    "contains_rule should be one of option1, option2"],
            [['contains_rule' => ['contains' => ['option1', 'option2']]],     ['contains_rule' => 'option2'],     true,     null                                             ],
            [
                //rule
                ['array_rule' => ['format' => ['array' => ['format' => 'integer', 'size' => 3]]]],
                //parameter
                ['array_rule' => 'invalid'],
                //success
                false,
                //message
                "array_rule should be an array"
            ],
            [
                //rule
                ['array_rule_invalid_size' => ['format' => ['array' => ['format' => 'integer', 'size' => 3]]]],
                //parameter
                ['array_rule_invalid_size' => [5]],
                //success
                false,
                //message
                "array_rule_invalid_size should be of size 3"
            ],
            [
                //rule
                ['array_rule_invalid_key' => ['format' => ['array' => ['format' => 'integer', 'size' => 1, 'keys' => ['array_key']]]]],
                //parameter
                ['array_rule_invalid_key' => [555]],
                //success
                false,
                //message
                "array_rule_invalid_key should contain the keys array_key"
            ],
        ];
    }

    private function assertSuccessResponse(string $expected_response) {
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($expected_response, $this->getActualOutput());
    }

    private function assertErrorResponse(string $status_code, string $status_message) {
        $response = json_encode([
            "status_code" => $status_code,
            "status_message" => $status_message
        ]);

        $this->assertEquals($status_code, http_response_code());
        $this->assertEquals($response, $this->getActualOutput());
    }

}

class BaseControllerForTests extends BaseController {
    public function get() {
        $this->sendSuccessResponse((object) ["id" => 1234]);
    }

    public function post(array $request) {
        $this->sendSuccessResponse((object) $request);
    }

    public function validate(array $rules, array $parameters): void {
        parent::validate($rules, $parameters);
    }

    protected function exitRequest(): void {
        // no op
    }
}
