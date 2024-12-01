<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $data['title']; ?></h1>
    <p class="lead"><?php echo $data['description']; ?></p>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <h3>QR Menü Sistemi Nedir?</h3>
            <p>QR Menü Sistemi, restoranlar için tasarlanmış modern bir dijital menü çözümüdür. Müşterileriniz QR kodu okutarak menünüze kolayca erişebilir ve siparişlerini verebilir.</p>
        </div>
        <div class="col-md-6">
            <h3>Özellikler</h3>
            <ul>
                <li>Kolay menü yönetimi</li>
                <li>QR kod entegrasyonu</li>
                <li>Mobil uyumlu tasarım</li>
                <li>Kategori bazlı menü organizasyonu</li>
                <li>Anlık menü güncellemeleri</li>
            </ul>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
