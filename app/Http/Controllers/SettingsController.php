<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the user's settings form.
     */
    public function index(Request $request): View
    {
        return view('settings.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's settings information.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validate only the fields that are not file uploads first
        $request->validate([
            'name' => ['string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'in:Motor'],
            'vehicle_plate_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle profile photo removal if requested
        if ($request->has('remove_photo') && $request->input('remove_photo') == '1') {
            // Delete the existing photo file if it exists
            if (auth()->user()->profile_photo_path) {
                $filePath = str_replace('storage/', 'public/', auth()->user()->profile_photo_path);
                if (\Storage::disk('public')->exists($filePath)) {
                    \Storage::disk('public')->delete($filePath);
                }
            }
            // Remove the photo path from the database
            $request->user()->profile_photo_path = null;
        }
        // Handle profile photo upload if present
        elseif ($request->hasFile('profile_photo')) {
            // Validate the profile photo separately
            $request->validate([
                'profile_photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            ]);

            $photo = $request->file('profile_photo');
            $filename = time() . '_' . auth()->user()->id . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('profile_photos', $filename, 'public');
            $request->user()->profile_photo_path = 'storage/profile_photos/' . $filename;
        }

        // Fill in the validated data (excluding photo which was handled separately)
        $request->user()->fill($request->only(['name', 'vehicle_type', 'vehicle_plate_number']));
        $request->user()->save();

        return Redirect::route('settings')->with('status', 'profile-updated');
    }
}
