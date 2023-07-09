@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <input type="hidden"
           name="location_id"
           id="location_id"
           value="{{ $location_id }}"
    />
    <location-info/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/locations/locations.js') }}"></script>
@endpush
