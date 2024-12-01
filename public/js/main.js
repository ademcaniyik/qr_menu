// Main JavaScript file

// Flash message auto-hide
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        let flash = document.getElementById('msg-flash');
        if(flash) {
            flash.style.transition = 'opacity 0.5s ease';
            flash.style.opacity = '0';
            setTimeout(function() {
                flash.remove();
            }, 500);
        }
    }, 3000);
});

// Image preview before upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Price input formatter
function formatPrice(input) {
    let value = input.value.replace(/[^\d]/g, '');
    value = (parseInt(value) / 100).toFixed(2);
    input.value = value;
}

// Confirm delete
function confirmDelete(message = 'Bu öğeyi silmek istediğinizden emin misiniz?') {
    return confirm(message);
}

// Sort menu items
function updateSort(categoryId) {
    const items = document.querySelectorAll(`#category-${categoryId} .menu-item`);
    const sortData = [];
    
    items.forEach((item, index) => {
        sortData.push({
            id: item.dataset.id,
            sort_order: index
        });
    });

    // Send sort data to server
    fetch('/menu-items/sort', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(sortData)
    });
}
