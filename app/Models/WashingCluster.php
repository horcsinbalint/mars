<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WashingCluster extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'number_of_machines', 'user_maximum_open_reservations', 'open_days_for_reservations', 'slot_size'
    ];

    /**
     * TODO: make it more clear
     * TODO: timezones, DST ???
     */
    function getSlotStart(\DateTime $dt) {
        $midnight = (clone $dt)->setTime(0, 0);
        $time_between = $dt->getTimestamp() - $midnight->getTimestamp();
        $slot_in_day = (int) ($time_between / 60 / $this->slot_size);
        if((clone $dt)->setTime(0,($slot_in_day+1)*$this->slot_size-1)->setTime(0,0) != $midnight){
            $slot_in_day = $slot_in_day-1;
        }
        return $dt->setTime(0,$slot_in_day*$this->slot_size);
    }

    function getSlotEnd(\DateTime $dt){
        $slot_start = $this->getSlotStart($dt);
        $slot_end = (clone $slot_start)->modify("+".$this->slot_size." minutes")->modify("-1 seconds");
        return $slot_end;
    }

    function getSlotsForDay(\DateTime $dt){
        $slot_start = $dt->setTime(0, 0);
        $result = [];
        for($slot_start_minute = 0; $slot_start_minute < 24*60; $slot_start_minute += $this->slot_size){
            $slot_start = $slot_start->setTime(0, $slot_start_minute);
            $slot_end = $this->getSlotEnd($slot_start);
            $reservations = \App\Models\WashingReservation::where('starts_on', '<=', $slot_end)
                        ->where('ends_on', '>=', $slot_start)->get();
            $current_slot = new \App\Utils\WashingSlot($this, clone $slot_start, $slot_end, $reservations, $this->isActive($slot_start));
            $result[] = $current_slot;
        }
        return $result;
    }

    function isActive(\DateTime $dt){
        $slot_start = $this->getSlotStart($dt);
        $slot_end = $this->getSlotEnd($dt);
        return (new \DateTime() <= $slot_start) && ($slot_start <= (new \DateTime())->modify("+" . $this->open_days_for_reservations . " days"));
    }

    function getWashingDays(\DateTime $dt){
        $result = [];
        for($day_delta = 0; $day_delta <= $this->open_days_for_reservations; $day_delta++){
            $day = (clone $dt)->modify("+".$day_delta." days")->setTime(0,0);
            $slots = $this->getSlotsForDay($day);
            $current_day = new \App\Utils\WashingDay($this, $day, $slots);
            $result[] = $current_day;
        }
        return $result;
    }

}
