@if ($parcel && $parcel->events)
    <section class="tracking__section pt-90">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tracking__wrapper">
                        @php
                            $groupedEvents = $parcel->events->groupBy(function ($item) {
                                return Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
                            });
                        @endphp

                        @foreach ($groupedEvents as $date => $events)
                            <div class="tracking__inner">
                                <div class="tracking__date">{{ Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
                                @foreach ($events as $event)
                                    <div class="tracking__item-step">
                                        <div class="tracking__item-text">
                                            <span class="tracking__item-time">{{ date('h:i a', strtotime($event->created_at)) }}</span>
                                        </div>
                                        <div class="tracking__item-status">
                                            <span class="tracking__item-status-dot"></span>
                                            <span class="tracking__item-status-line"></span>
                                        </div>
                                        <div class="tracking__item-text">
                                            @if ($event->title == 'parcel_cancel_event')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                <p>{{ $event->cancel_note != '' ? __('reason') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'assign_pickup_man_event')
                                                <p class="tracking__item-title">
                                                    <strong>{{ $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name }}</strong>
                                                    {{ __('is Assigned for Pickup') }}
                                                </p>
                                                <p>{{ __('pickup_man') }}:
                                                    <strong><span class="text">{{ $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name }}</span></strong>
                                                </p>
                                                <p>{{ __('phone_number') }}:
                                                    <strong><span class="text">{{ $event->pickupPerson->phone_number }}</span></strong>
                                                </p>
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_re_schedule_delivery_event')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                <p>{{ __('delivery_man') }}:
                                                    <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                </p>
                                                <p>{{ __('phone_number') }}:
                                                    <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                </p>
                                                @if ($event->thirdParty)
                                                    <p>{{ __('third_party') }}:
                                                        <strong>{{ $event->thirdParty->name . ' (' . $event->thirdParty->address . ')' }}</strong>
                                                    </p>
                                                @endif
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_return_to_wirehouse')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_return_assign_to_merchant_event')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                @if ($event->returnPerson)
                                                    <p>{{ __('delivery_man') }}:
                                                        <strong>{{ $event->returnPerson->user->first_name . ' ' . $event->returnPerson->user->last_name }}</strong>
                                                    </p>
                                                    <p>{{ __('phone_number') }}:
                                                        <strong>{{ $event->returnPerson->phone_number }}</strong>
                                                    </p>
                                                @endif
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_partial_delivered_event')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                @if ($event->deliveryPerson)
                                                    <p>{{ __('delivery_man') }}:
                                                        <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                    </p>
                                                    <p>{{ __('phone_number') }}:
                                                        <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                    </p>
                                                @endif
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_delivered_event')
                                                <p>{{ 'Yes!! ' }}
                                                    <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                    {{ __($event->title) }}
                                                </p>
                                                @if ($event->deliveryPerson)
                                                    <p>{{ __('delivery_man') }}:
                                                        <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                    </p>
                                                    <p>{{ __('phone_number') }}:
                                                        <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                    </p>
                                                @endif
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @elseif($event->title == 'parcel_received_event')
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @else
                                                <p class="tracking__item-title">{{ __($event->title) }}</p>
                                                <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
