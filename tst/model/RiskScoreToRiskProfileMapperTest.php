<?php

require __DIR__ . '/../../src/model/RiskScoreToRiskProfileMapper.php';
require __DIR__ . '/../../src/model/RiskScore.php';

class RiskScoreToRiskProfileMapperTest extends \PHPUnit\Framework\TestCase {
    public function testMap() {
        $score = new RiskScoreForTests(0, null, 2, 3);

        $risk_profile = RiskScoreToRiskProfileMapper::map($score);

        $expected_profile = [
            "auto" => "economic",
            "disability" => "regular",
            "home" =>  "responsible",
            "life" => "ineligible",
        ];
        $this->assertEquals($expected_profile, $risk_profile->jsonSerialize());
    }
}

class RiskScoreForTests extends RiskScore {
    public function __construct(?int $auto_score, ?int $life_score, ?int $disability_score, ?int $home_score) {
        $this->auto_score = $auto_score;
        $this->life_score = $life_score;
        $this->disability_score = $disability_score;
        $this->home_score = $home_score;
    }
}