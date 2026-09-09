<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $posts = Post::with(['media'])
                ->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15));

            if ($request->expectsJson()) {
                return response()->json(['data' => $posts]);
            }

            return view('admin_pages.posts.index', compact('posts'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to load posts.'], 500);
            }
            return back()->with('error', 'Failed to load posts.');
        }
    }

    public function create()
    {
        return view('admin_pages.posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'content' => 'nullable',
            'published' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'layout' => 'nullable',
            'excerpt' => 'nullable',
            'featured_image_file' => 'nullable|file|image|max:10240|mimes:jpeg,png,webp,avif,gif,svg',
            'attachment_files' => 'nullable|array',
            'attachment_files.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        try {
            $validated['published'] = $request->boolean('published');
            if (empty($validated['slug'])) {
                $validated['slug'] = \Str::slug($validated['title']);
            }

            $post = Post::create(collect($validated)->only([
                'title', 'slug', 'content', 'published',
                'meta_title', 'meta_description', 'layout', 'excerpt',
            ])->toArray());

            if ($request->hasFile('featured_image_file')) {
                $post->addMedia($request->file('featured_image_file'))
                    ->toMediaCollection('featured_image');
            }

            if ($request->hasFile('attachment_files')) {
                foreach ($request->file('attachment_files') as $file) {
                    $post->addMedia($file)
                        ->toMediaCollection('attachments');
                }
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Post created successfully.', 'data' => $post->load('media')], 201);
            }

            return redirect()->route('posts.edit', $post)->with('success', 'Post created successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to create post.', 'error' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Failed to create post: ' . $e->getMessage());
        }
    }

    public function edit(Post $post)
    {
        $post->load('media');

        return view('admin_pages.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug,' . $post->id,
            'content' => 'nullable',
            'published' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'layout' => 'nullable',
            'excerpt' => 'nullable',
            'featured_image_file' => 'nullable|file|image|max:10240|mimes:jpeg,png,webp,avif,gif,svg',
            'attachment_files' => 'nullable|array',
            'attachment_files.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        try {
            $validated['published'] = $request->boolean('published');
            if (empty($validated['slug'])) {
                $validated['slug'] = \Str::slug($validated['title']);
            }

            $post->update(collect($validated)->only([
                'title', 'slug', 'content', 'published',
                'meta_title', 'meta_description', 'layout', 'excerpt',
            ])->toArray());

            // Featured image
            if ($request->hasFile('featured_image_file')) {
                $post->clearMediaCollection('featured_image');
                $post->addMedia($request->file('featured_image_file'))
                    ->toMediaCollection('featured_image');
            }

            // Attachment additions
            if ($request->hasFile('attachment_files')) {
                foreach ($request->file('attachment_files') as $file) {
                    $post->addMedia($file)
                        ->toMediaCollection('attachments');
                }
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Post updated successfully.', 'data' => $post->fresh()->load('media')]);
            }

            return redirect()->route('posts.edit', $post)->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to update post.', 'error' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Failed to update post: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Post $post)
    {
        try {
            $post->delete();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Post deleted successfully.']);
            }

            return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to delete post.', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to delete post.');
        }
    }

    public function togglePublish(Request $request, Post $post)
    {
        try {
            $post->update(['published' => !$post->published]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Post ' . ($post->published ? 'published' : 'unpublished') . ' successfully.',
                    'published' => $post->published,
                ]);
            }

            return back()->with('success', 'Post status updated.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to update post status.', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to update post status.');
        }
    }
}
