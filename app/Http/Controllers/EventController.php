<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;    
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('type', 'asc')->get();

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        Event::create($validatedData);

        return redirect()->route('events.index');
    }
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        

        return redirect()->back()->with('success', 'Event deleted successfully.');
    } 
}
