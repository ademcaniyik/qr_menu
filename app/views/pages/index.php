<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <div class="row align-items-center min-vh-100">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">QR Menü Sistemi</h1>
            <p class="lead mb-4">
                İşletmeniz için modern ve kullanıcı dostu dijital menü çözümü. 
                QR kodları ile müşterilerinize kolay erişim sağlayın.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4">
                <a href="<?php echo URLROOT; ?>/auth/register" class="btn btn-primary btn-lg px-4 me-md-2">Hemen Başla</a>
                <a href="<?php echo URLROOT; ?>/pages/about" class="btn btn-outline-secondary btn-lg px-4">Daha Fazla Bilgi</a>
            </div>
        </div>
        <div class="col-lg-6">
            <img src="<?php echo URLROOT; ?>/img/hero-image.svg" alt="QR Menu System" class="img-fluid">
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
