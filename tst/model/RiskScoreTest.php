<?php
require __DIR__ . '/../../src/model/UserProfile.php';
require __DIR__ . '/../../src/model/RiskScore.php';
require __DIR__ . '/../../src/model/House.php';
require __DIR__ . '/../../src/model/Vehicle.php';


class RiskScoreTest extends \PHPUnit\Framework\TestCase {

    public function testCalculate() {
        $user_profile = new UserProfile(
            $age = 35,
            $dependents = 2,
            $income = 0,
            new House('owned'),
            $is_married = true,
            $risk_questions = [1,1,1],
            new Vehicle(2018)
        );

        $expected_scores = [
            'auto_score' => 3,
            'disability_score' => null,
            'home_score' => 2,
            'life_score' => 4
        ];

        $risk_score = new RiskScore();
        $risk_score->calculate($user_profile);

        $this->assertEquals($expected_scores, $risk_score->getObjectVariables());
    }

    /**
     * @dataProvider getRuleMethods
     */
    public function testCalculateForEachRule(string $rule, $parameter, array $expected_scores) {
        $risk_score = new RiskScoreForTests($base_score = 3);
        $callable = array($risk_score, $rule);

        $callable($parameter);

        $this->assertEquals($expected_scores, $risk_score->getObjectVariables());

    }

    public function getRuleMethods(): array {
        $ineligible_user = new UserProfile(65, 0, 0, null, true, [1,1,1], null);
        return [
            //If the user doesn’t have income, vehicles or houses, she is ineligible for disability, auto, and home insurance, respectively
            //If the user is over 60 years old, she is ineligible for disability and life insurance.
            ['checkIfIneligible', $ineligible_user, ['auto_score' => null, 'disability_score' => null, 'home_score' => null, 'life_score' => null]],

            //If the user is under 30 years old, deduct 2 risk points from all lines of insurance
            ['calculateForAge', 20, ['auto_score' => 1, 'disability_score' => 1, 'home_score' => 1, 'life_score' => 1]],

            //If she is between 30 and 40 years old, deduct 1
            ['calculateForAge', 35, ['auto_score' => 2, 'disability_score' => 2, 'home_score' => 2, 'life_score' => 2]],

            //If her income is above $200k, deduct 1 risk point from all lines of insurance
            ['calculateForIncome', 250.000, ['auto_score' => 2, 'disability_score' => 2, 'home_score' => 2, 'life_score' => 2]],

            //If the user's house is mortgaged, add 1 risk point to her home score and add 1 risk point to her disability score
            ['calculateForHousingStatus', 'mortgaged', ['auto_score' => 3, 'disability_score' => 4, 'home_score' => 4, 'life_score' => 3]],

            //If the user has dependents, add 1 risk point to both the disability and life scores
            ['calculateForDependents', 1, ['auto_score' => 3, 'disability_score' => 4, 'home_score' => 3, 'life_score' => 4]],

            //If the user is married, add 1 risk point to the life score and remove 1 risk point from disability
            ['calculateForMaritalStatus', true, ['auto_score' => 3, 'disability_score' => 2, 'home_score' => 3, 'life_score' => 4]],

            //If the user's vehicle was produced in the last 5 years, add 1 risk point to that vehicle’s score
            ['calculateForVehicle', getdate()['year'], ['auto_score' => 4, 'disability_score' => 3, 'home_score' => 3, 'life_score' => 3]],
        ];
    }
}

class RiskScoreForTests extends RiskScore {
    public function __construct(int $base_score) {
        $this->auto_score = $base_score;
        $this->life_score = $base_score;
        $this->disability_score = $base_score;
        $this->home_score = $base_score;
    }

    public function calculateForAge(int $age): void {
        parent::calculateForAge($age);
    }

    public function calculateForDependents(int $dependents): void {
        parent::calculateForDependents($dependents);
    }

    public function calculateForHousingStatus(?string $house_ownership_status): void {
        parent::calculateForHousingStatus($house_ownership_status);
    }

    public function calculateForIncome(int $income): void {
        parent::calculateForIncome($income);
    }

    public function calculateForMaritalStatus(bool $is_married): void {
        parent::calculateForMaritalStatus($is_married);
    }

    public function calculateForVehicle(?int $year): void {
        parent::calculateForVehicle($year);
    }

    public function checkIfIneligible(UserProfile $userProfile): void {
        parent::checkIfIneligible($userProfile);
    }

}