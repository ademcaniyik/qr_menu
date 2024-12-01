<?php
// Check if demo mode
$isDemo = isset($data['is_demo']) && $data['is_demo'];
$imageRoot = $isDemo ? URL_ROOT . '/img/demo' : URL_ROOT . '/uploads/menu_items';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --text-color: #2c3e50;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --card-shadow: 0 2px 15px rgba(0,0,0,0.1);
            --footer-bg: #2c3e50;
            --footer-text: #ffffff;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --primary-color: #ecf0f1;
                --secondary-color: #e74c3c;
                --text-color: #ecf0f1;
                --bg-color: #2c3e50;
                --card-bg: #34495e;
                --card-shadow: 0 2px 15px rgba(0,0,0,0.2);
                --footer-bg: #1a252f;
                --footer-text: #ecf0f1;
            }
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .menu-header {
            background-size: cover;
            background-position: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            padding: 4rem 0;
            margin-bottom: 2rem;
            position: relative;
        }

        .menu-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5));
            z-index: 1;
        }

        .menu-header .container {
            position: relative;
            z-index: 2;
        }

        .menu-header.no-image {
            background-color: var(--primary-color);
        }

        .category-title {
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        .menu-item {
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .menu-item .card {
            border: none;
            box-shadow: var(--card-shadow);
            background-color: var(--card-bg);
            border-radius: 8px;
            height: 100%;
            color: var(--text-color);
        }

        .menu-item .card-body {
            padding: 1.25rem;
        }

        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            z-index: 1;
        }

        .unavailable {
            opacity: 0.6;
        }

        .unavailable::after {
            content: 'Mevcut Değil';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            z-index: 1;
            font-weight: bold;
        }

        footer {
            background-color: var(--footer-bg, #2c3e50);
            color: var(--footer-text, #ffffff);
            padding: 2rem 0;
        }

        footer small {
            opacity: 0.8;
            display: block;
            margin-top: 0.5rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .menu-item {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .menu-item:nth-child(2) { animation-delay: 0.1s; }
        .menu-item:nth-child(3) { animation-delay: 0.2s; }
        .menu-item:nth-child(4) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <!-- Menu Header -->
    <div class="menu-header <?php echo empty($data['business']->cover_image) ? 'no-image' : ''; ?>" 
         <?php if(!empty($data['business']->cover_image)): ?>
         style="background-image: url('<?php echo $isDemo ? URL_ROOT . '/img/demo/' . $data['business']->cover_image : URL_ROOT . '/uploads/businesses/' . $data['business']->cover_image; ?>')"
         <?php endif; ?>>
        <div class="container text-center">
            <h1 class="display-4"><?php echo $data['business']->name; ?></h1>
            <?php if(!empty($data['business']->description)): ?>
                <p class="lead"><?php echo $data['business']->description; ?></p>
            <?php endif; ?>
            <?php if(!empty($data['business']->address) || !empty($data['business']->phone)): ?>
                <div class="mt-4">
                    <?php if(!empty($data['business']->address)): ?>
                        <p><i class="fas fa-map-marker-alt me-2"></i><?php echo $data['business']->address; ?></p>
                    <?php endif; ?>
                    <?php if(!empty($data['business']->phone)): ?>
                        <p><i class="fas fa-phone me-2"></i><?php echo $data['business']->phone; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Menu Content -->
    <div class="container mb-5">
        <?php foreach($data['categories'] as $category): ?>
            <?php if(!empty($category->items)): ?>
                <div class="category-section mb-5">
                    <h2 class="category-title"><?php echo $category->name; ?></h2>
                    <div class="row">
                        <?php foreach($category->items as $item): ?>
                            <div class="col-md-6 col-lg-4 menu-item">
                                <div class="card h-100 <?php echo !$item->is_available ? 'unavailable' : ''; ?>">
                                    <?php if(!empty($item->image)): ?>
                                        <img src="<?php echo $imageRoot . '/' . $item->image; ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo $item->name; ?>">
                                    <?php endif; ?>
                                    <div class="price-badge">
                                        <?php echo number_format($item->price, 2); ?> ₺
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $item->name; ?></h5>
                                        <?php if(!empty($item->description)): ?>
                                            <p class="card-text text-muted"><?php echo $item->description; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if(empty($data['categories']) || empty($category->items)): ?>
            <div class="text-center py-5">
                <h3>Henüz menü eklenmemiş</h3>
                <p class="text-muted">Bu işletme henüz menüsünü oluşturmamış.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container text-center">
            <p class="mb-0">  <?php echo date('Y'); ?> <?php echo $data['business']->name; ?> - Tüm hakları saklıdır.</p>
            <small>Powered by QR Menu System</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
