<?php

class House {
    private string $ownership_status;

    public function __construct(string $ownership_status) {
        $this->ownership_status = $ownership_status;
    }

    public function getOwnershipStatus(): string {
        return $this->ownership_status;
    }
}