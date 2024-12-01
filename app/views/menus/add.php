<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Yeni Ürün Ekle</h2>
            <p>Lütfen ürün bilgilerini girin</p>
            <form action="<?php echo URLROOT; ?>/menus/add/<?php echo $data['category_id']; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="name" class="form-label text-dark">Ürün Adı: <sup>*</sup></label>
                    <input type="text" name="name" class="form-control form-control-lg <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                    <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="description" class="form-label text-dark">Açıklama:</label>
                    <textarea name="description" class="form-control form-control-lg <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['description']; ?></textarea>
                    <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="price" class="form-label text-dark">Fiyat: <sup>*</sup></label>
                    <input type="text" name="price" class="form-control form-control-lg <?php echo (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['price']; ?>">
                    <span class="invalid-feedback"><?php echo $data['price_err']; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Kaydet" class="btn btn-success btn-block w-100">
                    </div>
                    <div class="col">
                        <a href="<?php echo URLROOT; ?>/menus" class="btn btn-light btn-block w-100">Geri</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
