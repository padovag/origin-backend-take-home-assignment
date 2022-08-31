<?php

class RiskScore {
    private int $auto_score;
    private int $disability_score;
    private int $home_score;
    private int $life_score;

    public function calculate(UserProfile $user_profile): self {
        $this->initializeWithBaseScore($user_profile->getRiskQuestions());

        $this->calculateForAge($user_profile->getAge());
        $this->calculateForIncome($user_profile->getIncome());
        $this->calculateForHousingStatus($user_profile->getHouse()->getOwnershipStatus());
        $this->calculateForDependents($user_profile->getDependents());
        $this->calculateForMaritalStatus($user_profile->isMarried());
        $this->calculateForVehicle($user_profile->getVehicle()->getYear());

        return $this;
    }

    private function calculateForAge(int $age): void {
        if ($age < 30) {
            $this->deductFromAllInsuranceLines(2);
        }

        if ($age > 30 && $age < 40) {
            $this->deductFromAllInsuranceLines(1);
        }
    }

    private function calculateForIncome(int $income): void {
        if ($income > 200.000) {
            $this->deductFromAllInsuranceLines(1);
        }
    }

    private function calculateForHousingStatus(string $house_ownership_status): void {
        if ($house_ownership_status === 'mortgaged') {
            $this->addToHomeScore(1);
            $this->addToDisabilityScore(1);
        }
    }

    private function calculateForDependents(int $dependents): void {
        if ($dependents > 0) {
            $this->addToDisabilityScore(1);
            $this->addToLifeScore(1);
        }
    }

    private function calculateForMaritalStatus(bool $is_married): void {
        if ($is_married) {
            $this->addToLifeScore(1);
            $this->deductFromDisabilityLine(1);
        }
    }

    private function calculateForVehicle(int $year): void {
        $was_produced_in_the_last_5_years = $year > (getdate()['year'] - 5);
        if ($was_produced_in_the_last_5_years) {
            $this->addToAutoScore(1);
        }
    }

    private function initializeWithBaseScore($risk_questions): void {
        $base_score = array_sum($risk_questions);

        $this->auto_score = $base_score;
        $this->disability_score = $base_score;
        $this->home_score = $base_score;
        $this->life_score = $base_score;
    }

    private function deductFromAllInsuranceLines(int $point): void{
        $this->deductFromAutoLine($point);
        $this->deductFromDisabilityLine($point);
        $this->deductFromHomeLine($point);
        $this->deductFromLifeLine($point);
    }

    private function deductFromAutoLine(int $point): void {
        $this->auto_score -= $point;
    }

    private function deductFromDisabilityLine(int $point): void {
        $this->disability_score -= $point;
    }

    private function deductFromHomeLine(int $point): void {
        $this->home_score -= $point;
    }

    private function deductFromLifeLine(int $point): void {
        $this->life_score -= $point;
    }

    private function addToHomeScore(int $point): void {
        $this->home_score += $point;
    }

    private function addToDisabilityScore(int $point): void {
        $this->disability_score += $point;
    }

    private function addToLifeScore(int $point): void {
        $this->life_score += $point;
    }

    private function addToAutoScore(int $point): void {
        $this->auto_score += $point;
    }

    public function getLifeScore(): int {
        return $this->life_score;
    }

    public function getAutoScore(): int {
        return $this->auto_score;
    }

    public function getDisabilityScore(): int {
        return $this->disability_score;
    }

    public function getHomeScore(): int {
        return $this->home_score;
    }

}