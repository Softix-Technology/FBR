@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Company Logo</h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $currentLogoUrl = ($company && $company->logo)
                            ? route('company.logo.file', ['filename' => $company->logo])
                            : asset('assets/images/users/avatar-1.jpg');
                    @endphp

                    <div class="mb-4 text-center">
                        <img id="logoPreview"
                             src="{{ $currentLogoUrl }}"
                             alt="{{ $company->cname ?? $user->name ?? 'Company Logo' }}"
                             class="rounded-circle shadow-sm border border-2"
                             style="width: 96px; height: 96px; object-fit: cover;">
                        <div class="mt-2 fw-semibold">{{ $user->business_name ?? $user->name ?? $company->cname ?? '' }}</div>
                    </div>

                    <form action="{{ route('company.logo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="logo" class="form-label">Select Logo Image (JPG, PNG, WEBP, GIF - Max 300KB)</label>
                            <input type="file"
                                   class="form-control"
                                   id="logo"
                                   name="logo"
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   required
                                   onchange="if(this.files[0]){ document.getElementById('logoPreview').src = URL.createObjectURL(this.files[0]); }">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Upload Logo</button>
                        </div>
                    </form>

                    @if ($company && $company->logo)
                        <form action="{{ route('company.logo.destroy') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove Logo</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

