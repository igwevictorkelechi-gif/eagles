<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('app.settings', ['school' => School::findOrFail(Auth::user()->school_id)]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'short_name' => ['nullable'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable'],
            'address' => ['nullable'],
            'website' => ['nullable'],
            'logo_url' => ['nullable'],
            'primary_color' => ['nullable'],
            'secondary_color' => ['nullable'],
        ]);
        School::findOrFail(Auth::user()->school_id)->update($data);
        return redirect()->route('app.settings')->with('ok', 'Settings saved.');
    }
}
