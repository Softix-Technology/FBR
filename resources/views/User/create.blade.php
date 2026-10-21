<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Create User</h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
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

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                  
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Province</label>
                        <input type="text" name="province" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CINC NTN</label>
                        <input type="text" name="cinc_ntn" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">STRN</label>
                        <input type="text" name="strn" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company</label>
                        <select name="company_id" id="" class="form-select select2">
                            <option value="">Select Company</option>
                            @foreach($company as $comp)
                                <option value="{{ $comp->cid }}">{{ $comp->cname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Enter reference">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">FBR Access Token</label>
                    <input type="text" name="fbr_access_token" class="form-control">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="is_admin">
                    <label class="form-check-label" for="is_admin">
                        Is Admin
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="use_sandbox" value="1" id="use_sandbox">
                    <label class="form-check-label" for="use_sandbox">
                        Use Sandbox
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Save User
                </button>

                <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">
                    Cancel
                </a>

            </form>
        </div>
    </div>
</div>

</body>
</html>