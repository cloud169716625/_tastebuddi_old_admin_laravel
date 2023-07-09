@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content')
    <location-create/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/locations/locations.js') }}"></script>
@endpush
