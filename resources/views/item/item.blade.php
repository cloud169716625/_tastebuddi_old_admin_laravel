@extends('layouts.app')

@section('title', ':: Item')

@push('scripts-bottom')
@endpush

@section('content')
    <style>
        @media (max-width: 575.98px) {
            h1{
                font-size: 18px;
            }
            .card-title{
                font-size: 16px;
            }
        }
    </style>
    <div class="container py-5 text-center">

        <h1>TravelBuddi</h1>

        @if($item)

        <div class="mb-3 mx-auto" style="max-width: 32rem;">
            @if($item->image_url)
            <img class="img-fluid img-thumbnail" src="{{$item->image_url}}" alt="{{$item->item_name}}">
            @else
            <h4 class="p-3 img-thumbnail">No Image Preview</h4>
            @endif
        </div>

        <h5 class="card-title mb-0">{{$item->item_name}}</h5>


        @else
        <h5 class="card-title mb-0">The item does not exist.</h5>
        @endif


    </div>
@endsection
