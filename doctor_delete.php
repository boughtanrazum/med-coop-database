<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$redirect_url = "doctor_manage.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $conn = new mysqli("localhost", "root", "", "med_coop");
    
    if ($conn->connect_error) {
        $error_message = urlencode("Ошибка подключения к базе данных: " . $conn->connect_error);
        $redirect_url .= "?error=" . $error_message;
    } else {
        $conn->begin_transaction();
        
        try {
            $update_sql = "UPDATE Checkup SET Doctor = NULL WHERE Doctor = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("i", $id);
            
            if (!$stmt_update->execute()) {
                throw new Exception($conn->error);
            }
            $stmt_update->close();
            
            $sql = "DELETE FROM Doctor WHERE IND = ?";
            $stmt_delete = $conn->prepare($sql);
            $stmt_delete->bind_param("i", $id);
            
            if (!$stmt_delete->execute()) {
                throw new Exception($conn->error);
            }
            
            $conn->commit();
            $redirect_url .= "?deleted=1";
            $stmt_delete->close();
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = urlencode($e->getMessage());
            $redirect_url .= "?error=" . $error_message;
        }
        
        $conn->close();
    }
} else {
    $error_message = urlencode("Не указан ID врача!");
    $redirect_url .= "?error=" . $error_message;
}

header("Location: $redirect_url");
exit();
?>