<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    /**
     * 🔹 Blog List Page
     * URL: /blogs
     */
    public function index()
    {
        $blogs = Blog::where('status', 1)
            ->latest()
            ->paginate(9);

        // ✅ correct blade path
        return view('frontEnd.layouts.pages.blog.index', compact('blogs'));
    }

    /**
     * 🔹 Blog Details Page
     * URL: /blog/{slug}
     */
    public function details($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // 👁️ view count increment
        $blog->increment('views');

        // 🔹 Recent Blogs
        $recentBlogs = Blog::where('status', 1)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->limit(5)
            ->get();

        // ✅ correct blade path
        return view(
            'frontEnd.layouts.pages.blog.details',
            compact('blog', 'recentBlogs')
        );
    }
}
