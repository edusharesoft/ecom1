@extends('backEnd.layouts.master')

@section('title','Newsletter Subscribers')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-header">
        <h4>Newsletter Subscribers</h4>
        <small class="text-muted">Emails submitted from footer newsletter form</small>
    </div>

    <div class="card-body">

        <div id="ajaxTable">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Subscribed At</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $key => $row)
                    <tr>
                        <td>{{ $subscribers->firstItem() + $key }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <form action="{{ route('admin.newsletter.subscribers.delete', $row->id) }}"
                                  method="POST"
                                  class="deleteNewsletterForm"
                                  style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No subscribers yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $subscribers->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

    // Pagination
    $(document).on('click','.pagination a',function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        $.get(url,function(data){
            let html = $(data).find('#ajaxTable').html();
            $('#ajaxTable').html(html);
        });
    });

    // Delete
    $(document).on('submit','.deleteNewsletterForm',function(e){
        e.preventDefault();
        if(!confirm('Are you sure to delete this subscriber?')) return;
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(){
                location.reload();
            }
        });
    });

});
</script>
@endpush
