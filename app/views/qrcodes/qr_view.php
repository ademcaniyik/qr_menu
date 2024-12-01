<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><?php echo $data['business']->name; ?> - QR Kod</h2>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <p class="lead">Bu QR kodu müşterilerinizle paylaşın</p>
                        <small class="text-muted">Müşterileriniz bu QR kodu okutarak menünüze ulaşabilir</small>
                    </div>

                    <!-- QR Code Image -->
                    <div class="qr-code-container mb-4">
                        <img src="<?php echo URLROOT; ?>/qrcodes/generate/<?php echo $data['business']->slug; ?>" 
                             alt="Menu QR Code" 
                             class="img-fluid">
                    </div>

                    <!-- Menu URL -->
                    <div class="mb-4">
                        <p class="mb-2">Menü URL:</p>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   value="<?php echo URLROOT . '/' . $data['business']->slug; ?>" 
                                   readonly>
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    onclick="copyToClipboard(this)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/qrcodes/download/<?php echo $data['business']->slug; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-download"></i> QR Kodu İndir
                        </a>
                        
                        <a href="<?php echo URLROOT; ?>/qrcodes/showMenu/<?php echo $data['business']->slug; ?>" 
                           target="_blank"
                           class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Menüyü Önizle
                        </a>

                        <a href="<?php echo URLROOT; ?>/businesses" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> İşletmelere Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.qr-code-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    display: inline-block;
}

.qr-code-container img {
    max-width: 300px;
}
</style>

<script>
function copyToClipboard(button) {
    const input = button.parentElement.querySelector('input');
    input.select();
    document.execCommand('copy');
    
    // Geçici olarak butonun içeriğini değiştir
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        button.innerHTML = originalHTML;
    }, 2000);
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
