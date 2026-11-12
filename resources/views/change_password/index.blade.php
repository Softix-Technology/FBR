@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Change Password</h4>

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

                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="current_password"
                                       name="current_password"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password"
                                        type="button"
                                        data-target="current_password"
                                        aria-label="Toggle password visibility">
                                    <i class="uil-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password"
                                       name="password"
                                       minlength="6"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password"
                                        type="button"
                                        data-target="password"
                                        aria-label="Toggle password visibility">
                                    <i class="uil-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       minlength="6"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password"
                                        type="button"
                                        data-target="password_confirmation"
                                        aria-label="Toggle password visibility">
                                    <i class="uil-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(this.getAttribute('data-target'));
                var icon = this.querySelector('i');
                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.classList.remove('uil-eye');
                        icon.classList.add('uil-eye-slash');
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.classList.remove('uil-eye-slash');
                        icon.classList.add('uil-eye');
                    }
                }
            });
        });
    });
</script>
@endsection

