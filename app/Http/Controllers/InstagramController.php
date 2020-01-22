<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instagram;

class InstagramController extends Controller
{
    public function edit(Request $request)
    {
        $instagram = Instagram::first();

        return view('instagram', ['instagram' => $instagram]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'session_id' => 'required',
            'query_hash' => 'required',
        ]);

        $instagram = Instagram::first();
        $instagram->session_id = $request->session_id;
        $instagram->query_hash = $request->query_hash;
        $instagram->save();

        return redirect('instagram')->with('status', 'Instagram authentication updated.');
    }
}
