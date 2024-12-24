<?php
require_once('libs/tcpdf/tcpdf.php');

// Check if the order ID is provided
if (!isset($_GET['order_id'])) {
    echo "No order ID provided.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit;
}

$order = $order_result->fetch_assoc();

// Fetch cart items
$cart_items_sql = "SELECT ci.quantity, p.product_name, p.price, p.discounted_price 
                   FROM cart_items ci 
                   JOIN products p ON ci.product_id = p.product_id 
                   WHERE ci.order_id = ?";
$cart_items_stmt = $conn->prepare($cart_items_sql);
$cart_items_stmt->bind_param("i", $order_id);
$cart_items_stmt->execute();
$cart_items_result = $cart_items_stmt->get_result();

$cart_items = [];
while ($item = $cart_items_result->fetch_assoc()) {
    $cart_items[] = $item;
}

$conn->close();

// Create PDF using TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Order Receipt');
$pdf->SetHeaderData('', 0, 'Order Receipt', 'Thank you for shopping with us!');
$pdf->setHeaderFont(['helvetica', '', 10]);
$pdf->setFooterFont(['helvetica', '', 8]);
$pdf->SetMargins(15, 27, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(true, 25);

$pdf->AddPage();

// Add content to the PDF
$html = '<h1>Order Receipt</h1>';
$html .= '<p>Order ID: ' . htmlspecialchars($order['order_id']) . '</p>';
$html .= '<p>Name: ' . htmlspecialchars($order['name']) . '</p>';
$html .= '<p>Address: ' . htmlspecialchars($order['address']) . '</p>';
$html .= '<p>Phone: ' . htmlspecialchars($order['phone']) . '</p>';
$html .= '<p>Email: ' . htmlspecialchars($order['email']) . '</p>';
$html .= '<p>Payment Method: ' . htmlspecialchars($order['payment_method']) . '</p>';
$html .= '<p>Total Price: $' . number_format($order['total_price'], 2) . '</p>';

$html .= '<h3>Cart Items</h3>';
$html .= '<table border="1" cellspacing="3" cellpadding="4">
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>';
foreach ($cart_items as $item) {
    $regular_price = $item['price'];
    $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : $regular_price;
    $item_price = $discounted_price;
    $item_subtotal = $item_price * $item['quantity'];

    $html .= '<tr>
                <td>' . htmlspecialchars($item['product_name']) . '</td>
                <td>$' . number_format($item_price, 2) . '</td>
                <td>' . $item['quantity'] . '</td>
                <td>$' . number_format($item_subtotal, 2) . '</td>
              </tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF to the browser
$pdf->Output('Order_Receipt_' . $order_id . '.pdf', 'I');
?>
