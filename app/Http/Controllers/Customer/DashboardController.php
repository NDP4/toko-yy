<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User */
        $user = Auth::user();
        return view('customer.dashboard', compact('user'));
    }

    public function profile(): View
    {
        /** @var User */
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }

    public function orders(): View
    {
        /** @var User */
        $user = Auth::user();
        $orders = $user->orders()->latest()->paginate(10);

        return view('customer.orders', compact('orders'));
    }
}
