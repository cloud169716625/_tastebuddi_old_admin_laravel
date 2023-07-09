
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="author" content=""/>
    <title><?php echo isset( $title ) ? $title : env('APP_NAME')  ?></title>
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo asset('/themes/clean/css/styles.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo asset('/themes/clean/css/admin.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo asset( '/css/icons/icomoon/styles.css' ) ?>" />
    <?php echo \Helpers\Layout::instance()->renderPageStyles(); ?>
</head>
<body>
<div class="page-container">
    <div class="page-sidebar">
        <a class="logo-box" href="<?php echo Url('') ?>">
                <img src="/images/app-logo.png" class="img-fluid" style="width:72px"/>
        </a>
        <div class="center-block" style="text-align: center">
            <span class="font-18 f-bold"><?php echo env( 'APP_NAME') ?></span>
        </div>
        <hr />
        <div class="page-sidebar-inner">
            <div class="page-sidebar-menu">
                <?php echo \App\Http\Controllers\SubMenus\SubMenusController::render( request() ) ?>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="page-header" style="padding:0">
            <div class="search-form">
                <form action="#" method="GET">
                    <div class="input-group"> <input type="text" name="search" class="form-control search-input" placeholder="Type something...">
                        <span class="input-group-btn"> <button class="btn btn-default" id="close-search" type="button"><i class="mdi mdi-close"></i></button> </span>
                    </div>
                </form>
            </div>
            <nav class="navbar navbar-default" style="margin:0;padding:0">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <div class="logo-sm"> <a href="javascript:void(0)" id="sidebar-toggle-button"><i class="fa fa-bars"></i></a>
                            <a class="logo-box" href=""><span></span></a>
                        </div>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav d-res-none-full">
                            <li><a href="javascript:void(0)" id="collapsed-sidebar-toggle-button"><i class="mdi mdi-menu"></i></a></li>
                            <li><a href="javascript:void(0)" id="toggle-fullscreen"><i class="mdi mdi-fullscreen"></i></a></li>
                            <li><a href="javascript:void(0)" id="search-button"><i class="mdi mdi-magnify"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="javascript:void(0)" class="right-sidebar-toggle" data-sidebar-id="main-right-sidebar"><i class="mdi mdi-email-outline"></i></a></li>
                            <li class="dropdown"> <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-bell-outline"></i></a>
                                <ul class="dropdown-menu dropdown-lg dropdown-content">
                                    <li class="drop-title">Notifications<a href="#" class="drop-title-link"><i class="fa fa-angle-right"></i></a></li>
                                    <li class="slimscroll dropdown-notifications">
                                        <ul class="list-unstyled dropdown-oc">
                                            <li> <a href="#"><span class="notification-badge bg-primary"><i class="fa fa-photo"></i></span> <span class="notification-info">Finished uploading photos to gallery <b>"South Africa"</b>. <small class="notification-date">20:00</small> </span></a> </li>
                                            <li> <a href="#"><span class="notification-badge bg-primary"><i class="fa fa-at"></i></span> <span class="notification-info"><b>John Doe</b> mentioned you in a post "Update v1.5".<br><small class="notification-date">06:07</small> </span></a> </li>
                                            <li> <a href="#"><span class="notification-badge bg-danger"><i class="fa fa-bolt"></i></span> <span class="notification-info">4 new special offers from the apps you follow! <small class="notification-date">Yesterday</small> </span></a> </li>
                                            <li> <a href="#"><span class="notification-badge bg-success"><i class="fa fa-bullhorn"></i></span> <span class="notification-info">There is a meeting with <b>Ethan</b> in 15 minutes! <small class="notification-date">Yesterday</small> </span></a> </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown user-dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <img src="http://themesboss.com/fadmin/assets/images/avatar/avatar-1.jpg" alt="" class="img-circle"></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Profile</a></li>
                                    <li><a href="#">Calendar</a></li>
                                    <li><a href="#"><span class="badge pull-right badge-danger">42</span>Messages</a></li>
                                    <li role="separator" class="divider"></li><li><a href="#">Account Settings</a></li>
                                    <li><a href="#">Log Out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div class="page-inner" id="content" v-cloak>
            <?php echo $content;  ?>
        </div>
    </div>
</div>
<script src="/js/app.js"></script>
<?php echo \Helpers\Layout::instance()->renderPageScripts() ?>
</body>
</html>