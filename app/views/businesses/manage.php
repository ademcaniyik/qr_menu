<?php require APP_ROOT . '/views/inc/header.php'; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $data['business']->name; ?></h1>
    </div>
    <div class="col-md-6">
        <div class="float-end">
            <a href="<?php echo URL_ROOT; ?>/businesses/edit/<?php echo $data['business']->id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> İşletmeyi Düzenle
            </a>
            <a href="<?php echo URL_ROOT; ?>/menu/generateQR/<?php echo $data['business']->id; ?>" class="btn btn-success">
                <i class="fas fa-qrcode"></i> QR Kod Oluştur
            </a>
        </div>
    </div>
</div>

<?php flash('business_message'); ?>

<div class="row">
    <!-- Business Info -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">İşletme Bilgileri</h5>
                <hr>
                <?php if($data['business']->cover_image): ?>
                    <img src="<?php echo URL_ROOT; ?>/uploads/businesses/<?php echo $data['business']->cover_image; ?>" 
                         class="img-fluid rounded mb-3" 
                         alt="<?php echo $data['business']->name; ?>">
                <?php endif; ?>
                <p><strong>Adres:</strong> <?php echo $data['business']->address; ?></p>
                <p><strong>Telefon:</strong> <?php echo $data['business']->phone; ?></p>
                <?php if($data['business']->description): ?>
                    <p><strong>Açıklama:</strong> <?php echo $data['business']->description; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QR Code Section -->
        <?php if($data['business']->qr_code): ?>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">QR Kod</h5>
                    <hr>
                    <img src="<?php echo $data['business']->qr_code; ?>" 
                         class="img-fluid mb-3" 
                         alt="QR Code">
                    <div class="d-grid gap-2">
                        <a href="<?php echo $data['business']->qr_code; ?>" 
                           class="btn btn-primary" 
                           download="menu_qr_<?php echo $data['business']->slug; ?>.png">
                            <i class="fas fa-download"></i> QR Kodu İndir
                        </a>
                        <a href="<?php echo $data['business']->menu_url; ?>" 
                           class="btn btn-info" 
                           target="_blank">
                            <i class="fas fa-external-link-alt"></i> Menüyü Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Menu Management -->
    <div class="col-md-8">
        <!-- Categories -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Menü Kategorileri</h5>
                    <a href="<?php echo URL_ROOT; ?>/categories/add/<?php echo $data['business']->id; ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Yeni Kategori
                    </a>
                </div>
                <hr>
                <?php if(empty($data['categories'])): ?>
                    <p class="text-center text-muted">Henüz kategori eklenmemiş.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach($data['categories'] as $category): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><?php echo $category->name; ?></h6>
                                    <div>
                                        <a href="<?php echo URL_ROOT; ?>/menu_items/index/<?php echo $category->id; ?>" 
                                           class="btn btn-sm btn-info me-2">
                                            <i class="fas fa-utensils"></i> Ürünler
                                        </a>
                                        <a href="<?php echo URL_ROOT; ?>/categories/edit/<?php echo $category->id; ?>" 
                                           class="btn btn-sm btn-warning me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form class="d-inline" action="<?php echo URL_ROOT; ?>/categories/delete/<?php echo $category->id; ?>" method="post"
                                              onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Analytics (if implemented) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Menü İstatistikleri</h5>
                <hr>
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3><?php echo count($data['categories']); ?></h3>
                        <p class="text-muted">Kategori</p>
                    </div>
                    <div class="col-md-4">
                        <h3><?php echo $data['totalItems']; ?></h3>
                        <p class="text-muted">Ürün</p>
                    </div>
                    <div class="col-md-4">
                        <h3><?php echo $data['viewCount']; ?></h3>
                        <p class="text-muted">Görüntülenme</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/inc/footer.php'; ?>
