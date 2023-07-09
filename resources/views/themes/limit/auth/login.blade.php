@extends('themes.limit.layouts.layout_auth')

@section('content')
    <div id="content-div" class="page-content">
        <div class="col-lg-4 col-lg-offset-4">
            <form action="" method="POST">
                <div class="panel panel-body login-form">
                    <?php if( session('message') ){ ?>
                        <div class="alert alert-<?php echo session('alert') ? session('alert') : 'success' ?>"><?php echo session('message') ?></div>
                    <?php } ?>
                    <div class="row">
                        <div style="align-items: center;display: flex;justify-content: center;">
                            <!--<img src="/images/logo-xs.png" class="img-responsive" />-->
                        </div>

                    </div>
                    <div class="text-center">
                        <h5 class="content-group">Login to your account </h5>
                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input type="text" name="username" class="form-control" placeholder="Username">
                        <div class="form-control-feedback">
                            <i class="icon-user text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input type="password" name="pwd" class="form-control" placeholder="Password">
                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"
                                id="signin-btn" v-html=" logging ? spinner : 'Sign in ' "
                        @click="login">
                        </button>
                    </div>

                    <div class="text-center">
                        <!--<a href="<?php echo Url('forgot_password') ?>">Forgot password?</a>-->
                    </div>
                </div>
                <?php echo csrf_field() ?>
            </form>
        </div>
    </div>
@endsection

