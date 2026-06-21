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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>
    <div class="floating-bg">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>
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
                <div class="<?php echo strpos($message, 'Ошибка') !== false ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($checkup): ?>
                <h2>Редактирование данных осмотра</h2>
                <form method="POST" action="checkup_edit.php">
                    <input type="hidden" name="id" value="<?php echo $checkup['IND']; ?>">
                    
                    <div class="form-group">
                        <label>Пациент:</label>
                        <select name="patient" required>
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
                    <div class="form-group">
                        <label>Врач:</label>
                        <select name="doctor" required>
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
                    <div class="form-group">
                        <label>Место осмотра:</label>
                        <select name="cabinet" required>
                            <option value="1" <?php echo $checkup['Cabinet'] == 1 ? 'selected' : ''; ?>>На дому</option>
                            <option value="2" <?php echo $checkup['Cabinet'] == 2 ? 'selected' : ''; ?>>Кабинет</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Диагноз:</label>
                        <select name="diagnosis" required>
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
                    <div class="form-group">
                        <label>Дата осмотра:</label>
                        <input type="date" name="date" value="<?php echo $checkup['Date']; ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Сохранить изменения</button>
                        <a href="checkup_manage.php" class="btn-cancel-form">Отмена</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-message">Осмотр не найден!</div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="checkup_manage.php" class="btn-add">Вернуться к управлению осмотрами</a>
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