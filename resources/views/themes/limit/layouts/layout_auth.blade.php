<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo env('SITE_NAME') ?></title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">

    <!-- Bundle this css --->
    <link href="/themes/limit/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="/themes/limit/css/core.css" rel="stylesheet" type="text/css">
    <link href="/themes/limit/css/components.css" rel="stylesheet" type="text/css">
    <link href="/themes/limit/css/colors.css" rel="stylesheet" type="text/css">

    <link href="/themes/limit/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="/css/fa/css/font-awesome.css" rel="stylesheet" type="text/css">
    <?php //echo \Helpers\Layout::instance()->renderPageStyles() ?>
    <!-- /global stylesheets -->

    <!-- Core JS files -->

    <!-- /core JS files -->
</head>

<body>

<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
        <a class="navbar-brand" href=""><img src="/images/logo.png" alt=""></a>
        <a class="navbar-brand"><b><?php echo env('SITE_NAME') ?></b></a>
    </div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container" style="padding: 0;margin-top:32px;">

<!-- Page content -->
    @yield('content')
<!-- /page content -->

</div>
<!-- /page container -->


<!-- Footer -->
<div class="footer text-muted" style="text-align:center;width:100%">
    &copy  <?php echo env('SITE_NAME') ?> <?php echo date( 'Y' ) ?>
</div>
<!-- /footer -->
<script type="text/javascript" src="/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/plugins/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="/plugins/vue/vue-2.4.4.js"></script>

<?php //echo \Helpers\Layout::instance()->renderPageScripts() ?>

<script>
    var lVue = new Vue({
        el:'#lDiv',
        data:{
            logging: false,
            spinner: '<i class="fa fa-refresh fa-spin"></i>'
        },
        methods:{
            init(){
                   
            },
            login(){
                this.logging  = true
            }
        },
        mounted:function(){
            this.init();
        }
    });
    
</script>
</body>
</html>


