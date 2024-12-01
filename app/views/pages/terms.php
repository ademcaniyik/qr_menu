<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $data['title']; ?></h1>
    <p class="lead"><?php echo $data['description']; ?></p>
    
    <div class="mt-5">
        <h3>1. Hizmet Kullanım Koşulları</h3>
        <p>QR Menü Sistemi'ni kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız.</p>
        
        <h3>2. Hesap Güvenliği</h3>
        <p>Hesabınızın güvenliğinden siz sorumlusunuz. Şifrenizi kimseyle paylaşmayın.</p>
        
        <h3>3. Gizlilik</h3>
        <p>Kişisel verileriniz gizlilik politikamıza uygun olarak işlenir.</p>
        
        <h3>4. Hizmet Değişiklikleri</h3>
        <p>Hizmetlerimizde önceden haber vermeksizin değişiklik yapma hakkımız saklıdır.</p>
        
        <h3>5. Sorumluluk Reddi</h3>
        <p>Hizmetimizin kullanımından doğabilecek zararlardan sorumlu değiliz.</p>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
