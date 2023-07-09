@extends( 'themes.appetiser.layouts.layout_admin' )

@section( 'content')
    <input type="hidden"
           name="cid"
           id="cid"
           value="<?php echo \App\Helpers\Utils::convertInt( $country_id ) ?>"
    />
    <country-info/>
@endsection

@push( 'scripts-bottom' )
    <script src="{{ asset('js/countries/countries.js') }}"></script>
@endpush







