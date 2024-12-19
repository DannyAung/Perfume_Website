// Function to toggle the discount fields based on the discount status
function toggleDiscountField() {
    const discountAvailable = document.getElementById('discount_available').value;
    const discountPercentageField = document.getElementById('discount_field');
    const discountedPriceField = document.getElementById('discounted_price_field');

    if (discountAvailable === 'Yes') {
        discountPercentageField.style.display = 'block';
        discountedPriceField.style.display = 'block';
    } else {
        discountPercentageField.style.display = 'none';
        discountedPriceField.style.display = 'none';
    }
}

// Ensure the discount fields are properly displayed on page load
window.onload = function() {
    toggleDiscountField();  // Call to show/hide based on initial value of discount_available
}


 // Function to toggle discount fields based on discount availability
function toggleDiscountField() {
    var discountField = document.getElementById('discount_field');
    var discountedPriceField = document.getElementById('discounted_price_field');
    var discountAvailable = document.getElementById('discount_available').value;

    if (discountAvailable === 'Yes') {
        discountField.style.display = 'block';
        discountedPriceField.style.display = 'block';
    } else {
        discountField.style.display = 'none';
        discountedPriceField.style.display = 'none';
    }
}

function calculateDiscount() {
    var price = parseFloat(document.getElementById('price').value);
    var discountPercentage = parseFloat(document.getElementById('discount_percentage').value);
    if (!isNaN(price) && !isNaN(discountPercentage)) {
        var discountedPrice = price - (price * (discountPercentage / 100));
        document.getElementById('discounted_price').value = discountedPrice.toFixed(2);
    }
}
