<div class="card pf-sidebar-panel mb-4">
    <div class="card-body">
        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-dollar-sign"></i> Pricing & Inventory</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label class="form-label">Purchase <small class="text-muted">(Opt.)</small></label>
                    <input type="number" name="purchase_price" class="form-control border-primary" placeholder="0">
                </div>
                <div class="col-6">
                    <label class="form-label">Old Price</label>
                    <input type="number" name="old_price" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">New Price <small class="text-muted">(Opt.)</small></label>
                    <input type="number" name="new_price" class="form-control font-weight-bold" placeholder="0">
                </div>
                <div class="col-6">
                    <label class="form-label">Reseller</label>
                    <input type="number" step="0.01" name="reseller_price" class="form-control" placeholder="Optional" title="Special price for resellers">
                </div>
                <div class="col-6">
                    <label class="form-label">Stock <small class="text-muted">(Opt.)</small></label>
                    <input type="number" name="stock" class="form-control" placeholder="0">
                </div>
                <div class="col-6">
                    <label class="form-label">Unit</label>
                    <input type="text" name="pro_unit" class="form-control" placeholder="pcs">
                </div>
            </div>
        </div>

        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-image"></i> Media & Video</div>
            <label class="form-label">Gallery *</label>
            <div class="increment-wrapper">
                <div class="control-group increment image-row">
                    <div class="row align-items-center g-1">
                        <div class="col-9">
                            <input type="file" name="image[]" class="form-control form-control-sm" required accept="image/*">
                        </div>
                        <div class="col-3">
                            <button class="btn btn-success btn-increment btn-sm w-100" type="button"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clone d-none">
                <div class="control-group image-row">
                    <div class="row align-items-center g-1">
                        <div class="col-9">
                            <input type="file" name="image[]" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-3">
                            <button class="btn btn-danger btn-remove-image btn-sm w-100" type="button"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <label class="form-label mt-2 mb-1">প্রোডাক্ট ভিডিও</label>
            <div class="d-flex pf-video-radios flex-wrap">
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="pro_video_source" id="vs_yt_c" value="youtube" checked>
                    <label class="form-check-label" for="vs_yt_c"><i class="fa fa-youtube-play text-danger"></i> YouTube</label>
                </div>
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="pro_video_source" id="vs_up_c" value="upload">
                    <label class="form-check-label" for="vs_up_c"><i class="fa fa-upload text-primary"></i> Upload</label>
                </div>
            </div>
            <div id="yt_section_c">
                <input type="text" name="pro_video" id="pro_video_c" class="form-control form-control-sm" placeholder="YouTube URL / Video ID">
                <div id="yt_preview_c" class="mt-1" style="display:none;">
                    <iframe id="yt_iframe_c" width="100%" height="100" src="" frameborder="0" allowfullscreen style="border-radius:8px;"></iframe>
                </div>
                <small class="text-muted pf-field-hint">YouTube URL or Video ID</small>
            </div>
            <div id="up_section_c" style="display:none;">
                <input type="file" name="pro_video_file" id="pro_video_file_c" class="form-control form-control-sm" accept="video/mp4,video/webm,video/ogg">
                <div id="up_preview_c" class="mt-1" style="display:none;">
                    <video id="up_video_c" width="100%" height="100" controls style="border-radius:8px;background:#000;"></video>
                </div>
                <small class="text-muted pf-field-hint">MP4, WebM, OGG max 40MB</small>
            </div>
        </div>

        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-settings"></i> Settings</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label class="form-label">Type</label>
                    <select class="form-control form-control-sm bg-light" id="product_type" name="product_type">
                        <option value="physical" selected>Physical</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>
                <div id="advance_area" class="col-6">
                    <label class="form-label">Advance</label>
                    <input type="number" name="advance_amount" class="form-control form-control-sm" placeholder="0">
                </div>
                <div class="col-12">
                    <label class="form-label">Brand</label>
                    <select class="form-control form-control-sm select2" name="brand_id">
                        <option value="">None</option>
                        @foreach($brands as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center pf-free-delivery mb-2">
                <label class="switch me-2 mb-0">
                    <input type="checkbox" value="1" name="free_delivery">
                    <span class="slider round"></span>
                </label>
                <small class="text-muted mb-0">Free delivery</small>
            </div>
            <div id="digital_area" style="display:none;" class="p-2 border rounded mb-2 bg-light">
                <label class="form-label">Digital File</label>
                <input type="file" class="form-control form-control-sm mb-1" name="digital_file">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label"><small>Limit</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_limit" value="5">
                    </div>
                    <div class="col-6">
                        <label class="form-label"><small>Days</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_expire_days" value="7">
                    </div>
                </div>
            </div>
            <div class="row text-center g-compact">
                <div class="col-4">
                    <label class="d-block form-label">Status</label>
                    <label class="switch"><input type="checkbox" value="1" name="status" checked><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Hot</label>
                    <label class="switch"><input type="checkbox" value="1" name="topsale"><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Flash</label>
                    <label class="switch"><input type="checkbox" value="1" name="flashsale"><span class="slider round"></span></label>
                </div>
            </div>
        </div>

        <div class="pf-side-foot">
            <button type="submit" class="btn btn-success w-100 shadow rounded-pill"><i class="fe-check-circle me-1"></i> Publish Product</button>
        </div>
    </div>
</div>
