@extends( 'themes.appetiser.layouts.layout_landing' )

@section('title')
    Privacy Policy
@endsection

@section('content')
    <Privacy/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/landing/privacy.js') }}"></script>
@endpush
