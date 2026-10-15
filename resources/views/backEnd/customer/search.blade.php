@if($customers && count($customers) > 0)
    <div class="card border-0 shadow-none mb-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-top-0">Customer Name</th>
                            <th class="border-top-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $value)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-soft-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="fe-user text-primary font-size-13"></i>
                                    </div>
                                    <div>
                                        <h5 class="my-0 fw-semibold font-size-14">
                                            <a href="{{route('customers.profile',['id'=>$value->id])}}" class="text-dark text-decoration-none">
                                                {{$value->name}}
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end align-middle">
                                <a href="{{route('customers.profile',['id'=>$value->id])}}" class="btn btn-xs btn-light rounded-circle shadow-sm">
                                    <i class="fe-arrow-right text-muted"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="text-center p-3">
        <p class="text-muted mb-0"><i class="fe-alert-circle me-1"></i> No customers found.</p>
    </div>
@endif