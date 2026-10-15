<div class="card pf-sidebar-panel mb-4">
    <div class="card-body">
        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-dollar-sign"></i> Pricing & Inventory</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label for="purchase_price" class="form-label">Purchase <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control border-primary @error('purchase_price') is-invalid @enderror"
                           name="purchase_price" value="{{ $edit_data->purchase_price}}" id="purchase_price" placeholder="0" />
                    @error('purchase_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="old_price" class="form-label">Old Price</label>
                    <input type="text" class="form-control @error('old_price') is-invalid @enderror"
                           name="old_price" value="{{ $edit_data->old_price }}" id="old_price" />
                    @error('old_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="new_price" class="form-label">New <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control font-weight-bold @error('new_price') is-invalid @enderror"
                           name="new_price" value="{{ $edit_data->new_price }}" id="new_price" placeholder="0" />
                    @error('new_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="reseller_price" class="form-label">Reseller</label>
                    <input type="text" step="0.01" class="form-control @error('reseller_price') is-invalid @enderror"
                           name="reseller_price" value="{{ old('reseller_price', $edit_data->reseller_price) }}" id="reseller_price" placeholder="Optional" />
                    @error('reseller_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="stock" class="form-label">Stock <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control @error('stock') is-invalid @enderror"
                           name="stock" value="{{ $edit_data->stock }}" id="stock" placeholder="0" />
                    @error('stock')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="pro_unit" class="form-label">Unit</label>
                    <input type="text" class="form-control @error('pro_unit') is-invalid @enderror"
                           name="pro_unit" value="{{ $edit_data->pro_unit }}" id="pro_unit" />
                </div>
                <div class="col-12">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select class="form-control form-control-sm select2 @error('brand_id') is-invalid @enderror" name="brand_id">
                        <option value="">Select..</option>
                        @foreach($brands as $value)
                            <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>{{$value->name}}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>
        </div>

        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-image"></i> Media & Video</div>
            <label class="form-label">Gallery</label>
            <div class="increment-wrapper">
                <div class="control-group increment image-row">
                    <div class="row align-items-center g-1">
                        <div class="col-9">
                            <input type="file" name="image[]" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*" />
                        </div>
                        <div class="col-3">
                            <button class="btn btn-success btn-increment btn-sm w-100" type="button"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    @error('image')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>
            <div class="clone hide d-none" style="display: none;">
                <div class="control-group image-row">
                    <div class="row align-items-center g-1">
                        <div class="col-9">
                            <input type="file" name="image[]" class="form-control form-control-sm" accept="image/*" />
                        </div>
                        <div class="col-3">
                            <button class="btn btn-danger btn-remove-image btn-sm w-100" type="button"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product_img mt-1 d-flex flex-wrap">
                @foreach($edit_data->images->filter(fn($img) => !$img->color_id && !$img->size_id) as $image)
                    <div class="position-relative me-1 mb-1">
                        <img src="{{asset($image->image)}}" class="edit-image border" alt="">
                        <a href="{{route('products.image.destroy',['id'=>$image->id])}}"
                           class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle"
                           style="padding: 0px 4px; top: -5px; right: -5px;"
                           onclick="return confirm('Delete this image?')"><i class="mdi mdi-close"></i></a>
                    </div>
                @endforeach
            </div>
            @php $colorSizeImages = $edit_data->images->filter(fn($img) => $img->color_id || $img->size_id); @endphp
            @if($colorSizeImages->isNotEmpty())
            <div class="mt-1">
                <label class="form-label small text-muted mb-0">Color/Size imgs</label>
                <div class="d-flex flex-wrap gap-1 product_img">
                    @foreach($colorSizeImages as $img)
                        <div class="position-relative">
                            <img src="{{asset($img->image)}}" class="edit-image border" alt="">
                            <a href="{{route('products.image.destroy',['id'=>$img->id])}}" class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle" style="padding:0 4px;top:-5px;right:-5px;" onclick="return confirm('Delete?')"><i class="mdi mdi-close"></i></a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @php $existingVideoType = $edit_data->pro_video_type ?? ($edit_data->pro_video ? 'youtube' : null); @endphp
            <label class="form-label mt-2 mb-1">প্রোডাক্ট ভিডিও</label>
            <div class="d-flex pf-video-radios flex-wrap">
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="pro_video_source" id="vs_yt_e" value="youtube" {{ $existingVideoType !== 'upload' ? 'checked' : '' }}>
                    <label class="form-check-label" for="vs_yt_e"><i class="fa fa-youtube-play text-danger"></i> YouTube</label>
                </div>
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="pro_video_source" id="vs_up_e" value="upload" {{ $existingVideoType === 'upload' ? 'checked' : '' }}>
                    <label class="form-check-label" for="vs_up_e"><i class="fa fa-upload text-primary"></i> Upload</label>
                </div>
            </div>
            <div id="yt_section_e" style="{{ $existingVideoType === 'upload' ? 'display:none;' : '' }}">
                <input type="text" name="pro_video" id="pro_video_e" class="form-control form-control-sm @error('pro_video') is-invalid @enderror"
                       value="{{ $edit_data->pro_video }}" placeholder="YouTube URL / Video ID">
                @error('pro_video')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                @if($edit_data->pro_video)
                <div id="yt_preview_e" class="mt-1">
                    <iframe id="yt_iframe_e" width="100%" height="100" src="https://www.youtube.com/embed/{{ $edit_data->pro_video }}" frameborder="0" allowfullscreen style="border-radius:8px;"></iframe>
                </div>
                @else
                <div id="yt_preview_e" class="mt-1" style="display:none;">
                    <iframe id="yt_iframe_e" width="100%" height="100" src="" frameborder="0" allowfullscreen style="border-radius:8px;"></iframe>
                </div>
                @endif
            </div>
            <div id="up_section_e" style="{{ $existingVideoType === 'upload' ? '' : 'display:none;' }}">
                @if($existingVideoType === 'upload' && $edit_data->pro_video_path)
                <div class="mb-1 p-1 bg-light rounded d-flex align-items-center gap-1" style="font-size:11px;">
                    <i class="fa fa-film text-primary"></i>
                    <span class="text-truncate">{{ basename($edit_data->pro_video_path) }}</span>
                    <a href="{{ asset($edit_data->pro_video_path) }}" target="_blank" class="btn btn-xs btn-outline-primary ms-auto py-0 px-1"><i class="fa fa-play"></i></a>
                </div>
                @endif
                <input type="file" name="pro_video_file" id="pro_video_file_e" class="form-control form-control-sm" accept="video/mp4,video/webm,video/ogg">
                <div id="up_preview_e" class="mt-1" style="display:none;">
                    <video id="up_video_e" width="100%" height="100" controls style="border-radius:8px;background:#000;"></video>
                </div>
            </div>
        </div>

        @php
            $currentType = old('product_type', $edit_data->is_digital ? 'digital' : 'physical');
            $isDigital   = $currentType === 'digital';
        @endphp
        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-settings"></i> Settings</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label for="product_type" class="form-label">Type</label>
                    <select class="form-control form-control-sm bg-light" id="product_type" name="product_type">
                        <option value="physical" {{ $currentType === 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="digital" {{ $currentType === 'digital' ? 'selected' : '' }}>Digital</option>
                    </select>
                </div>
                <div id="advance_area" class="col-6" style="{{ $isDigital ? 'display:none;' : '' }}">
                    <label for="advance_amount" class="form-label">Advance</label>
                    <input type="text" class="form-control form-control-sm @error('advance_amount') is-invalid @enderror"
                           name="advance_amount" id="advance_amount" value="{{ old('advance_amount', $edit_data->advance_amount) }}" />
                </div>
                <div class="col-6">
                    <label for="sold" class="form-label">Sold</label>
                    <input type="text" class="form-control form-control-sm @error('sold') is-invalid @enderror"
                           name="sold" value="{{ $edit_data->sold }}" id="sold" />
                </div>
            </div>
            <div class="d-flex align-items-center pf-free-delivery mb-2">
                <label class="switch me-2 mb-0">
                    <input type="checkbox" value="1" name="free_delivery" {{ old('free_delivery', $edit_data->free_delivery) ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <small class="text-muted mb-0">Free delivery</small>
            </div>
            <div id="digital_area" style="{{ $isDigital ? '' : 'display:none;' }}" class="p-2 border rounded mb-2 bg-light">
                @if($edit_data->digital_file)
                    <small class="d-block text-truncate mb-1">File: <code>{{ $edit_data->digital_file }}</code></small>
                @endif
                <input type="file" class="form-control form-control-sm mb-1" name="digital_file" id="digital_file">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label"><small>Limit</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_limit" id="download_limit"
                               value="{{ old('download_limit', $edit_data->download_limit ?? 5) }}" min="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label"><small>Days</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_expire_days" id="download_expire_days"
                               value="{{ old('download_expire_days', $edit_data->download_expire_days ?? 7) }}" min="1">
                    </div>
                </div>
            </div>
            <div class="row text-center g-compact">
                <div class="col-4">
                    <label class="d-block form-label">Status</label>
                    <label class="switch"><input type="checkbox" value="1" name="status" @if($edit_data->status==1) checked @endif><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Hot</label>
                    <label class="switch"><input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1) checked @endif><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Flash</label>
                    <label class="switch"><input type="checkbox" value="1" name="flashsale" @if($edit_data->flashsale==1) checked @endif><span class="slider round"></span></label>
                </div>
            </div>
        </div>

        <div class="pf-side-foot">
            <button type="submit" class="btn btn-success w-100 shadow rounded-pill"><i class="fe-check-circle me-1"></i> Update Product</button>
        </div>
    </div>
</div>
