<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$redirect_url = "prescription_manage.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $conn = new mysqli("localhost", "root", "", "med_coop");
    
    if ($conn->connect_error) {
        $error_message = urlencode("Ошибка подключения к базе данных: " . $conn->connect_error);
        $redirect_url .= "?error=" . $error_message;
    } else {
        $sql = "DELETE FROM Prescription WHERE IND = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $redirect_url .= "?deleted=1";
        } else {
            $error_message = urlencode($conn->error);
            $redirect_url .= "?error=" . $error_message;
        }
        
        $stmt->close();
        $conn->close();
    }
} else {
    $error_message = urlencode("Не указан ID рецепта!");
    $redirect_url .= "?error=" . $error_message;
}

header("Location: $redirect_url");
exit();
?>