<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['business']->name; ?> - Menü</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="qr-menu-container">
    <!-- İşletme Başlığı -->
    <div class="restaurant-header">
        <div class="container text-center">
            <?php if(!empty($data['business']->logo_path)): ?>
                <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $data['business']->logo_path; ?>" 
                     alt="<?php echo $data['business']->name; ?>" class="restaurant-logo">
            <?php endif; ?>
            <h1 class="restaurant-name"><?php echo $data['business']->name; ?></h1>
            <p class="menu-name"><?php echo $data['menu']->name; ?></p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <!-- Kategori Menüsü -->
            <div class="col-lg-3 mb-4">
                <div class="category-nav">
                    <div class="list-group">
                        <?php foreach($data['categories'] as $category): ?>
                            <a href="#category-<?php echo $category->id; ?>" 
                               class="list-group-item list-group-item-action">
                                <?php echo $category->name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Ürünler -->
            <div class="col-lg-9">
                <?php foreach($data['categories'] as $category): ?>
                    <div id="category-<?php echo $category->id; ?>" class="menu-category">
                        <h2 class="category-title"><?php echo $category->name; ?></h2>
                        <?php if(!empty($category->description)): ?>
                            <p class="category-description"><?php echo $category->description; ?></p>
                        <?php endif; ?>

                        <?php if(!empty($category->menu_items)): ?>
                            <div class="row g-4">
                                <?php foreach($category->menu_items as $item): ?>
                                    <?php if($item->is_available): ?>
                                        <div class="col-md-6">
                                            <div class="menu-item">
                                                <?php if($item->image): ?>
                                                    <div class="menu-item-image-container">
                                                        <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $item->image; ?>" 
                                                             class="menu-item-image" 
                                                             alt="<?php echo $item->name; ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="menu-item-content">
                                                    <div class="menu-item-header">
                                                        <h3 class="menu-item-name"><?php echo $item->name; ?></h3>
                                                        <span class="menu-item-price">
                                                            <?php echo number_format($item->price, 2); ?> ₺
                                                        </span>
                                                    </div>
                                                    <?php if($item->description): ?>
                                                        <p class="menu-item-description">
                                                            <?php echo $item->description; ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-category">
                                Bu kategoride henüz ürün bulunmuyor.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Özel CSS Stilleri -->
<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #e74c3c;
    --background-color: #f8f9fa;
    --text-color: #2c3e50;
    --light-text: #7f8c8d;
    --border-color: #ecf0f1;
}

body {
    background-color: var(--background-color);
    color: var(--text-color);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

.qr-menu-container {
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* Restaurant Header */
.restaurant-header {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.restaurant-logo {
    max-height: 120px;
    margin-bottom: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.restaurant-name {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.menu-name {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 0;
}

/* Category Navigation */
.category-nav {
    position: sticky;
    top: 20px;
}

.list-group-item {
    border: none;
    margin-bottom: 0.5rem;
    border-radius: 8px !important;
    color: var(--text-color);
    font-weight: 500;
    transition: all 0.3s ease;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.list-group-item:hover,
.list-group-item.active {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: white;
    transform: translateX(5px);
}

/* Menu Categories */
.menu-category {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.category-title {
    color: var(--primary-color);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.category-description {
    color: var(--light-text);
    margin-bottom: 2rem;
}

/* Menu Items */
.menu-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.menu-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.menu-item-image-container {
    position: relative;
    padding-top: 66.67%; /* 3:2 Aspect Ratio */
    overflow: hidden;
}

.menu-item-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.menu-item:hover .menu-item-image {
    transform: scale(1.05);
}

.menu-item-content {
    padding: 1.5rem;
}

.menu-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.menu-item-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color);
    margin: 0;
}

.menu-item-price {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1rem;
}

.menu-item-description {
    color: var(--light-text);
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.5;
}

.empty-category {
    text-align: center;
    padding: 2rem;
    color: var(--light-text);
    background: var(--background-color);
    border-radius: 8px;
}

@media (max-width: 991px) {
    .category-nav {
        position: relative;
        top: 0;
        margin-bottom: 2rem;
    }

    .list-group {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
    }

    .list-group-item {
        flex: 0 0 auto;
        margin-right: 0.5rem;
        margin-bottom: 0;
        white-space: nowrap;
    }
}

@media (max-width: 767px) {
    .restaurant-header {
        padding: 2rem 0;
    }

    .restaurant-name {
        font-size: 2rem;
    }

    .menu-category {
        padding: 1.5rem;
    }

    .menu-item-content {
        padding: 1rem;
    }
}
</style>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Smooth Scroll Script -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            document.querySelectorAll('.list-group-item').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
        }
    });
});

// Sayfa yüklendiğinde ilk kategoriyi aktif yap
window.addEventListener('load', () => {
    const firstCategory = document.querySelector('.list-group-item');
    if (firstCategory) {
        firstCategory.classList.add('active');
    }
});

// Scroll olayını dinle ve görünür kategoriyi vurgula
window.addEventListener('scroll', () => {
    const categories = document.querySelectorAll('.menu-category');
    const navItems = document.querySelectorAll('.list-group-item');
    
    let currentCategory = null;
    categories.forEach(category => {
        const rect = category.getBoundingClientRect();
        if (rect.top <= 100 && rect.bottom >= 100) {
            currentCategory = category.id;
        }
    });

    if (currentCategory) {
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === `#${currentCategory}`) {
                item.classList.add('active');
            }
        });
    }
});
</script>

</body>
</html>