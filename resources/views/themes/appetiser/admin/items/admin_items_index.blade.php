@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <item-list/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/items/items.js') }}"></script>
@endpush
