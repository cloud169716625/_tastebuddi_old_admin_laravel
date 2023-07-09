@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content')
    <item-create/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/items/items.js') }}"></script>
@endpush
