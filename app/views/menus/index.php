<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $data['title']; ?></h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/businesses" class="btn btn-light float-end menu-action-btn">
            <i class="fa fa-backward"></i> İşletmelere Dön
        </a>
    </div>
</div>

<?php flash('menu_message'); ?>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="card-title mb-0">
                    <?php echo $data['business']->name; ?>
                    <a href="<?php echo URLROOT; ?>/categories/add/<?php echo $data['business']->id; ?>" class="btn btn-primary float-end menu-action-btn">
                        <i class="fa fa-plus"></i> Yeni Kategori
                    </a>
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($data['categories'])) : ?>
                    <p>Henüz kategori eklenmemiş.</p>
                <?php else : ?>
                    <?php foreach($data['categories'] as $category) : ?>
                        <div class="card category-card mb-3">
                            <div class="card-header">
                                <div class="card-title">
                                    <span><?php echo $category->name; ?></span>
                                    <div class="category-actions">
                                        <a href="<?php echo URLROOT; ?>/menus/add/<?php echo $category->id; ?>" class="menu-action-btn btn btn-success" title="Ürün Ekle">
                                            <i class="fa fa-plus"></i> Ürün Ekle
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->id; ?>" class="menu-action-btn btn btn-primary" title="Kategoriyi Düzenle">
                                            <i class="fa fa-edit"></i> Düzenle
                                        </a>
                                        <form class="d-inline" action="<?php echo URLROOT; ?>/categories/delete/<?php echo $category->id; ?>" method="post">
                                            <button type="submit" class="menu-action-btn btn btn-danger" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')" title="Kategoriyi Sil">
                                                <i class="fa fa-trash"></i> Sil
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($category->description)) : ?>
                                    <p class="card-text"><?php echo $category->description; ?></p>
                                <?php endif; ?>
                                
                                <?php if (empty($category->items)) : ?>
                                    <p>Bu kategoride henüz ürün yok.</p>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover menu-table">
                                            <thead>
                                                <tr>
                                                    <th>Ürün Adı</th>
                                                    <th>Açıklama</th>
                                                    <th>Fiyat</th>
                                                    <th width="150">İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($category->items as $item) : ?>
                                                    <tr>
                                                        <td><?php echo $item->name; ?></td>
                                                        <td><?php echo $item->description; ?></td>
                                                        <td><?php echo number_format($item->price, 2); ?> TL</td>
                                                        <td>
                                                            <div class="category-actions">
                                                                <a href="<?php echo URLROOT; ?>/menu_items/edit/<?php echo $item->id; ?>" class="menu-action-btn btn btn-primary" title="Ürünü Düzenle">
                                                                    <i class="fa fa-edit"></i> Düzenle
                                                                </a>
                                                                <form class="d-inline" action="<?php echo URLROOT; ?>/menu_items/delete/<?php echo $item->id; ?>" method="post">
                                                                    <button type="submit" class="menu-action-btn btn btn-danger" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" title="Ürünü Sil">
                                                                        <i class="fa fa-trash"></i> Sil
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
