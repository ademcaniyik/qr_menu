<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h1><?php echo $data['menu']->name; ?></h1>
            <p class="lead">İşletme: <?php echo $data['business']->name; ?></p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Menü Detayları</h5>
                    <p class="card-text">Oluşturulma Tarihi: <?php echo date('d/m/Y H:i', strtotime($data['menu']->created_at)); ?></p>
                    
                    <!-- Kategoriler -->
                    <h6>Kategoriler:</h6>
                    <div class="list-group">
                        <?php foreach($data['categories'] as $category): ?>
                            <div class="list-group-item">
                                <h6><?php echo $category->name; ?></h6>
                                <?php if(!empty($category->description)): ?>
                                    <small class="text-muted"><?php echo $category->description; ?></small>
                                <?php endif; ?>
                                
                                <!-- Ürünler -->
                                <?php if(!empty($category->menu_items)): ?>
                                    <div class="ms-3 mt-2">
                                        <?php foreach($category->menu_items as $item): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong><?php echo $item->name; ?></strong>
                                                    <?php if($item->description): ?>
                                                        <br><small class="text-muted"><?php echo $item->description; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary"><?php echo number_format($item->price, 2); ?> ₺</span>
                                                    <?php if(!$item->is_available): ?>
                                                        <span class="badge bg-danger">Stokta Yok</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">QR Kod</h5>
                    <p class="card-text">Bu QR kodu müşterilerinizle paylaşın</p>
                    
                    <!-- QR Code Image -->
                    <img src="<?php echo URLROOT; ?>/qrcodes/generate/<?php echo $data['business']->slug; ?>" 
                         alt="Menu QR Code" 
                         class="img-fluid mb-3">
                    
                    <!-- Download Button -->
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/qrcodes/download/<?php echo $data['business']->slug; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-download"></i> QR Kodu İndir
                        </a>
                        
                        <!-- Preview Link -->
                        <a href="<?php echo URLROOT; ?>/<?php echo $data['business']->slug; ?>" 
                           target="_blank"
                           class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Menüyü Önizle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
