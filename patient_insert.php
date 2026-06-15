<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "med_coop");
    
    if ($conn->connect_error) {
        $message = "Ошибка подключения: " . $conn->connect_error;
    } else {
        $surname = $conn->real_escape_string($_POST['surname']);
        $name = $conn->real_escape_string($_POST['name']);
        $patronymic = $conn->real_escape_string($_POST['patronymic']);
        $gender = intval($_POST['gender']);
        $birth = $conn->real_escape_string($_POST['birth']);
        $address = $conn->real_escape_string($_POST['address']);
        
        $result = $conn->query("SELECT MAX(IND) as max_id FROM Patient");
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ? $row['max_id'] + 1 : 1);
        
        $sql = "INSERT INTO Patient (IND, Surname, Name, Patronymic, Gender, Birth, Address) 
                VALUES ($next_id, '$surname', '$name', '$patronymic', $gender, '$birth', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            $conn->close();
            header("Location: patient_manage.php?success=1");
            exit();
        } else {
            $message = "Ошибка: " . $conn->error . "<br>";
            
            $result = $conn->query("SELECT IND FROM Patient ORDER BY IND");
            $used_ids = [];
            while($row = $result->fetch_assoc()) {
                $used_ids[] = $row['IND'];
            }
            
            $new_id = 1;
            while (in_array($new_id, $used_ids)) {
                $new_id++;
            }
            
            $sql2 = "INSERT INTO Patient (IND, Surname, Name, Patronymic, Gender, Birth, Address) 
                    VALUES ($new_id, '$surname', '$name', '$patronymic', $gender, '$birth', '$address')";
            
            if ($conn->query($sql2) === TRUE) {
                $conn->close();
                header("Location: patient_manage.php?success=1");
                exit();
            } else {
                $message = "Критическая ошибка: " . $conn->error;
            }
        }
        
        $conn->close();
    }
} else {
    $message = "Неверный метод запроса";
}
?>
