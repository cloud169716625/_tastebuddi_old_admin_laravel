@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content' )
    <user-list/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/users/users.js') }}"></script>
@endpush

