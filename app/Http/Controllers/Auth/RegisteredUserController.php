<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Jurusan;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'jurusanOptions' => Jurusan::labels(),
            'angkatanOptions' => $this->availableAngkatanOptions(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:20'],
            'class' => ['required', 'string', 'in:' . implode(',', Jurusan::values())],
            'angkatan' => ['required', 'integer', 'digits:4', 'min:2020', 'max:' . (now()->year + 1)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'npm' => $request->npm,
            'class' => $request->class,
            'angkatan' => $request->angkatan,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Generate a reasonable range of angkatan years for the dropdown.
     */
    private function availableAngkatanOptions(): array
    {
        $currentYear = (int) now()->year;

        return range($currentYear + 1, $currentYear - 5);
    }
}