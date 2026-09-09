@extends('layouts.master')

@section('title', 'User Management')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">User Management</h3>
            <p class="mb-4">
                Create, edit and manage administrator and user accounts.
            </p>
        </div>
    </div>

    {{-- Success / Error messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Users Table --}}
    <div class="row mb-4 mt-3">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">All Users</h6>
                    <a href="{{ route('users.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">person_add</i>
                        Add User
                    </a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold">{{ $user->id }}</span>
                                        </td>
                                        <td class="align-middle ps-2">
                                            <span class="text-xs font-weight-bold">{{ $user->name }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-xs">{{ $user->email }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($user->user_role == 2)
                                                <span class="badge bg-gradient-dark">Admin</span>
                                            @else
                                                <span class="badge bg-gradient-info">User</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($user->active)
                                                <span class="badge bg-gradient-success">Active</span>
                                            @else
                                                <span class="badge bg-gradient-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                                <i class="material-symbols-rounded" style="font-size: 16px;">edit</i>
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="material-symbols-rounded" style="font-size: 16px;">delete</i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
