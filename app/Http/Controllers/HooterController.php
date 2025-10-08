<?php

namespace App\Http\Controllers;

use App\Models\Hoot;
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
        //Firstly validating the request 
        $validated = $request->validate(
            [
                'message' => 'required|string|max:255|min:5',
            ]
        );

        // Use the authenticated user
        auth()->user()->hoots()->create($validated);

        return redirect('/')->with('success', 'Hoot has been created!');
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
        $this -> authorize('update', $hoot);
        return view('hoots.edit', compact('hoot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hoot $hoot)
    {

        $this -> authorize('update', $hoot);

        //Firstly validating the request 
        $validated = $request->validate([
            'message' => 'required|string|max:255|min:5',
        ]);

       $hoot -> update($validated);


        return redirect('/')->with('success', 'Your hoot has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hoot $hoot)
    {
        $this -> authorize('delete', $hoot);
        $hoot->delete();
        return redirect('/')->with('success', 'Your hoot has been deleted!');
    }
}
