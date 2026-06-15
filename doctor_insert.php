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
        $category = isset($_POST['category']) && !empty($_POST['category']) 
                    ? intval($_POST['category']) 
                    : null;
        
        if ($category !== null) {
            $check_sql = "SELECT IND FROM Category WHERE IND = $category";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                $message = "Ошибка: указанная категория не существует!";
                $category = null;
            }
        }
        
        $result = $conn->query("SELECT MAX(IND) as max_id FROM Doctor");
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ? $row['max_id'] + 1 : 1);
        
        if ($category !== null) {
            $sql = "INSERT INTO Doctor (IND, Surname, Name, Patronymic, Category) 
                    VALUES ($next_id, '$surname', '$name', '$patronymic', $category)";
        } else {
            $sql = "INSERT INTO Doctor (IND, Surname, Name, Patronymic) 
                    VALUES ($next_id, '$surname', '$name', '$patronymic')";
        }
        
        if ($conn->query($sql) === TRUE) {
            $conn->close();
            header("Location: doctor_manage.php?success=1");
            exit();
        } else {
            $message = "Ошибка: " . $conn->error . "<br>";
            
            $result = $conn->query("SELECT IND FROM Doctor ORDER BY IND");
            $used_ids = [];
            while($row = $result->fetch_assoc()) {
                $used_ids[] = $row['IND'];
            }
            
            $new_id = 1;
            while (in_array($new_id, $used_ids)) {
                $new_id++;
            }
            
            if ($category !== null) {
                $sql2 = "INSERT INTO Doctor (IND, Surname, Name, Patronymic, Category) 
                        VALUES ($new_id, '$surname', '$name', '$patronymic', $category)";
            } else {
                $sql2 = "INSERT INTO Doctor (IND, Surname, Name, Patronymic) 
                        VALUES ($new_id, '$surname', '$name', '$patronymic')";
            }
            
            if ($conn->query($sql2) === TRUE) {
                $conn->close();
                header("Location: doctor_manage.php?success=1");
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
