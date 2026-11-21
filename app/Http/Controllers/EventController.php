<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_time', 'asc')->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
            'userRole' => Auth::user()->role,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'type'        => 'required|in:Latihan,TC,Gladi,Kejuaraan,Rapat',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after_or_equal:start_time',
            'location'    => 'required|string|max:255',
        ]);

        $validatedData['qr_token'] = Str::random(32);

        Event::create($validatedData);

        return redirect()->route('events.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'type'        => 'required|in:Latihan,TC,Gladi,Kejuaraan,Rapat',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after_or_equal:start_time',
            'location'    => 'required|string|max:255',
        ]);

        $event->update($validatedData);

        return redirect()->route('events.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->back()->with('success', 'Agenda berhasil dihapus.');
    }
}
