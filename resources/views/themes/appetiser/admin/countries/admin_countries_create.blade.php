@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content')
    <country-create/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/countries/countries.js') }}"></script>
@endpush
