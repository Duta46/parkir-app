<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
        Log::info('SettingsController@update called');
        Log::info('Auth status at start: ' . (Auth::check() ? 'logged in' : 'not logged in'));
        Log::info('Session ID at start: ' . session()->getId());
        Log::info('Has remove_photo: ' . ($request->has('remove_photo') ? 'yes' : 'no'));
        Log::info('Remove photo value: ' . ($request->input('remove_photo') ?? 'null'));

        // Validasi
        $request->validate([
            'name' => ['string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'in:Motor'],
            'vehicle_plate_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();
        Log::info('User ID: ' . $user->id);

        // Hapus foto jika diminta
        if ($request->has('remove_photo') && $request->input('remove_photo') == '1') {
            Log::info('Processing photo removal');

            if ($user->profile_photo_path) {
                Log::info('Photo path exists: ' . $user->profile_photo_path);
                $filePath = str_replace('storage/', 'public/', $user->profile_photo_path);

                if (Storage::disk('public')->exists($filePath)) {
                    Log::info('File exists, attempting to delete: ' . $filePath);
                    Storage::disk('public')->delete($filePath);
                    Log::info('File deleted successfully');
                } else {
                    Log::info('File does not exist in storage: ' . $filePath);
                }
            }

            $user->profile_photo_path = null;
            Log::info('Profile photo path set to null');
        }
        // Upload foto jika ada
        elseif ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => ['nullable', 'image', 'max:2048'],
            ]);

            $photo = $request->file('profile_photo');
            $filename = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('profile_photos', $filename, 'public');
            $user->profile_photo_path = 'storage/profile_photos/' . $filename;
        }

        // Update data lainnya
        $user->fill($request->only(['name', 'vehicle_type', 'vehicle_plate_number']));
        $user->save();
        Log::info('User saved to database');

        // Perbarui otentikasi setelah update
        if (Auth::check()) {
            Log::info('Refreshing auth session');
            Auth::setUser($user);
        }

        Log::info('Session ID at end: ' . session()->getId());
        Log::info('Auth status at end: ' . (Auth::check() ? 'logged in' : 'not logged in'));
        Log::info('SettingsController@update completed');

        return Redirect::route('settings')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            $filePath = str_replace('storage/', 'public/', $user->profile_photo_path);

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $user->profile_photo_path = null;
        $user->save();

        return Redirect::route('settings')->with('status', 'photo-deleted');
    }
}
