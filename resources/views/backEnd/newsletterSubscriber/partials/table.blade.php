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
