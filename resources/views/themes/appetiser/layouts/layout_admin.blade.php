<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TravelBuddi Admin</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="/themes/appetiser/css/bootstrap-4.3.1.css" >
    <link rel="stylesheet" type="text/css" href="/themes/appetiser/css/app.css" />

    <!-- Font awesome CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo asset( '/css/icons/icomoon/styles.css' ) ?>" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/app.css') }}" />

    <?php echo \Helpers\Layout::instance()->renderPageStyles() ?>

    <style>
        [v-cloak] {display: none}
    </style>

    @stack('scripts-top')
</head>

<body>
    <div id="app">
        <div class="container-fluid back-default">
            <div class="row">

                <div class="left-navigation">
                    <div class="logo-holder p-20">
                        <h4 style="margin: 0">TravelBuddi</h4>
                    </div>
                    <div class="main-nav back-primary">
                        <!-- Move this into a partial or component -->
                        <ul class="nav-list">
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/users' ) }}" class="black">Users</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/categories' ) }}" class="black">Categories</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/countries' ) }}" class="black">Countries</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/cities' ) }}" class="black">Cities</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/locations' ) }}" class="black">Locations</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/items' ) }}" class="black">Items</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/reports' ) }}" class="black">Reports</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'admin/settings' ) }}" class="black">Settings</a></li>
                            <li class="nav-item m-top f-strong"><a href="{{ url( 'logout' ) }}" class="black">Logout</a></li>
                        </ul>
                    </div>
                </div>

                <div class="main-page" id="content" v-cloak>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts-bottom')
</body>
</html>


