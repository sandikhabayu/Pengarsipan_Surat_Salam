<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
{
    return view('petugas.profile.index', [
        'user' => auth()->user()
    ]);
}
    public function indexKepalaSekolah()
{
    return view('kepala-sekolah.profile.index', [
        'user' => auth()->user()
    ]);
}

    public function edit(Request $request)
{
    $role = $request->user()->role;
    $routePrefix = ($role === 'kepala_sekolah') ? 'kepala-sekolah' : 'petugas';
    
    return view("{$routePrefix}.profile.edit", [
        'user' => $request->user()
    ]);
}

public function update(Request $request)
{
    $user = $request->user();
    $role = $user->role;
    $routePrefix = ($role === 'kepala_sekolah') ? 'kepala-sekolah' : 'petugas';

    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        'telepon' => ['required', 'numeric', 'min:10'],
        'alamat' => ['required', 'string', 'max:255'],
        'password' => ['nullable', 'confirmed', Password::defaults()],
        'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    ]);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->telepon = $request->telepon;
    $user->alamat = $request->alamat;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    if ($request->hasFile('profile_photo')) {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
    }

    $user->save();

    return redirect()->route("{$routePrefix}.profile.index")
        ->with('success', 'Profil berhasil diperbarui');
}
}