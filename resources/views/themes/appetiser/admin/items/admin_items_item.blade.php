@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content')
    <input type="hidden"
           name="item_id"
           id="item_id"
           value="{{ $item_id }}"
    />
    <item-info/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/items/items.js') }}"></script>
@endpush
