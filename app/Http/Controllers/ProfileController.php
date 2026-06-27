<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Services\ProfilePhotoService;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private ProfilePhotoService $profilePhotoService)
    {
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $photoWasUpdated = false;

        $uploadErrorCode = data_get($_FILES, 'profile_photo.error', UPLOAD_ERR_NO_FILE);
        if ($uploadErrorCode !== UPLOAD_ERR_NO_FILE && $uploadErrorCode !== UPLOAD_ERR_OK) {
            return Redirect::back()->withErrors([
                'profile_photo' => 'Upload foto gagal. Pastikan ukuran file tidak melebihi batas server dan coba lagi.',
            ])->withInput();
        }

        $validated = $request->validated();

        $user->fill(Arr::except($validated, ['profile_photo', 'remove_profile_photo']));

        if ($request->boolean('remove_profile_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $photoWasUpdated = true;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            try {
                $user->profile_photo_path = $this->profilePhotoService->storeOptimized($request->file('profile_photo'));
                $photoWasUpdated = true;
            } catch (\Throwable $exception) {
                return Redirect::back()->withErrors([
                    'profile_photo' => 'Foto profil tidak dapat disimpan. Silakan gunakan file lain dan coba lagi.',
                ])->withInput();
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        Auth::guard('web')->setUser($user->fresh());

        return Redirect::route('profile.edit')->with([
            'status' => 'profile-updated',
            'photo-updated' => $photoWasUpdated,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
