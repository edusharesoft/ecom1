@extends('backEnd.layouts.master')

@php
    use Illuminate\Support\Str;
@endphp

@section('title','Contact Messages')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-header">
        <h4>Contact Messages</h4>
    </div>

    <div class="card-body">

        <div id="ajaxTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $key => $row)
                    <tr>
                        <td>{{ $messages->firstItem() + $key }}</td>
                        <td>{{ $row->full_name }}</td>
                        <td>{{ $row->mobile }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->subject }}</td>
                        <td>{{ Str::limit($row->details, 50) }}</td>
                        <td>
                            {{-- Delete --}}
                            <form action="{{ route('admin.contact.messages.delete',$row->id) }}"
                                  method="POST"
                                  class="deleteForm"
                                  style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No messages found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
           {{ $messages->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

    // ================= Pagination =================
    $(document).on('click','.pagination a',function(e){
        e.preventDefault();
        let url = $(this).attr('href');

        $.get(url,function(data){
            let html = $(data).find('#ajaxTable').html();
            $('#ajaxTable').html(html);
        });
    });

    // ================= Delete =================
    $(document).on('submit','.deleteForm',function(e){
        e.preventDefault();

        if(!confirm('Are you sure to delete?')) return;

        let form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success:function(){
                // reload current page data
                location.reload();
            }
        });
    });

});
</script>
@endpush
