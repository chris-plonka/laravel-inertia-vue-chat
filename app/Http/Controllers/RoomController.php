<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\RoomResource;

class RoomController extends Controller
{
    public function show(Room $room)
    {
        return Inertia::render('Room/Show', [
            'room' => RoomResource::make($room),
        ]);
    }
}
