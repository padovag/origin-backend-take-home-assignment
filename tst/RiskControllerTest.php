<?php
require __DIR__ . '/../src/RiskController.php';

class RiskControllerTest extends \PHPUnit\Framework\TestCase {
    public function testCalculate() {
        $controller = new RiskControllerForTests();
        $parameters = [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ["ownership_status"=> "owned"],
            "income"=> 0,
            "marital_status"=> "married",
            "risk_questions"=> [0, 1, 0],
            "vehicle"=> ["year"=> 2018]
        ];

        $controller->calculate($parameters);

        $expected_response = json_encode([
            "auto" => "regular",
            "disability" => "ineligible",
            "home" => "economic",
            "life" => "regular"
        ]);
        $this->assertSuccessResponse($expected_response);
    }

    public function getInvalidParametersTestCases(): array {
        return [
            // parameters, expected message, expects_other_exceptions
            [[], "age is required, age should be an integer, dependents is required, dependents should be an integer, income is required, income should be an integer, marital_status is required, marital_status should be one of single, married, risk_questions is required, risk_questions should be an array, house should be an array, vehicle should be an array", true],
            [$this->getInvalidFormatParameters(), "age should be an integer, dependents should be an integer, income should be an integer, marital_status should be one of single, married, risk_questions should be an array, house should be an array, vehicle should be an array", true],
            [$this->getInvalidMaritalStatus(), "marital_status should be one of single, married", false],
            [$this->getInvalidArraySizedRiskQuestionsParameters(), "risk_questions should be of size 3", false],
            [$this->getInvalidFormatHouseParameter(), "house should be an array", false],
            [$this->getInvalidKeysHouseParameter(), "house should contain the keys ownership_status", false],
            [$this->getInvalidFormatVehicleParameter(), "vehicle should be an array", false],
            [$this->getInvalidKeysVehicleParameter(), "vehicle should contain the keys year", false],
        ];
    }

    /**
     * @dataProvider getInvalidParametersTestCases
     */
    public function testCalculateWithInvalidParameters(array $parameters, string $expected_message, bool $expects_other_exceptions) {
        $controller = new RiskControllerForTests();

        $this->expectOutputRegex(json_encode([
            "status_code" => "400",
            "status_message" => $expected_message
        ]));

        if ($expects_other_exceptions) {
            $this->expectException(TypeError::class); // thrown because the exit was ovewritten for the testing purposes
        }

        $controller->calculate($parameters);
    }

    private function getInvalidFormatParameters(): array {
        return [
            "age"=> '35',
            "dependents"=> '2',
            "house"=> 'owned',
            "income"=> '0',
            "marital_status"=> 1234,
            "risk_questions"=> '0,1,0',
            "vehicle"=> '2018'
        ];
    }

    private function getInvalidMaritalStatus(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ["ownership_status"=> "owned"],
            "income"=> 0,
            "marital_status"=> "illegal_value",
            "risk_questions"=> [0, 1, 0],
            "vehicle"=> ["year"=> 2018]
        ];
    }

    private function getInvalidArraySizedRiskQuestionsParameters(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ["ownership_status"=> "owned"],
            "income"=> 0,
            "marital_status"=> "single",
            "risk_questions"=> [0],
            "vehicle"=> ["year"=> 2018]
        ];
    }

    private function getInvalidFormatHouseParameter(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> "owned",
            "income"=> 0,
            "marital_status"=> "single",
            "risk_questions"=> [1,1,0],
            "vehicle"=> ["year"=> 2018]
        ];
    }

    private function getInvalidKeysHouseParameter(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ["owned"],
            "income"=> 0,
            "marital_status"=> "single",
            "risk_questions"=> [1,1,0],
            "vehicle"=> ["year"=> 2018]
        ];
    }

    private function getInvalidFormatVehicleParameter(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ['ownership_status'=> 'owned'],
            "income"=> 0,
            "marital_status"=> "single",
            "risk_questions"=> [1,1,0],
            "vehicle"=> 2018
        ];
    }

    private function getInvalidKeysVehicleParameter(): array {
        return [
            "age"=> 35,
            "dependents"=> 2,
            "house"=> ['ownership_status'=> 'owned'],
            "income"=> 0,
            "marital_status"=> "single",
            "risk_questions"=> [1,1,0],
            "vehicle"=> [2018]
        ];
    }

    private function assertSuccessResponse(string $expected_response) {
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($expected_response, $this->getActualOutput());
    }
}

class RiskControllerForTests extends RiskController {
    protected function exitRequest(): void {
        //overwriting this method so we can test the outputs
    }
}