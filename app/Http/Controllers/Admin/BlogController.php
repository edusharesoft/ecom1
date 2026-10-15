<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Blog list
     */
    public function index()
    {
        $blogs = Blog::latest()->get();
        return view('backEnd.blog.index', compact('blogs'));
    }

    /**
     * Create blog form
     */
    public function create()
    {
        return view('backEnd.blog.create');
    }

    /**
     * Store new blog
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'required',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'nullable|in:0,1',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizer::store($request->file('image'), 'uploads/blogs/');
        }

        Blog::create([
            'title'             => $request->title,
            'slug'              => Str::slug($request->title).'-'.time(),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'image'             => $imagePath,
            'status'            => $request->status ?? 1,
        ]);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog created successfully');
    }

    /**
     * Edit blog form
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('backEnd.blog.edit', compact('blog'));
    }

    /**
     * Update blog
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'required',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'nullable|in:0,1',
        ]);

        if ($request->hasFile('image')) {

            // delete old image
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            $blog->image = ImageOptimizer::store($request->file('image'), 'uploads/blogs/');
        }

        $blog->update([
            'title'             => $request->title,
            'slug'              => Str::slug($request->title).'-'.time(),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'status'            => $request->status ?? 1,
        ]);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog updated successfully');
    }

    /**
     * Delete blog
     */
    public function delete($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->image && file_exists(public_path($blog->image))) {
            unlink(public_path($blog->image));
        }

        $blog->delete();

        return back()->with('success', 'Blog deleted successfully');
    }
}
