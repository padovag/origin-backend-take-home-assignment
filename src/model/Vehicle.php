<?php

class Vehicle {
    private int $year;

    public function __construct(int $year) {
        $this->year = $year;
    }

    public function getYear(): int {
        return $this->year;
    }
}