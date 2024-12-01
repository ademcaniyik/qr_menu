<?php require APP_ROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['category']->name; ?> - Yeni Ürün Ekle</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URL_ROOT; ?>/menu_items/index/<?php echo $data['category']->id; ?>" class="btn btn-secondary float-end">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/menu_items/add/<?php echo $data['category']->id; ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" value="<?php echo $data['name']; ?>">
                            <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>" 
                                      id="description" name="description" rows="3"><?php echo $data['description']; ?></textarea>
                            <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Fiyat (₺)</label>
                            <input type="number" step="0.01" class="form-control <?php echo (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" 
                                   id="price" name="price" value="<?php echo $data['price']; ?>">
                            <span class="invalid-feedback"><?php echo $data['price_err']; ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Ürün Görseli</label>
                            <input type="file" class="form-control <?php echo (!empty($data['image_err'])) ? 'is-invalid' : ''; ?>" 
                                   id="image" name="image" accept="image/*">
                            <span class="invalid-feedback"><?php echo $data['image_err']; ?></span>
                            <small class="form-text text-muted">Maksimum dosya boyutu: 5MB. İzin verilen formatlar: JPG, JPEG, PNG</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" checked>
                                <label class="form-check-label" for="is_available">Ürün Mevcut</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview image before upload
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Dosya boyutu 5MB\'dan büyük olamaz.');
                    this.value = '';
                    return;
                }

                // Check file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Sadece JPG, JPEG ve PNG dosyaları yüklenebilir.');
                    this.value = '';
                    return;
                }
            }
        });
    </script>

<?php require APP_ROOT . '/views/inc/footer.php'; ?>
