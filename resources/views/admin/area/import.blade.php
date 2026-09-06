<div class="modal fade" id="import_area_modal" tabindex="-1" aria-labelledby="importAreaModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('import') }} {{ __('areas') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('areas.import') }}" method="POST" enctype="multipart/form-data" class="form">
                @csrf
                <div class="row gx-20">
                    <div class="col-12">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="importFile" class="form-label mb-0">{{ __('file') }} (.xlsx, .csv) <span class="text-danger">*</span></label>
                                <a href="{{ route('areas.download-sample') }}" class="btn btn-sm sg-btn-outline-primary">
                                    <i class="las la-download"></i> {{ __('download_sample') }}
                                </a>
                            </div>
                            <input type="file" class="form-control rounded-2" id="importFile" name="file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted mt-2 d-block">
                                {{ __('sample_columns') }}: <strong>division, district, thana, delivery_zone, area_name</strong>
                            </small>
                            <div class="nk-block-des text-danger">
                                <p class="file_error error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center mt-30">
                    <button type="submit" class="btn sg-btn-primary">{{ __('import') }}</button>
                    @include('backend.common.loading-btn', ['class' => 'btn sg-btn-primary'])
                </div>
            </form>
        </div>
    </div>
</div>
