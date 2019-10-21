<?php

namespace model;

class RMCalcData
{
    public $weight;
    public $reps;


    public function __construct(string $weight, string $reps)
    {
        $this->weight = $weight;
        $this->reps = $reps;
    }
}
