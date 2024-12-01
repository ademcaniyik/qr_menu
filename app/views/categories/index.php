<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['business']->name; ?> - Kategoriler</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/categories/add/<?php echo $data['business']->id; ?>" class="btn btn-primary float-end">
                <i class="fas fa-plus"></i> Yeni Kategori Ekle
            </a>
        </div>
    </div>

    <?php flash('category_message'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php if(empty($data['categories'])) : ?>
                        <p class="text-center">Henüz kategori eklenmemiş.</p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Kategori Adı</th>
                                        <th>Açıklama</th>
                                        <th width="200">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody id="category-list">
                                    <?php foreach($data['categories'] as $category) : ?>
                                        <tr data-id="<?php echo $category->id; ?>">
                                            <td>
                                                <i class="fas fa-grip-vertical handle" style="cursor: move;"></i>
                                            </td>
                                            <td><?php echo $category->name; ?></td>
                                            <td><?php echo $category->description; ?></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->id; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/menu-items/index/<?php echo $category->id; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-utensils"></i>
                                                </a>
                                                <form class="d-inline" action="<?php echo URLROOT; ?>/categories/delete/<?php echo $category->id; ?>" method="post" onsubmit="return confirmDelete();">
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

    <script>
        function confirmDelete() {
            return confirm('Bu kategoriyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz ve kategorideki tüm menü öğeleri silinecektir.');
        }

        // Initialize Sortable.js
        new Sortable(document.getElementById('category-list'), {
            handle: '.handle',
            animation: 150,
            onEnd: function(evt) {
                const categoryIds = Array.from(evt.to.children).map(row => row.dataset.id);
                
                // Send new order to server
                fetch(`${URLROOT}/categories/reorder/<?php echo $data['business']->id; ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ categoryIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const flash = document.createElement('div');
                        flash.className = 'alert alert-success alert-dismissible fade show mt-3';
                        flash.innerHTML = `
                            Kategori sırası güncellendi
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').insertBefore(flash, document.querySelector('.table-responsive'));
                        
                        // Auto dismiss after 3 seconds
                        setTimeout(() => {
                            flash.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    const flash = document.createElement('div');
                    flash.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    flash.innerHTML = `
                        Kategori sırası güncellenirken bir hata oluştu
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('.card-body').insertBefore(flash, document.querySelector('.table-responsive'));
                });
            }
        });
    </script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
