@extends('frontEnd.layouts.master')
@section('title','Blog')

@push('css')
<style>
.blog-card {
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    transition: all .3s ease;
    background: #fff;
    height: 100%;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

.blog-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.blog-content {
    padding: 15px;
}

.blog-title a {
    font-size: 18px;
    font-weight: 600;
    color: #222;
    text-decoration: none;
}

.blog-title a:hover {
    color: #0d6efd;
}

.blog-meta {
    font-size: 13px;
    color: #777;
    margin-bottom: 10px;
}

.read-more-btn {
    border-radius: 20px;
    padding: 6px 22px;
    font-size: 14px;
}
</style>
@endpush

@section('content')
<section class="blog-section product-section">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="sorting-section mb-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <strong>Blog</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Blog Grid --}}
        <div class="row">
            @foreach($blogs as $blog)
            <div class="col-md-4 mb-4 d-flex">

                <div class="blog-card w-100">

                    {{-- Image --}}
                    <div class="blog-img">
                        <a href="{{ route('blog.details',$blog->slug) }}">
                            @if($blog->image)
                                <img src="{{ url('public/'.$blog->image) }}" alt="{{ $blog->title }}">
                            @else
                                <img src="{{ url('public/no-image.png') }}" alt="No Image">
                            @endif
                        </a>
                    </div>

                    {{-- Content --}}
                    <div class="blog-content">

                        <div class="blog-title mb-1">
                            <a href="{{ route('blog.details',$blog->slug) }}">
                                {{ Str::limit($blog->title,50) }}
                            </a>
                        </div>

                        <div class="blog-meta">
                            {{ $blog->created_at->format('d M Y') }} |
                            👁 {{ $blog->views }}
                        </div>

                        <p>
                            {{ Str::limit($blog->short_description,120) }}
                        </p>

                        <div class="text-center mt-3">
                            <a href="{{ route('blog.details', $blog->slug) }}"
                               class="btn btn-sm btn-outline-primary read-more-btn">
                                Read More
                            </a>
                        </div>

                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate text-center">
                    {{ $blogs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
