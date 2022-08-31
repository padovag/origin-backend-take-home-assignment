<?php

class RiskProfile implements JsonSerializable {
    public const INELIGIBLE = "ineligible";
    public const ECONOMIC = "economic";
    public const REGULAR = "regular";
    public const RESPONSIBLE = "responsible";

    private string $auto;
    private string $disability;
    private string $home;
    private string $life;

    public function __construct(string $auto, string $disability, string $home, string $life) {
        $this->auto = $auto;
        $this->disability = $disability;
        $this->home = $home;
        $this->life = $life;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}