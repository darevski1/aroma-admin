@extends('layouts.master')

@section('title', 'Create User')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Create New User</h3>
            <p class="mb-4">
                Add a new user account and assign a role.
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Name --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        {{-- Email --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        {{-- Password --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        {{-- Password Confirmation --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        {{-- Role --}}
                        <div class="input-group input-group-outline my-3">
                            <select name="user_role" class="form-control" required>
                                <option value="" disabled {{ old('user_role') ? '' : 'selected' }}>Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('user_role') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->user_role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Active Status --}}
                        <div class="input-group input-group-outline my-3">
                            <select name="active" class="form-control" required>
                                <option value="1" {{ old('active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                Create User
                            </button>
                            <a href="{{ route('users') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: Avatar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-sm font-weight-bolder mb-3">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">account_circle</i>
                        Profile Photo
                    </h6>

                    <x-media.avatar-picker :model="null" :current="null" />

                    <p class="text-muted text-xs mt-3">
                        Recommended: 400x400px. Accepted formats: JPG, PNG, WebP, GIF.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
