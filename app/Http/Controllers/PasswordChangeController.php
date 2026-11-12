<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Ensure the authenticated user belongs to a company with Change Password enabled.
     */
    private function authorizeFeature(): void
    {
        $user = auth()->user();

        if (
            !$user
            || (int) $user->is_admin !== 1
            || !$user->company
            || !$user->company->can_change_password
        ) {
            abort(403, 'Change Password feature is not enabled for your company.');
        }
    }

    /**
     * Show the change password form for the enabled company user.
     */
    public function edit()
    {
        $this->authorizeFeature();

        return view('change_password.index');
    }

    /**
     * Update the authenticated user's own login password.
     */
    public function update(Request $request)
    {
        $this->authorizeFeature();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()
            ->route('password.change.edit')
            ->with('success', 'Password changed successfully.');
    }
}

