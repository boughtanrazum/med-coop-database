<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "med_coop");
    
    if ($conn->connect_error) {
        $message = "Ошибка подключения: " . $conn->connect_error;
    } else {
        $checkup = intval($_POST['checkup']);
        $medicine = intval($_POST['medicine']);
        $method = $conn->real_escape_string($_POST['method']);
        
        $check_checkup = $conn->query("SELECT IND FROM Checkup WHERE IND = $checkup");
        if ($check_checkup->num_rows == 0) {
            $message = "Ошибка: указанный осмотр не существует!";
        } 
        
        elseif ($conn->query("SELECT IND FROM Medicine WHERE IND = $medicine")->num_rows == 0) {
            $message = "Ошибка: указанное лекарство не существует!";
        }
        else {
            $result = $conn->query("SELECT MAX(IND) as max_id FROM Prescription");
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ? $row['max_id'] + 1 : 1);
            
            $sql = "INSERT INTO Prescription (IND, Checkup, Medicine, Method) 
                    VALUES ($next_id, $checkup, $medicine, '$method')";
            
            if ($conn->query($sql) === TRUE) {
                $conn->close();
                header("Location: prescription_manage.php?success=1");
                exit();
            } else {
                $message = "Ошибка: " . $conn->error . "<br>";
                
                $result = $conn->query("SELECT IND FROM Prescription ORDER BY IND");
                $used_ids = [];
                while($row = $result->fetch_assoc()) {
                    $used_ids[] = $row['IND'];
                }
                
                $new_id = 1;
                while (in_array($new_id, $used_ids)) {
                    $new_id++;
                }
                
                $sql2 = "INSERT INTO Prescription (IND, Checkup, Medicine, Method) 
                        VALUES ($new_id, $checkup, $medicine, '$method')";
                
                if ($conn->query($sql2) === TRUE) {
                    $conn->close();
                    header("Location: prescription_manage.php?success=1");
                    exit();
                } else {
                    $message = "Критическая ошибка: " . $conn->error;
                }
            }
        }
        
        $conn->close();
    }
} else {
    $message = "Неверный метод запроса";
}
?>
