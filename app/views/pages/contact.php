<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $data['title']; ?></h1>
    <p class="lead"><?php echo $data['description']; ?></p>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <h3>İletişim Bilgileri</h3>
            <ul class="list-unstyled">
                <li><i class="fas fa-envelope me-2"></i> info@qrmenu.com</li>
                <li><i class="fas fa-phone me-2"></i> +90 (212) 123 45 67</li>
                <li><i class="fas fa-map-marker-alt me-2"></i> İstanbul, Türkiye</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h3>İletişim Formu</h3>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Adınız</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Mesajınız</label>
                    <textarea class="form-control" id="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gönder</button>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
