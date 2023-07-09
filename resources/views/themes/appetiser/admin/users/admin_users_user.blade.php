@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content' )
    <input type="hidden"
           name="uid"
           id="uid"
           value="<?php echo \App\Helpers\Utils::convertInt( $user_id ) ?>"
    />
    <user-info/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/users/users.js') }}"></script>
@endpush





