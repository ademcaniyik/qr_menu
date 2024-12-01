<?php require APP_ROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['category']->name; ?> - Ürünler</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URL_ROOT; ?>/menu_items/add/<?php echo $data['category']->id; ?>" class="btn btn-primary float-end">
                <i class="fas fa-plus"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <?php flash('menu_item_message'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php if(empty($data['menuItems'])) : ?>
                        <p class="text-center">Henüz ürün eklenmemiş.</p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th width="100">Görsel</th>
                                        <th>Ürün Adı</th>
                                        <th>Açıklama</th>
                                        <th>Fiyat</th>
                                        <th width="100">Durum</th>
                                        <th width="200">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody id="menu-item-list">
                                    <?php foreach($data['menuItems'] as $item) : ?>
                                        <tr data-id="<?php echo $item->id; ?>">
                                            <td>
                                                <i class="fas fa-grip-vertical handle" style="cursor: move;"></i>
                                            </td>
                                            <td>
                                                <?php if($item->image) : ?>
                                                    <img src="<?php echo URL_ROOT; ?>/uploads/menu_items/<?php echo $item->image; ?>" 
                                                         alt="<?php echo $item->name; ?>" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px;">
                                                <?php else : ?>
                                                    <img src="<?php echo URL_ROOT; ?>/img/no-image.png" 
                                                         alt="No Image" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px;">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $item->name; ?></td>
                                            <td><?php echo $item->description; ?></td>
                                            <td><?php echo number_format($item->price, 2); ?> ₺</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input availability-toggle" 
                                                           type="checkbox" 
                                                           id="availability_<?php echo $item->id; ?>"
                                                           data-id="<?php echo $item->id; ?>"
                                                           <?php echo $item->is_available ? 'checked' : ''; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?php echo URL_ROOT; ?>/menu_items/edit/<?php echo $item->id; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form class="d-inline" action="<?php echo URL_ROOT; ?>/menu_items/delete/<?php echo $item->id; ?>" method="post" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sortable.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    
    <script>
        // Initialize sortable
        new Sortable(document.getElementById('menu-item-list'), {
            handle: '.handle',
            animation: 150,
            onEnd: function () {
                // Get new order
                const rows = document.querySelectorAll('#menu-item-list tr');
                const items = Array.from(rows).map(row => row.dataset.id);
                
                // Send to server
                fetch('<?php echo URL_ROOT; ?>/menu_items/updateOrder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'items=' + JSON.stringify(items)
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Sıralama güncellenirken bir hata oluştu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Sıralama güncellenirken bir hata oluştu');
                });
            }
        });

        // Handle availability toggle
        document.querySelectorAll('.availability-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const itemId = this.dataset.id;
                
                fetch('<?php echo URL_ROOT; ?>/menu_items/toggleAvailability/' + itemId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Durum güncellenirken bir hata oluştu');
                        // Revert toggle
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Durum güncellenirken bir hata oluştu');
                    // Revert toggle
                    this.checked = !this.checked;
                });
            });
        });
    </script>

<?php require APP_ROOT . '/views/inc/footer.php'; ?>
