<?php

namespace App\Http\Controllers\Dormitory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Room;
use App\Models\User;
use App\Models\Role;

class WashingController extends Controller
{
    /**
     * Returns the room assignment page
     */
    public function index()
    {
        //$this->authorize('viewAny', Room::class);

        dd(\App\Models\WashingCluster::first()->getWashingDays(new \DateTime()));
    }
}
