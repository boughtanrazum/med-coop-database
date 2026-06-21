<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "med_coop");
$message = "";
$prescription = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
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
        $sql = "UPDATE Prescription SET 
                Checkup = $checkup, 
                Medicine = $medicine, 
                Method = '$method' 
                WHERE IND = $id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Данные рецепта успешно обновлены!";
        } else {
            $message = "Ошибка: " . $conn->error;
        }
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id > 0) {
    $sql = "SELECT * FROM Prescription WHERE IND = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $prescription = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование рецепта - Медицинский Кооператив</title>
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
            <h1>Редактирование рецепта</h1>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="prescription_manage.php">Управление рецептами</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'Ошибка') !== false ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($prescription): ?>
                <h2>Редактирование данных рецепта</h2>
                <form method="POST" action="prescription_edit.php">
                    <input type="hidden" name="id" value="<?php echo $prescription['IND']; ?>">
                    
                    <div class="form-group">
                        <label>Осмотр:</label>
                        <select name="checkup" required>
                            <option value="">Выберите осмотр</option>
                            <?php
                            $sql = "SELECT c.IND, c.Date, 
                                    CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                                    CONCAT(d.Surname, ' ', d.Name, ' ', d.Patronymic) as Doctor
                                    FROM Checkup c
                                    JOIN Patient p ON c.Patient = p.IND
                                    JOIN Doctor d ON c.Doctor = d.IND
                                    ORDER BY c.Date DESC";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()) {
                                $selected = ($row['IND'] == $prescription['Checkup']) ? 'selected' : '';
                                echo "<option value='{$row['IND']}' $selected>{$row['Date']} - {$row['Patient']} / {$row['Doctor']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Лекарство:</label>
                        <select name="medicine" required>
                            <option value="">Выберите лекарство</option>
                            <?php
                            $result = $conn->query("SELECT * FROM Medicine ORDER BY Naming");
                            while($row = $result->fetch_assoc()) {
                                $selected = ($row['IND'] == $prescription['Medicine']) ? 'selected' : '';
                                echo "<option value='{$row['IND']}' $selected>{$row['Naming']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Метод применения:</label>
                        <textarea name="method" rows="3" required><?php echo $prescription['Method']; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Сохранить изменения</button>
                        <a href="prescription_manage.php" class="btn-cancel-form">Отмена</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-message">Рецепт не найден!</div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="prescription_manage.php" class="btn-add">Вернуться к управлению рецептами</a>
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