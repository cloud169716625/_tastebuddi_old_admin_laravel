
<div class="row" style="">
    <div class="col-lg-4 col-lg-offset-4"></div>
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded">
            <div class="text-center">
                <div><img src="/images/app-logo.png" class="img-fluid"/></div>
                <h3 class="text-uppercase font-weight-bold">Appetiser</h3>
                <p class="mt-10 mb-30">Sign into your account</p>
            </div>
            <?php if( session( 'has_error' ) ){ ?>
                <div class="alert alert-danger">
                    <?php echo session( 'message' ) ?>
                </div>
            <?php } ?>

            <form class="login_account" method="POST">
                <div class="row">
                    <div class="col-lg-12 ">
                        <input type="text" name="email" value="<?php echo request('email') ?>"
                               class="form-control bg-white login-input" placeholder="Email" >
                    </div>
                    <div class="col-lg-12">
                        <input type="password" name="pwd" class="login-input form-control bg-white" id="" placeholder="Password">
                    </div>
                    <div class="col-lg-12">
                        <div class="checkbox">
                            <label> <a href="<?php echo Url('password/recover') ?>">Forgot Password ?</a> </label>
                        </div>
                    </div>
                    <div class="col-lg-12 mb-30 mt-10">
                        <button class="btn btn-danger w-100" v-html="signing_in ? spinner : 'Sign in'  " @click="login()"></button>
                    </div>
                </div>
                <?php echo csrf_field() ?>
            </form>
        </div>
        <div class="text-center mt-20">
            <p>
                <small class="text-dark mr-5">Don't have an account ?</small>
                <a href="<?php echo Url('Register') ?>" class="text-dark font-weight-bold">Create an account</a>
            </p>
        </div>
    </div>
</div>

