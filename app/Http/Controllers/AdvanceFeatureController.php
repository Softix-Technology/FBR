<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class AdvanceFeatureController extends Controller
{
    /**
     * Display the advance features page for Super Admin (is_admin == 3).
     */
    public function index(Request $request)
    {
        abort_if((int) auth()->user()->is_admin !== 3, 403, 'Unauthorized access.');

        $companies = Company::orderBy('cname')->get();
        $selectedCompany = null;

        if ($request->filled('company_id')) {
            $selectedCompany = Company::find($request->input('company_id'));
        }

        return view('advance_features.index', compact('companies', 'selectedCompany'));
    }

    /**
     * Save feature permissions for the selected company.
     */
    public function update(Request $request)
    {
        abort_if((int) auth()->user()->is_admin !== 3, 403, 'Unauthorized access.');

        $request->validate([
            'company_id' => 'required|exists:companies,cid',
        ]);

        $company = Company::findOrFail($request->input('company_id'));
        $company->update([
            'can_change_password' => $request->boolean('can_change_password'),
        ]);

        return redirect()
            ->route('advance_features.index', ['company_id' => $company->cid])
            ->with('success', 'Advance features updated for ' . $company->cname . '.');
    }
}

