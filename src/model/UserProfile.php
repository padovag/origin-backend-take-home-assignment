<?php

class UserProfile {
    private int $age;
    private int $dependents;
    private int $income;
    private House $house;
    private bool $is_married;
    private array $risk_questions;
    private Vehicle $vehicle;

    public function __construct(int $age, int $dependents, int $income, House $house, string $is_married, array $risk_questions, Vehicle $vehicle) {
        $this->age = $age;
        $this->dependents = $dependents;
        $this->income = $income;
        $this->house = $house;
        $this->is_married = $is_married;
        $this->risk_questions = $risk_questions;
        $this->vehicle = $vehicle;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function getDependents(): int {
        return $this->dependents;
    }

    public function getIncome(): int {
        return $this->income;
    }

    public function getHouse(): House {
        return $this->house;
    }

    public function isMarried(): bool {
        return $this->is_married;
    }

    public function getRiskQuestions(): array {
        return $this->risk_questions;
    }

    public function getVehicle(): Vehicle {
        return $this->vehicle;
    }

}