@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <city-list/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/cities/cities.js') }}"></script>
@endpush
