<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['title']; ?></h1>
        </div>
    </div>

    <?php flash('business_message'); ?>

    <div class="row">
        <?php foreach($data['businesses'] as $business): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $business->name; ?></h4>
                        <p class="card-text"><?php echo $business->description; ?></p>
                        <hr>
                        <p class="mb-1"><i class="fas fa-map-marker-alt"></i> <?php echo $business->address; ?></p>
                        <p class="mb-1"><i class="fas fa-phone"></i> <?php echo $business->phone; ?></p>
                        <p class="mb-1"><i class="fas fa-envelope"></i> <?php echo $business->email; ?></p>
                        
                        <?php if(isAdminUser()): ?>
                        <p class="mb-1 mt-2">
                            <i class="fas fa-user"></i> Sahibi: 
                            <?php 
                                $owner = $data['userModel']->getUserById($business->user_id);
                                echo $owner ? $owner->username : 'Bilinmiyor';
                            ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <a href="<?php echo URLROOT; ?>/qrcodes/viewQrCode/<?php echo $business->id; ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-qrcode"></i> QR Kod
                            </a>
                            <?php if(hasBusinessAccess($business->id)): ?>
                            <a href="<?php echo URLROOT; ?>/businesses/edit/<?php echo $business->id; ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <form action="<?php echo URLROOT; ?>/businesses/delete/<?php echo $business->id; ?>" 
                                  method="post" 
                                  onsubmit="return confirm('Bu işletmeyi silmek istediğinizden emin misiniz?');">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
