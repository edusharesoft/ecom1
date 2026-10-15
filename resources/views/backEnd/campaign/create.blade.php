@extends('backEnd.layouts.master')
@section('title','Landing Page Create')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('campaign.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Landing Page Create</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('campaign.store') }}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="createForm">
                    @csrf
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Landing Page Title *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="banner" class="form-label">Banner Image *</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" id="banner" required>
                            @error('banner')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="banner_title" class="form-label">Banner Title *</label>
                            <input type="text" class="form-control @error('banner_title') is-invalid @enderror" name="banner_title" value="{{ old('banner_title') }}" id="banner_title" required="">
                            @error('banner_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ old('deadline') }}" id="deadline">
                            @error('deadline')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="top_title_1" class="form-label">Top Title 1</label>
                            <input type="text" class="form-control @error('top_title_1') is-invalid @enderror" name="top_title_1" value="{{ old('top_title_1') }}" id="top_title_1">
                            @error('top_title_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="top_title_2" class="form-label">Top Title 2</label>
                            <input type="text" class="form-control @error('top_title_2') is-invalid @enderror" name="top_title_2" value="{{ old('top_title_2') }}" id="top_title_2">
                            @error('top_title_2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="heading_1" class="form-label">Heading 1</label>
                            <input type="text" class="form-control @error('heading_1') is-invalid @enderror" name="heading_1" value="{{ old('heading_1') }}" id="heading_1">
                            @error('heading_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="feature_1" class="form-label">Feature 1</label>
                            <input type="text" class="form-control @error('feature_1') is-invalid @enderror" name="feature_1" value="{{ old('feature_1') }}" id="feature_1">
                            @error('feature_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="feature_2" class="form-label">Feature 2</label>
                            <input type="text" class="form-control @error('feature_2') is-invalid @enderror" name="feature_2" value="{{ old('feature_2') }}" id="feature_2">
                            @error('feature_2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="heading_2" class="form-label">Heading 2</label>
                            <input type="text" class="form-control @error('heading_2') is-invalid @enderror" name="heading_2" value="{{ old('heading_2') }}" id="heading_2">
                            @error('heading_2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="heading_3" class="form-label">Heading 3</label>
                            <input type="text" class="form-control @error('heading_3') is-invalid @enderror" name="heading_3" value="{{ old('heading_3') }}" id="heading_3">
                            @error('heading_3')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="heading_4" class="form-label">Heading 4</label>
                            <input type="text" class="form-control @error('heading_4') is-invalid @enderror" name="heading_4" value="{{ old('heading_4') }}" id="heading_4">
                            @error('heading_4')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="note" class="form-label">Note</label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ old('note') }}" id="note">
                            @error('note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="billing_details" class="form-label">Billing Details</label>
                            <input type="text" class="form-control @error('billing_details') is-invalid @enderror" name="billing_details" value="{{ old('billing_details') }}" id="billing_details">
                            @error('billing_details')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="video" class="form-label">Youtube Video ID</label>
                            <input type="text" class="form-control @error('video') is-invalid @enderror" name="video" value="{{ old('video') }}" id="video">
                            @error('video')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Products *</label>
                            <select class="select2 form-control @error('product_id') is-invalid @enderror" 
                                    name="product_id[]" 
                                    multiple="multiple" 
                                    data-placeholder="Choose ..." 
                                    required>
                                @foreach($products as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- col end -->
                
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="image_one" class="form-label">Image One *</label>
                            <input type="file" class="form-control @error('image_one') is-invalid @enderror" name="image_one" id="image_one" required="">
                            @error('image_one')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="image_two" class="form-label">Image Two</label>
                            <input type="file" class="form-control @error('image_two') is-invalid @enderror" name="image_two" id="image_two">
                            @error('image_two')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="image_three" class="form-label">Image Three</label>
                            <input type="file" class="form-control @error('image_three') is-invalid @enderror" name="image_three" id="image_three">
                            @error('image_three')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                
                    <div class="col-sm-6 mb-3">
                        <label for="image">Review Image *</label>
                        <div class="input-group control-group increment">
                            <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" required />
                            <div class="input-group-btn">
                                <button class="btn btn-success btn-increment" type="button"><i class="fa fa-plus"></i></button>
                            </div>
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="clone hide" style="display: none;">
                            <div class="control-group input-group">
                                <input type="file" name="image[]" class="form-control" />
                                <div class="input-group-btn">
                                    <button class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- col end -->
                
                    <div class="col-sm-6 mb-3">
                        <div class="form-group mb-3">
                            <label for="review" class="form-label">Review *</label>
                            <input type="text" class="form-control @error('review') is-invalid @enderror" name="review" value="{{ old('review') }}" id="review" required="">
                            @error('review')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="short_description" class="form-label">Short Description *</label>
                            <textarea name="short_description" rows="6" class="summernote form-control @error('short_description') is-invalid @enderror" required="">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="description" class="form-label">Description *</label>
                            <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror" required="">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                
                  
                    <!-- col end -->
                
                    <div>
                        <input type="submit" class="btn btn-success" value="Create Campaign">
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection


@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-pickers.init.js"></script>

<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",    
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment").after(html);
        });
        $("body").on("click", ".btn-danger", function () {
            $(this).parents(".control-group").remove();
        });
        $('.select2').select2();
    });
</script>
@endsection