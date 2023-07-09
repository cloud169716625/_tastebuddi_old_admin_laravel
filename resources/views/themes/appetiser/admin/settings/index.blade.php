@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <settings-index/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/settings/settings.js') }}"></script>
@endpush
