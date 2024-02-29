<?php

namespace App\Utils;

class WashingSlot
{
    public $cluster;
    public $start_time;
    public $end_time;
    public $reservations;
    public $active;

    public function __construct($cluster, $start_time, $end_time, $reservations, $active){
        $this->cluster = $cluster;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->reservations = $reservations;
        $this->active = $active;
    }
}
