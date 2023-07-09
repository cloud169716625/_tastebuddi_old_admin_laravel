@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <country-list/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/countries/countries.js') }}"></script>
@endpush

