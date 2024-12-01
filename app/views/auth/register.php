<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">İşletme Kaydı</h2>
                    <form action="<?php echo URLROOT; ?>/auth/register" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Kişisel Bilgiler</h4>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ad Soyad</label>
                                    <input type="text" name="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Adresi</label>
                                    <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Şifre</label>
                                    <input type="password" name="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Şifre Tekrar</label>
                                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3">İşletme Bilgileri</h4>
                                <div class="mb-3">
                                    <label for="business_name" class="form-label">İşletme Adı</label>
                                    <input type="text" name="business_name" class="form-control <?php echo (!empty($data['business_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['business_name']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['business_name_err']; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="business_phone" class="form-label">İşletme Telefonu</label>
                                    <input type="tel" name="business_phone" class="form-control" value="<?php echo $data['business_phone']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="business_address" class="form-label">İşletme Adresi</label>
                                    <textarea name="business_address" class="form-control" rows="3"><?php echo $data['business_address']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Kayıt Ol</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <p class="text-center mb-0">Zaten hesabınız var mı? <a href="<?php echo URLROOT; ?>/auth/login">Giriş Yapın</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
