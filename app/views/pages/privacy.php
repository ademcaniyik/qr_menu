<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $data['title']; ?></h1>
    <p class="lead"><?php echo $data['description']; ?></p>
    
    <div class="mt-5">
        <h3>1. Toplanan Veriler</h3>
        <p>Hizmetlerimizi kullanırken aşağıdaki verileriniz toplanabilir:</p>
        <ul>
            <li>Ad, soyad, e-posta gibi kişisel bilgiler</li>
            <li>İşletme bilgileri</li>
            <li>Menü ve ürün bilgileri</li>
            <li>Kullanım istatistikleri</li>
        </ul>
        
        <h3>2. Verilerin Kullanımı</h3>
        <p>Toplanan veriler aşağıdaki amaçlarla kullanılır:</p>
        <ul>
            <li>Hizmet kalitesini artırmak</li>
            <li>Müşteri desteği sağlamak</li>
            <li>Yasal yükümlülükleri yerine getirmek</li>
        </ul>
        
        <h3>3. Veri Güvenliği</h3>
        <p>Verileriniz endüstri standardı güvenlik önlemleriyle korunmaktadır.</p>
        
        <h3>4. Çerezler</h3>
        <p>Sitemizde çerezler kullanılmaktadır. Çerez kullanımı hakkında detaylı bilgi için çerez politikamızı inceleyebilirsiniz.</p>
        
        <h3>5. İletişim</h3>
        <p>Gizlilik politikamız hakkında sorularınız için bizimle iletişime geçebilirsiniz.</p>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
