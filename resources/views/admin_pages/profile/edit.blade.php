@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">My Profile</h3>
            <p class="mb-4">Manage your account settings and password.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Profile Information --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-sm font-weight-bolder mb-3">
                        <i class="material-symbols-rounded me-1" style="font-size:16px;">person</i>
                        Profile Information
                    </h6>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Name</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Email</label>
                            <div class="input-group input-group-outline">
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-muted text-xs mt-2 mb-3">
                            Role: {{ $user->role->user_role ?? 'N/A' }}
                            &middot;
                            Member since: {{ $user->created_at?->format('M d, Y') ?? 'N/A' }}
                        </div>

                        <button type="submit" class="btn bg-gradient-dark">
                            <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                            Save Profile
                        </button>
                    </form>
                </div>
            </div>

            {{-- Password --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-sm font-weight-bolder mb-3">
                        <i class="material-symbols-rounded me-1" style="font-size:16px;">lock</i>
                        Change Password
                    </h6>

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Current Password</label>
                            <div class="input-group input-group-outline">
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            @error('current_password')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">New Password</label>
                            <div class="input-group input-group-outline">
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            @error('password')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Confirm New Password</label>
                            <div class="input-group input-group-outline">
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn bg-gradient-dark">
                            <i class="material-symbols-rounded me-1" style="font-size: 16px;">lock</i>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($user->getFirstMedia('avatar'))
                        <img src="{{ $user->getFirstMedia('avatar')->getUrl() }}" alt="{{ $user->name }}"
                             class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                    @else
                        <div class="avatar bg-gradient-dark mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:100px;height:100px;border-radius:50%;">
                            <span class="text-white" style="font-size:36px;font-weight:bold;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <h6 class="text-sm font-weight-bolder">{{ $user->name }}</h6>
                    <p class="text-xs text-muted mb-0">{{ $user->email }}</p>
                    <span class="badge bg-gradient-info text-xxs mt-2">{{ $user->role->user_role ?? 'User' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
