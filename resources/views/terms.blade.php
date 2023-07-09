@extends( 'themes.appetiser.layouts.layout_landing' )

@section('title')
    Terms and Conditions
@endsection

@section('content')
    <Terms/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/landing/terms.js') }}"></script>
@endpush
