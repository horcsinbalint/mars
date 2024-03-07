@extends('layouts.app')
@section('title')
<i class="material-icons left">local_laundry_service</i> @lang('general.washing_reservations')
@endsection

@section('content')
<div class="row">

    @foreach ($washing_days as $day)
        <p>Day start</p>
        Day start: {{ $day->start_time->format('Y-m-d H:i') }}
        @foreach ($day->slots as $slot)
            <p>Slot start: {{ $slot->start_time->format('Y-m-d H:i') }}</p>
            @foreach ($slot->reservations as $reservation)
                {{ $reservation->user()->name }},
            @endforeach
        @endforeach
        <p>Day end</p>
    @endforeach

</div>
@endsection
