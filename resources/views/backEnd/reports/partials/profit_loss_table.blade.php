<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th class="text-end">Sales</th>
            <th class="text-end">COGS</th>
            <th class="text-end">Expense</th>
            <th class="text-end">Net Profit</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows ?? [] as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->date }}</td>
                <td class="text-end">{{ number_format($row->sales,2) }}</td>
                <td class="text-end">{{ number_format($row->cogs,2) }}</td>
                <td class="text-end">{{ number_format($row->expense,2) }}</td>
                <td class="text-end fw-bold
                    {{ $row->profit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($row->profit,2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    কোনো রেকর্ড পাওয়া যায়নি
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if(isset($rows))
    <div class="mt-3">
        {{ $rows->links() }}
    </div>
@endif
