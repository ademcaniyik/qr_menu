<?php require APP_ROOT . '/views/inc/header.php'; ?>

<div class="menu-header text-center mb-5">
    <h1 class="display-4 mb-3"><?php echo $data['business']->name; ?></h1>
    <?php if (!empty($data['business']->description)): ?>
        <p class="lead mb-0"><?php echo $data['business']->description; ?></p>
    <?php endif; ?>
</div>

<div class="menu-categories">
    <?php if (!empty($data['categories'])): ?>
        <div class="category-navigation mb-4">
            <nav class="nav nav-pills nav-fill">
                <?php foreach ($data['categories'] as $index => $category): ?>
                    <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                       href="#category-<?php echo $category->id; ?>">
                        <?php echo $category->name; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <?php foreach ($data['categories'] as $category): ?>
            <div id="category-<?php echo $category->id; ?>" class="category-section mb-5">
                <h2 class="category-title h3 mb-4"><?php echo $category->name; ?></h2>
                <?php if (!empty($category->description)): ?>
                    <p class="category-description text-muted mb-4"><?php echo $category->description; ?></p>
                <?php endif; ?>
                
                <div class="row g-4">
                    <?php 
                    $menuItems = array_filter($data['menu_items'], function($item) use ($category) {
                        return $item->category_id === $category->id;
                    });
                    ?>
                    
                    <?php if (!empty($menuItems)): ?>
                        <?php foreach ($menuItems as $item): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card menu-item-card h-100">
                                    <?php if (!empty($item->image)): ?>
                                        <img src="<?php echo asset_url('uploads/menu_items/' . $item->image); ?>" 
                                             class="card-img-top menu-item-image" 
                                             alt="<?php echo $item->name; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0"><?php echo $item->name; ?></h5>
                                            <span class="price-text">₺<?php echo number_format($item->price, 2); ?></span>
                                        </div>
                                        
                                        <?php if (!empty($item->description)): ?>
                                            <p class="card-text text-muted"><?php echo $item->description; ?></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!$item->is_available): ?>
                                            <div class="unavailable-badge">
                                                <span>Şu anda mevcut değil</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-muted text-center">Bu kategoride henüz ürün bulunmuyor.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <h3 class="text-muted">Henüz kategori eklenmemiş</h3>
        </div>
    <?php endif; ?>
</div>

<script>
// Smooth scroll for category navigation
document.querySelectorAll('.category-navigation .nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        // Update active state
        document.querySelectorAll('.category-navigation .nav-link').forEach(navLink => {
            navLink.classList.remove('active');
        });
        this.classList.add('active');
        
        // Smooth scroll
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
});

// Update active category on scroll
window.addEventListener('scroll', function() {
    const categories = document.querySelectorAll('.category-section');
    const navLinks = document.querySelectorAll('.category-navigation .nav-link');
    
    categories.forEach((category, index) => {
        const rect = category.getBoundingClientRect();
        if (rect.top <= 100 && rect.bottom >= 100) {
            navLinks.forEach(link => link.classList.remove('active'));
            navLinks[index].classList.add('active');
        }
    });
});
</script>

<?php require APP_ROOT . '/views/inc/footer.php'; ?>
