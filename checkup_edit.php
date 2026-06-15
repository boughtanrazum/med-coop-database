<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "med_coop");
$message = "";
$checkup = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
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
        
        $sql = "UPDATE Checkup SET 
                Patient = $patient, 
                Doctor = $doctor, 
                Cabinet = $cabinet, 
                Diagnosis = $diagnosis, 
                Date = '$date' 
                WHERE IND = $id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Данные осмотра успешно обновлены!";
        } else {
            $message = "Ошибка: " . $conn->error;
        }
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id > 0) {
    $sql = "SELECT * FROM Checkup WHERE IND = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $checkup = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование осмотра - Медицинский Кооператив</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Редактирование осмотра</h1>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="checkup_manage.php">Управление осмотрами</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <?php if ($message): ?>
                <div style="background: <?php echo strpos($message, 'Ошибка') !== false ? '#f8d7da' : '#d4edda'; ?>; 
                            color: <?php echo strpos($message, 'Ошибка') !== false ? '#721c24' : '#155724'; ?>; 
                            padding: 15px; border-radius: 5px; margin-bottom: 20px; 
                            border-left: 4px solid <?php echo strpos($message, 'Ошибка') !== false ? '#dc3545' : '#28a745'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($checkup): ?>
                <h2>Редактирование данных осмотра</h2>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <form method="POST" action="checkup_edit.php">
                        <input type="hidden" name="id" value="<?php echo $checkup['IND']; ?>">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Пациент:</label>
                            <select name="patient" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Выберите пациента</option>
                                <?php
                                $result = $conn->query("SELECT * FROM Patient ORDER BY Surname, Name");
                                while($row = $result->fetch_assoc()) {
                                    $selected = ($row['IND'] == $checkup['Patient']) ? 'selected' : '';
                                    echo "<option value='{$row['IND']}' $selected>{$row['Surname']} {$row['Name']} {$row['Patronymic']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Врач:</label>
                            <select name="doctor" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Выберите врача</option>
                                <?php
                                $result = $conn->query("SELECT * FROM Doctor ORDER BY Surname, Name");
                                while($row = $result->fetch_assoc()) {
                                    $selected = ($row['IND'] == $checkup['Doctor']) ? 'selected' : '';
                                    echo "<option value='{$row['IND']}' $selected>{$row['Surname']} {$row['Name']} {$row['Patronymic']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Место осмотра:</label>
                            <select name="cabinet" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="1" <?php echo $checkup['Cabinet'] == 1 ? 'selected' : ''; ?>>На дому</option>
                                <option value="2" <?php echo $checkup['Cabinet'] == 2 ? 'selected' : ''; ?>>Кабинет</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Диагноз:</label>
                            <select name="diagnosis" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Выберите диагноз</option>
                                <?php
                                $result = $conn->query("SELECT * FROM Diagnosis ORDER BY Naming");
                                while($row = $result->fetch_assoc()) {
                                    $selected = ($row['IND'] == $checkup['Diagnosis']) ? 'selected' : '';
                                    echo "<option value='{$row['IND']}' $selected>{$row['Naming']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Дата осмотра:</label>
                            <input type="date" name="date" value="<?php echo $checkup['Date']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Сохранить изменения</button>
                        <a href="checkup_manage.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; margin-left: 10px;">Отмена</a>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;">
                    Осмотр не найден!
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="checkup_manage.php" style="background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Вернуться к управлению осмотрами</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>Лабораторные работы №3,4</p>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>