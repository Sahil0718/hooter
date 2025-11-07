<?php

namespace App\Http\Controllers;

use App\Models\Hoot;
use App\Events\HootCreated;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HooterController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hoots = Hoot::with('user')
            ->latest()
            ->take(50)
            ->get();
        return view('home', ['hoots' => $hoots]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5|max:255',
        ]);

        // If user is logged in
        if (auth()->check()) {
            $hoot = auth()->user()->hoots()->create($validated);
            // Broadcast creation
            event(new HootCreated($hoot));
        } else {
            // If guest, create hoot manually and mark anonymous
            $hoot = \App\Models\Hoot::create([
                'message' => $validated['message'],
                'user_id' => null,
                'is_anonymous' => true,
            ]);
            event(new HootCreated($hoot));
        }

        return redirect()->back()->with('success', 'Hoot posted successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hoot $hoot)
    {
        $this->authorize('update', $hoot);
        return view('hoots.edit', compact('hoot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hoot $hoot)
    {

        $this->authorize('update', $hoot);

        //Firstly validating the request 
        $validated = $request->validate([
            'message' => 'required|string|max:255|min:5',
        ]);

        $hoot->update($validated);


        return redirect('/')->with('success', 'Your hoot has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hoot $hoot)
    {
        $this->authorize('delete', $hoot);
        $hoot->delete();
        return redirect('/')->with('success', 'Your hoot has been deleted!');
    }
}
