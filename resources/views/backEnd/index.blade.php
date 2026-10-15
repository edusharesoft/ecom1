@extends('backEnd.layouts.master')

@section('title','Contact Messages')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Contact Messages</h4>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $key => $row)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $row->full_name }}</td>
                    <td>{{ $row->mobile }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->subject }}</td>
                    <td>{{ Str::limit($row->details, 50) }}</td>
                    <td>
                        @if($row->status == 0)
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-success">Seen</span>
                        @endif
                    </td>
                    <td>
                        {{-- Status --}}
                        <form action="{{ route('admin.contact.messages.status',$row->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-info">
                                Status
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form action="{{ route('admin.contact.messages.delete',$row->id) }}"
                              method="POST"
                              style="display:inline"
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                @if($messages->count() == 0)
                <tr>
                    <td colspan="8" class="text-center">No messages found</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
