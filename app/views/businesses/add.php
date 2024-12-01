<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-body bg-light mt-5">
                <h2>Yeni İşletme Ekle</h2>
                <p>İşletme bilgilerini doldurun</p>
                <form action="<?php echo URL_ROOT; ?>/businesses/add" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="name">İşletme Adı: <sup>*</sup></label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                        <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="description">Açıklama: <sup>*</sup></label>
                        <textarea name="description" class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['description']; ?></textarea>
                        <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="phone">Telefon: <sup>*</sup></label>
                        <input type="text" name="phone" class="form-control <?php echo (!empty($data['phone_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['phone']; ?>">
                        <span class="invalid-feedback"><?php echo $data['phone_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="email">E-posta: <sup>*</sup></label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                        <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="address">Adres: <sup>*</sup></label>
                        <textarea name="address" class="form-control <?php echo (!empty($data['address_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['address']; ?></textarea>
                        <span class="invalid-feedback"><?php echo $data['address_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" for="logo">Logo:</label>
                        <input type="file" name="logo" class="form-control <?php echo (!empty($data['logo_err'])) ? 'is-invalid' : ''; ?>" accept="image/*">
                        <span class="invalid-feedback"><?php echo $data['logo_err']; ?></span>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="submit" value="Kaydet" class="btn btn-success btn-block">
                        </div>
                        <div class="col">
                            <a href="<?php echo URL_ROOT; ?>/businesses" class="btn btn-light btn-block">Geri</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
