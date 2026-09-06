<section class="event__section pt-70">
    <div class="container">
        <div class="row">
            @foreach($events as $event)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="eventBox wow fadeInUp" data-wow-delay=".3s">
                        <a href="#" class="eventBox__thumb">
                            <img src="{{ getFileLink('280X190', $event['image']) }}" alt="event-thumb" />
                        </a>
                        <div class="eventBox__content">
                            <h4 class="title">
                                <a href="#">{{ @$event->language->title }}</a>
                            </h4>
                            <p class="desc">{{ @$event->language->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
