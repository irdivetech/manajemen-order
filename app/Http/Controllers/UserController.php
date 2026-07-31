<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::latest()->get();

        return view(isMobile() ? 'users.mobile.index' : 'users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view(isMobile() ? 'users.mobile.create' : 'users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load('orders');

        return view(isMobile() ? 'users.mobile.show' : 'users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view(isMobile() ? 'users.mobile.edit' : 'users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Update authenticated user profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        Auth::user()->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update authenticated user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        if (! Hash::check($data['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        Auth::user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
