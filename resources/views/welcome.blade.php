<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('/assets/logo.ico') }}">
    <link rel="stylesheet" href="{{ asset('/assets/style.css') }}">
    <title>TravelBuddi</title>


  </head>
  <body>
    <div class="main-container position-ref full-height">
      <div class="content">
        <div>
          <img src="{{ asset('/assets/logo.png') }}" alt="TravelBuddi" height="200px">
        </div>
        <div class="flex-center m-t-sm">
            <h2>TravelBuddi</h2>
          </div>
        <div class="flex-center">
          <div>
            <a href="#"><img class="badge badge-appstore" alt="Download on the Apple Store" src="{{ asset('/assets/app-store-badge.png') }}"></a>
          </div>
          <div>
            <a href="#"><img class="badge badge-playstore" alt="Get it on Google Play" src="{{ asset('/assets/google-play-badge.png') }}"></a>
          </div>
        </div>

        <div class="flex-center m-t-md">
          <!-- <h1>Website Coming Soon</h1> -->
        </div>
        <p>Contact us at <br><a href="mailto:hello@travelbuddi.com">hello@travelbuddi.com</a><br>+62 0812 4620 2862</p>
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
    </div>


</body></html>
