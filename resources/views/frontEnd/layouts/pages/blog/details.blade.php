@extends('frontEnd.layouts.master')
@section('title', $blog->title)

@push('css')
<style>
.blog-details img {
    max-width: 100%;
    border-radius: 6px;
}

.blog-meta {
    font-size: 14px;
    color: #777;
    margin-bottom: 15px;
}

.sidebar-blog {
    padding: 10px;
}

.sidebar-blog img {
    width: 80px;
    height: 65px;
    object-fit: cover;
    border-radius: 6px;
}

.sidebar-blog-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.3;
    color: #222;
    text-decoration: none;
}

.sidebar-blog-title:hover {
    color: #0d6efd;
}

.sidebar-blog-meta {
    font-size: 12px;
    color: #777;
}
</style>
@endpush

@section('content')
<section class="blog-details product-section">
    <div class="container">

        {{-- 🔹 Breadcrumb --}}
        <div class="sorting-section mb-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <a href="{{ route('blogs') }}">Blog</a>
                        <span>/</span>
                        <strong>{{ Str::limit($blog->title, 40) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- 🔹 Main Blog Content --}}
            <div class="col-md-8">

                <h3 class="mb-2">{{ $blog->title }}</h3>

                <div class="blog-meta">
                {{ $blog->created_at->format('d M Y') }}
                    &nbsp; | &nbsp;
                    👁 {{ $blog->views }} Views
                </div>

                {{-- Blog Image --}}
                @if($blog->image)
                    <img src="{{ url('public/'.$blog->image) }}"
                         class="img-fluid mb-4"
                         alt="{{ $blog->title }}">
                @else
                    <img src="{{ url('public/no-image.png') }}"
                         class="img-fluid mb-4"
                         alt="No Image">
                @endif

                {{-- Blog Description --}}
                <div class="blog-content">
                    {!! $blog->description !!}
                </div>

            </div>

            {{-- 🔹 Sidebar --}}
            <div class="col-md-4">

                <div class="card">
                    <div class="card-header">
                        Latest Blogs
                    </div>

                    <ul class="list-group list-group-flush">

                        @foreach($recentBlogs as $rblog)
                        <li class="list-group-item sidebar-blog">

                            <div class="d-flex">

                                {{-- Sidebar Image --}}
                                <div class="me-2">
                                    @if($rblog->image)
                                        <img src="{{ url('public/'.$rblog->image) }}"
                                             alt="{{ $rblog->title }}">
                                    @else
                                        <img src="{{ url('public/no-image.png') }}"
                                             alt="No Image">
                                    @endif
                                </div>

                                {{-- Sidebar Content --}}
                                <div>
                                    <a href="{{ route('blog.details', $rblog->slug) }}"
                                       class="sidebar-blog-title">
                                        {{ Str::limit($rblog->title, 45) }}
                                    </a>

                                    <div class="sidebar-blog-meta mt-1">
                                       {{ $rblog->created_at->format('d M Y') }}
                                       |
                                      👁 {{ $rblog->views }} Views
                                    </div>
                                </div>

                            </div>

                        </li>
                        @endforeach

                    </ul>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection
