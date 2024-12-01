<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Giriş Yap</h2>
            <p>Lütfen bilgilerinizi girin</p>
            <?php flash('register_success'); ?>
            <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                <div class="form-group mb-3">
                    <label for="email">E-posta: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                    <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Şifre: <sup>*</sup></label>
                    <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                    <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Giriş Yap" class="btn btn-success btn-block w-100">
                    </div>
                    <div class="col">
                        <a href="<?php echo URLROOT; ?>/auth/register" class="btn btn-light btn-block w-100">Hesabınız yok mu? Kayıt olun</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
