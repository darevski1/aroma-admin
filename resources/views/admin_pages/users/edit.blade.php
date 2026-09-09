@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Edit User: {{ $user->name }}</h3>
            <p class="mb-4">
                Update user account details and role.
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
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Name --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        {{-- Email --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        {{-- Password (optional on edit) --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        {{-- Password Confirmation --}}
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        {{-- Role --}}
                        <div class="input-group input-group-outline my-3">
                            <select name="user_role" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('user_role', $user->user_role) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->user_role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Active Status --}}
                        <div class="input-group input-group-outline my-3">
                            <select name="active" class="form-control" required>
                                <option value="1" {{ old('active', $user->active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('active', $user->active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        {{-- Info --}}
                        <div class="text-muted text-xs mt-2">
                            Created: {{ $user->created_at?->format('M d, Y H:i') ?? 'N/A' }}
                            &middot;
                            Last updated: {{ $user->updated_at?->format('M d, Y H:i') ?? 'N/A' }}
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                Update User
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

                    <x-media.avatar-picker :model="$user" :current="$user->getFirstMedia('avatar')" />

                    <p class="text-muted text-xs mt-3">
                        Recommended: 400x400px. Accepted formats: JPG, PNG, WebP, GIF.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
