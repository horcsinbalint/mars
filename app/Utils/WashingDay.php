<?php

namespace App\Utils;

class WashingDay
{
    public $start_time;
    public $slots;

    public function __construct($cluster, $start_time, $slots){
        $this->cluster = $cluster;
        $this->start_time = $start_time;
        $this->slots = $slots;
    }
}
