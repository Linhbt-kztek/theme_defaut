<div class="{{ $i == 1 ? 'col-4' : ($i == 0 ? 'col-12' : 'col-6') }}">
    <div class="float-left" style="margin-top:15px">
        <div class="form-group">
            <label class="control-label"><b>{{ $i == 0 ? 'Banner' : 'Ảnh ' . $i }}</b></label>
            <div id="wap_image{{ $i }}">
                @if (empty($banners['image' . $i]))
                    <div id="image{{ $i }}"></div>
                @else
                    <div class="image_div">
                        <div class="img-upload-preview" style="display: flex; position: relative;">
                            <input name="no_edit_image{{ $i }}" type="hidden" value="1">
                            <img src="{{ $banners['image' . $i] }}" alt="image" class="img-responsive"
                                style="height: auto; width: 100%;max-height: 400px;">
                            <div style="position: absolute; top: 0; right: 0;">
                                <button id="remove-image-id-{{ $i }}" type="button"
                                    class="btn btn-sm btn-danger close-btn remove-image-{{ $i }}">
                                    <i class="ri-close-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="previous_image{{ $i }}" value="{{ $images['image' . $i] }}">
                @endif
            </div>
        </div>
    </div>
</div>
