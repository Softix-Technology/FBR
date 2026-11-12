@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Advance Feature</h4>

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

                    <form method="GET" action="{{ route('advance_features.index') }}" class="mb-4" id="companySelectForm">
                        <label for="company_id" class="form-label fw-semibold">Select Registered Company</label>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <select name="company_id" id="company_id" class="form-select select2" data-toggle="select2" required>
                                    <option value="">-- Select Company --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->cid }}" {{ (string) request('company_id') === (string) $company->cid ? 'selected' : '' }}>
                                            {{ $company->cname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Select</button>
                        </div>
                    </form>

                    @if ($selectedCompany)
                        <hr>
                        <h5 class="mb-3">Features for: <span class="text-primary">{{ $selectedCompany->cname }}</span></h5>

                        <form method="POST" action="{{ route('advance_features.update') }}">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $selectedCompany->cid }}">

                            <div class="form-check mb-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="can_change_password"
                                       name="can_change_password"
                                       value="1"
                                       {{ $selectedCompany->can_change_password ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="can_change_password">
                                    Change Password
                                </label>
                                <div class="form-text">
                                    When checked, only users of <strong>{{ $selectedCompany->cname }}</strong> will see the Change Password option in their sidebar.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.select2) {
            var $companySelect = jQuery('#company_id');
            $companySelect.select2({
                placeholder: '-- Select Company --',
                allowClear: true,
                width: '100%'
            });
            $companySelect.on('change', function () {
                if (this.value) {
                    document.getElementById('companySelectForm').submit();
                }
            });
        } else {
            document.getElementById('company_id').addEventListener('change', function () {
                if (this.value) {
                    document.getElementById('companySelectForm').submit();
                }
            });
        }
    });
</script>
@endsection

