<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CompanyLogoController extends Controller
{
    /**
     * Show the company logo upload page for the authenticated user's company.
     */
    public function edit()
    {
        $user = auth()->user();
        $company = Company::find($user->c_id);

        return view('company_logo.index', compact('company', 'user'));
    }

    /**
     * Store or replace the authenticated user's company logo.
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:300',
        ]);

        $user = auth()->user();
        $company = Company::findOrFail($user->c_id);

        $dir = public_path('uploads/company-logos');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if ($company->logo) {
            $oldPath = $dir . DIRECTORY_SEPARATOR . basename($company->logo);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $file = $request->file('logo');
        $filename = 'company_' . $company->cid . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        $company->update(['logo' => $filename]);

        return redirect()->route('company.logo.edit')->with('success', 'Company logo updated successfully.');
    }

    /**
     * Remove the authenticated user's company logo and restore the default avatar.
     */
    public function destroy()
    {
        $user = auth()->user();
        $company = Company::findOrFail($user->c_id);

        if ($company->logo) {
            $oldPath = public_path('uploads/company-logos' . DIRECTORY_SEPARATOR . basename($company->logo));
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
            $company->update(['logo' => null]);
        }

        return redirect()->route('company.logo.edit')->with('success', 'Company logo removed.');
    }

    /**
     * Serve an uploaded company logo file safely regardless of webroot configuration.
     */
    public function file(string $filename)
    {
        $safeName = basename($filename);
        $path = public_path('uploads/company-logos' . DIRECTORY_SEPARATOR . $safeName);
        abort_if(!File::exists($path), 404);

        return response()->file($path);
    }
}

