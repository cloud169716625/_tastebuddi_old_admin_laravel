@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <city-create/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/cities/cities.js') }}"></script>
@endpush
