<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function addCredit(Request $request, User $user)
    {
        $this->authorize('add credit');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $user->increment('credit', $validated['amount']);

        return back()->with('success', 'Credit added successfully');
    }

    public function showProfile()
    {
        $user = auth()->user();
        $bookings = $user->bookings()->with(['showtime.movie'])->latest()->paginate(10);
        
        return view('profile', compact('user', 'bookings'));
    }

    public function showusers()
    {
        $user = auth()->user();
            if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $users = User::all();
            
        }
        
        return view('admin.roles.create', compact('users'));
       
    }


} 