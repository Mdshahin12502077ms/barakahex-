<div class="breadcrumb__area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb__title text-center">
                    <h2 class="title">{!! $page_info->title !!}</h2>
                    <p class="desc">{{ __('last_updated') }}: {{ date('M d, Y', strtotime($page_info->updated_at)) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
