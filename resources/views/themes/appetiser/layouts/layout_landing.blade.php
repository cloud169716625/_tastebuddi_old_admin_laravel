<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('/assets/logo.ico') }}">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('/assets/page.css') }}">
        <title>@yield('title') | TravelBuddi</title>
  </head>
  <body>
    <div class="logo-container">
        <div class="logo-heading">
            <img src="{{ asset('/assets/logo.png') }}" alt="TravelBuddi" height="200px">
        </div>
        <div class="app-title">
            TravelBuddi
        </div>
    </div>
    <div class="content" id='content'>
        @yield('content')
    </div>
    <footer>
        <ul>
            <li>
              <a href="{{ url('privacy') }}">Privacy Policy</a>
            </li>
            <li>
              <a href="{{ url('terms-of-service') }}">Terms and Conditions</a>
          </li>
          <li>
              <a href="{{ url('support') }}">Support</a> 
          </li>
        </ul>
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts-bottom')
</body></html>