@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content' )
    <category-list></category-list>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/categories/categories.js') }}"></script>
@endpush
