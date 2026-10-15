@extends('backEnd.layouts.master')
@section('title','TikTok Pixel Management')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
    .card { border: none; box-shadow: 0 0 20px rgba(18,38,63,0.03); border-radius: 12px; overflow: hidden; }
    .card-body { padding: 25px; }
    .table thead th { background-color: #f9fbfd; font-weight: 600; text-transform: uppercase; font-size: 11px; color: #8391a2; letter-spacing: 0.5px; border-bottom: 1px solid #eef2f7; padding: 12px 15px; }
    .table tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f5f7; color: #313b5e; font-size: 14px; }
    .pixel-code { font-family: 'Courier New', monospace; background: #f1f5f7; padding: 4px 8px; border-radius: 4px; color: #d63384; font-weight: 600; }
    .badge-soft-success { background-color: rgba(10,207,151,0.18); color: #0acf97; }
    .badge-soft-danger { background-color: rgba(250,92,124,0.18); color: #fa5c7c; }
    .badge-pill { padding: 5px 10px; border-radius: 50rem; font-weight: 500; font-size: 11px; }
    .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; color: #6c757d; transition: all 0.2s; border: 1px solid transparent; background: #f9fbfd; cursor: pointer; }
    .action-btn:hover { background-color: #eef2f7; color: #343a40; }
    .btn-status-active:hover { background-color: rgba(10,207,151,0.1); color: #0acf97; }
    .btn-status-inactive:hover { background-color: rgba(255,188,0,0.1); color: #ffbc00; }
    .btn-edit:hover { background-color: rgba(114,124,245,0.1); color: #727cf5; }
    .btn-delete:hover { background-color: rgba(250,92,124,0.1); color: #fa5c7c; }
    .tiktok-badge { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #010101 0%, #1a1a2e 100%); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">
                    <span class="tiktok-badge me-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V9.05a8.16 8.16 0 004.77 1.52V7.13a4.85 4.85 0 01-1-.44z"/></svg>
                        TikTok
                    </span>
                    Pixel Management
                </h4>
                <p class="text-muted font-size-13 mb-0">Manage your TikTok tracking pixels.</p>
            </div>
            <a href="{{route('tiktok.pixels.create')}}" class="btn btn-dark rounded-pill shadow-sm px-4">
                <i class="fe-plus me-1"></i> Add TikTok Pixel
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-hover w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>TikTok Pixel ID</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key => $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="pixel-code">{{ $value->code }}</span></td>
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge badge-pill badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-pill badge-soft-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('tiktok.pixels.inactive')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="action-btn btn-status-inactive" title="Deactivate">
                                                    <i class="fe-thumbs-down"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('tiktok.pixels.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="action-btn btn-status-active" title="Activate">
                                                    <i class="fe-thumbs-up"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{route('tiktok.pixels.edit', $value->id)}}" class="action-btn btn-edit" title="Edit">
                                            <i class="fe-edit"></i>
                                        </a>
                                        <form method="post" action="{{route('tiktok.pixels.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="action-btn btn-delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
@endsection
