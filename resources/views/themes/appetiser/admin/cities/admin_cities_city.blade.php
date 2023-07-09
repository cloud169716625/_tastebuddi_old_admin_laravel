@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <input type="hidden"
           name="cid"
           id="cid"
           value="<?php echo \App\Helpers\Utils::convertInt( $city_id ) ?>"
    />
    <city-info/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/cities/cities.js') }}"></script>
@endpush
