document.addEventListener('DOMContentLoaded', () => {
    const thumbnails = document.querySelectorAll('.thumbnail-img');
    const mainImage = document.getElementById('main-image');

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            // Update the main image's source
            mainImage.src = thumbnail.src;

            // Add an active class to the clicked thumbnail
            thumbnails.forEach(thumb => thumb.style.border = '1px solid #ddd');
            thumbnail.style.border = '2px solid #007bff';
        });
    });
});

function addToFavorites(productId) {
    // Logic for adding to favorites (You can handle this with AJAX, a PHP request, or by saving to a session/cookie)
    alert('Product ' + productId + ' added to favorites!');
}

//  // Quantity adjustment 
//  // Quantity adjustment functions
//     function increaseQuantity() {
//         let quantityInput = document.getElementById('quantity');
//         let max = parseInt(quantityInput.getAttribute('max'));
//         let current = parseInt(quantityInput.value);
//         if (current < max) {
//             quantityInput.value = current + 1;
//         }
//     }

//     function decreaseQuantity() {
//         let quantityInput = document.getElementById('quantity');
//         let current = parseInt(quantityInput.value);
//         if (current > 1) {
//             quantityInput.value = current - 1;
//         }
//     }