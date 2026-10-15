@extends('backEnd.layouts.master')
@section('title','Landing Page Edit')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

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
                <h4 class="page-title">Landing Page Edit</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{route('campaign.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">


                      <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Landing Page Title *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name}}"  id="name" required="">
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
                            <label for="banner" class="form-label">Banner Image</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" value="{{ $edit_data->banner }}"  id="banner" >
                            <img src="{{asset($edit_data->banner)}}" alt="" class="edit-image">
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
                            <label for="banner_title" class="form-label">Banner Title</label>
                            <input type="text" class="form-control @error('banner_title') is-invalid @enderror" name="banner_title" value="{{ $edit_data->banner_title }}" id="banner_title">
                            @error('banner_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ $edit_data->deadline  }}" id="deadline">
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
                            <input type="text" class="form-control @error('top_title_1') is-invalid @enderror" name="top_title_1" value="{{ $edit_data->top_title_1 }}" id="top_title_1">
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
                            <input type="text" class="form-control @error('top_title_2') is-invalid @enderror" name="top_title_2" value="{{ $edit_data->top_title_2 }}" id="top_title_2">
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
                            <input type="text" class="form-control @error('heading_1') is-invalid @enderror" name="heading_1" value="{{ $edit_data->heading_1 }}" id="heading_1">
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
                            <input type="text" class="form-control @error('feature_1') is-invalid @enderror" name="feature_1" value="{{ $edit_data->feature_1 }}" id="feature_1">
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
                            <input type="text" class="form-control @error('feature_2') is-invalid @enderror" name="feature_2" value="{{ $edit_data->feature_2 }}" id="feature_2">
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
                            <input type="text" class="form-control @error('heading_2') is-invalid @enderror" name="heading_2" value="{{ $edit_data->heading_2 }}" id="heading_2">
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
                            <input type="text" class="form-control @error('heading_3') is-invalid @enderror" name="heading_3" value="{{ $edit_data->heading_3 }}" id="heading_3">
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
                            <input type="text" class="form-control @error('heading_4') is-invalid @enderror" name="heading_4" value="{{ $edit_data->heading_4 }}" id="heading_4">
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
                            <input type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ $edit_data->note }}" id="note">
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
                            <input type="text" class="form-control @error('billing_details') is-invalid @enderror" name="billing_details" value="{{ $edit_data->billing_details }}" id="billing_details">
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
                            <input type="text" class="form-control @error('video') is-invalid @enderror" name="video" value="{{ $edit_data->video}}"  id="video">
                            @error('video')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->

                  

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Products *</label>
                            <select class="select2 form-control @error('product_id') is-invalid @enderror" 
                                    name="product_id[]" 
                                    multiple="multiple" 
                                    data-placeholder="Choose ...">
                                <option value="">Select..</option>
                                @foreach($products as $value)
                                    <option value="{{ $value->id }}" 
                                        {{ $value->id == $edit_data->product_id || in_array($value->id, array_column($select_products, 'id')) ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div>Selected Products</div>
                        <ul>
                           @foreach($select_products as $sp)
                           <li>{{$sp->name}}</li>
                            @endforeach  
                        </ul>
                       
                    </div>


                    <!-- col end -->
                    
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="image_one" class="form-label">Image One</label>
                            <input type="file" class="form-control @error('image_one') is-invalid @enderror" name="image_one" value="{{ $edit_data->image_one }}"  id="image_one" >
                            <img src="{{asset($edit_data->image_one)}}" alt="" class="edit-image">
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
                            <input type="file" class="form-control @error('image_two') is-invalid @enderror" name="image_two" value="{{ $edit_data->image_two }}"  id="image_two" >
                            <img src="{{asset($edit_data->image_two)}}" alt="" class="edit-image">
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
                            <input type="file" class="form-control @error('image_three') is-invalid @enderror" name="image_three" value="{{ $edit_data->image_three }}"  id="image_three" >
                            <img src="{{asset($edit_data->image_three)}}" alt="" class="edit-image">
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
                            <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" />
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
                        <div class="product_img">
                            @foreach($edit_data->images as $image)
                            <img src="{{asset($image->image)}}" class="edit-image border" alt="" />
                            <a href="{{route('campaign.image.destroy',['id'=>$image->id])}}" class="btn btn-xs btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></a>
                            @endforeach
                        </div>
                    </div>
                    <!-- col end -->

                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="review" class="form-label">Review *</label>
                            <input type="text" class="form-control @error('review') is-invalid @enderror" name="review" value="{{ $edit_data->review}}"  id="review" required="">
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
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea name="short_description"  rows="6" class="summernote form-control @error('short_description') is-invalid @enderror">{{$edit_data->short_description}}</textarea>
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
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description"  rows="6" class="summernote form-control @error('description') is-invalid @enderror">{{$edit_data->description}}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->

                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                                <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
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
<!-- Plugins js -->
<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
  $(".summernote").summernote({
    placeholder: "Enter Your Text Here",
  });
</script>
<script type="text/javascript">
    $('.select2').select2();
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