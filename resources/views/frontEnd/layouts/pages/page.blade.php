@extends('frontEnd.layouts.master')
@section('title','Page')
@section('content')

<section class="comn_sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="cmn_menu">
                    <ul>
                        @foreach($cmnmenu as $key=>$value)
                        <li>
                            <a href="{{route('page',$value->slug)}}">{{$value->name}}</a>
                        </li>
                        @endforeach
                        <li>
                            <a href="{{route('contact')}}">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="createpage-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-content">
                    <div class="page-title mb-2">
                        <h5>{{$page->title}}</h5>
                    </div>
                    <div class="page-description">
                        {!! $page->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
