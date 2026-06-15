<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "med_coop");
    
    if ($conn->connect_error) {
        $message = "Ошибка подключения: " . $conn->connect_error;
    } else {
        $patient = intval($_POST['patient']);
        $doctor = intval($_POST['doctor']);
        $cabinet = intval($_POST['cabinet']);
        $diagnosis = intval($_POST['diagnosis']);
        $date = $conn->real_escape_string($_POST['date']);
        
        $check_patient = $conn->query("SELECT IND FROM Patient WHERE IND = $patient");
        if ($check_patient->num_rows == 0) {
            $message = "Ошибка: указанный пациент не существует!";
        } 
        
        elseif ($conn->query("SELECT IND FROM Doctor WHERE IND = $doctor")->num_rows == 0) {
            $message = "Ошибка: указанный врач не существует!";
        }
        
        elseif ($conn->query("SELECT IND FROM Diagnosis WHERE IND = $diagnosis")->num_rows == 0) {
            $message = "Ошибка: указанный диагноз не существует!";
        }
        else {
            $result = $conn->query("SELECT MAX(IND) as max_id FROM Checkup");
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ? $row['max_id'] + 1 : 1);
            
            $sql = "INSERT INTO Checkup (IND, Patient, Doctor, Cabinet, Diagnosis, Date) 
                    VALUES ($next_id, $patient, $doctor, $cabinet, $diagnosis, '$date')";
            
            if ($conn->query($sql) === TRUE) {
                $conn->close();
                header("Location: checkup_manage.php?success=1");
                exit();
            } else {
                $message = "Ошибка: " . $conn->error . "<br>";
                
                $result = $conn->query("SELECT IND FROM Checkup ORDER BY IND");
                $used_ids = [];
                while($row = $result->fetch_assoc()) {
                    $used_ids[] = $row['IND'];
                }
                
                $new_id = 1;
                while (in_array($new_id, $used_ids)) {
                    $new_id++;
                }
                
                $sql2 = "INSERT INTO Checkup (IND, Patient, Doctor, Cabinet, Diagnosis, Date) 
                        VALUES ($new_id, $patient, $doctor, $cabinet, $diagnosis, '$date')";
                
                if ($conn->query($sql2) === TRUE) {
                    $conn->close();
                    header("Location: checkup_manage.php?success=1");
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
