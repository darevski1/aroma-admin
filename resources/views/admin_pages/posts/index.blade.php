@extends('layouts.master')

@section('title', 'Posts')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Posts</h3>
            <p class="mb-4">
                Manage blog posts.
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

    {{-- Posts Table --}}
    <div class="row mb-4 mt-3">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">All Posts</h6>
                    <a href="{{ route('posts.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">add</i>
                        Add Post
                    </a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold">{{ $post->id }}</span>
                                        </td>
                                        <td class="align-middle ps-2">
                                            <span class="text-xs font-weight-bold">{{ $post->title }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($post->published)
                                                <span class="badge bg-gradient-success">Published</span>
                                            @else
                                                <span class="badge bg-gradient-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-xs text-muted">{{ $post->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            {{-- Toggle Publish --}}
                                            <form action="{{ route('posts.toggle', $post->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $post->published ? 'btn-outline-success' : 'btn-outline-secondary' }} me-1" title="{{ $post->published ? 'Unpublish' : 'Publish' }}">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">{{ $post->published ? 'visibility' : 'visibility_off' }}</i>
                                                </button>
                                            </form>

                                            {{-- Edit --}}
                                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                                <i class="material-symbols-rounded" style="font-size: 16px;">edit</i>
                                            </a>

                                            {{-- View (if published) --}}
                                            @if ($post->published)
                                                <a href="{{ route('pages.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-dark me-1" title="View on site">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">open_in_new</i>
                                                </a>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">delete</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No posts found. Create your first post to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
