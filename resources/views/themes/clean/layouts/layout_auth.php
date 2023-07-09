<!DOCTYPE html>

<html lang="en"> <head> <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Appetiser Login"/>
    <meta name="keywords" content=""/>
    <meta name="author" content=""/>
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">

    <title></title>
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo asset( '/themes/clean/css/auth.css' ) ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo asset( '/themes/clean/css/styles.css' ) ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo asset( '/css/icons/icomoon/styles.css' ) ?>" />
    <?php echo \Helpers\Layout::instance()->renderPageStyles(); ?>
</head>

<body>
<div class="account-background">
    <div class="account-table">
        <div class="account-table-cell">
            <div class="container" id="content" style="width:100%">
                <?php echo $content  ?>
            </div>
        </div>
    </div>
</div>

<script src="/js/app.js"></script>
<?php echo \Helpers\Layout::instance()->renderPageScripts() ?>

</body>
</html>

