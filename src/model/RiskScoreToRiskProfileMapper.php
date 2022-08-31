<?php

require __DIR__ . '/RiskProfile.php';

class RiskScoreToRiskProfileMapper {
    public static function map(RiskScore $score): RiskProfile {
        return new RiskProfile(
            self::getProfileForScore($score->getAutoScore()),
            self::getProfileForScore($score->getDisabilityScore()),
            self::getProfileForScore($score->getHomeScore()),
            self::getProfileForScore($score->getLifeScore())
        );
    }

    private static function getProfileForScore(int $line_score) {
        if ($line_score <= 0) {
            return "economic";
        }

        if ($line_score === 1 || $line_score === 2) {
            return "regular";
        }

        if ($line_score >= 3) {
            return "responsible";
        }
    }


}