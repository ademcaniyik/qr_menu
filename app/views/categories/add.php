<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-body bg-light mt-5">
                <h2>Yeni Kategori Ekle</h2>
                <p>Lütfen kategori bilgilerini girin</p>
                <form action="<?php echo URLROOT; ?>/categories/add/<?php echo $data['business_id']; ?>" method="post">
                    <div class="form-group mb-3">
                        <label for="name">Kategori Adı: <sup>*</sup></label>
                        <input type="text" name="name" class="form-control form-control-lg <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                        <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Açıklama:</label>
                        <textarea name="description" class="form-control form-control-lg <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['description']; ?></textarea>
                        <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="submit" value="Kaydet" class="btn btn-success btn-block w-100">
                        </div>
                        <div class="col">
                            <a href="<?php echo URLROOT; ?>/categories/index/<?php echo $data['business_id']; ?>" class="btn btn-light btn-block w-100">Geri</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
