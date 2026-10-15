@extends('frontEnd.layouts.master') 
@section('title','Hot Deals') 
@push('css')
<link rel="stylesheet" href="{{asset('public/frontEnd/css/jquery-ui.css')}}" />
@endpush 
@section('content')



@endsection
@push('script')
<script>
    $(".sort").change(function(){
       $('#loading').show();
       $(".sort-form").submit();
    })
</script>
@endpush