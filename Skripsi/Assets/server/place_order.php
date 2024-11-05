<?php

$conn = mysqli_connect("localhost","root","","php_project")
        or die("Couldn't connect to database");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include('connection.php');

if (isset($_POST['place_order'])) {
    // Mendapatkan user data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $user_id = 1; // Placeholder; replace this with actual user ID if available
    $order_date = date('Y-m-d H:i:s');

    // Langkah 1: Masukkan data ke dalam tabel pemesanan
    $stmt = $conn->prepare("INSERT INTO pemesanan (id_user, tanggal_pemesanan) VALUES (?, ?); ");

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Mengikat parameter dan mengeksekusi query
    $stmt->bind_param("is", $user_id, $order_date);

    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    
    // Mendapatkan ID pemesanan yang baru dimasukkan
    $id_pemesanan = $stmt->insert_id;
    echo $id_pemesanan;

    $stmt->close();
    $conn->close();

    $insert_detail_query = "INSERT INTO detail_pemesanan (pemesananID, productsid, jumlah) VALUES (?, ?, ?)";

    foreach ($_SESSION['cart'] as $item) {
        $sparepartsid = $item['product_id'];
        $jumlah = $item['product_quantity'];
        
        // Memasukkan setiap item dalam keranjang ke detail pemesanan
        $stmt_detail->bind_param("iii", $id_pemesanan, $sparepartsid, $jumlah);
        $stmt_detail->execute();
    }
    
    $stmt_detail->close();
    $conn->close();

    // Kosongkan keranjang belanja setelah checkout selesai
    unset($_SESSION['cart']);

    // Redirect ke halaman sukses atau halaman lain setelah checkout
    header("Location: index.php"); // ganti dengan halaman yang diinginkan
    exit();

}
?>
