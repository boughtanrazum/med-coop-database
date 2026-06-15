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
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <div style="background: <?php echo strpos($message, 'Ошибка') !== false ? '#f8d7da' : '#d4edda'; ?>; 
                            color: <?php echo strpos($message, 'Ошибка') !== false ? '#721c24' : '#155724'; ?>; 
                            padding: 15px; border-radius: 5px; margin-bottom: 20px; 
                            border-left: 4px solid <?php echo strpos($message, 'Ошибка') !== false ? '#dc3545' : '#28a745'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($prescription): ?>
                <h2>Редактирование данных рецепта</h2>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <form method="POST" action="prescription_edit.php">
                        <input type="hidden" name="id" value="<?php echo $prescription['IND']; ?>">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Осмотр:</label>
                            <select name="checkup" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
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
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Лекарство:</label>
                            <select name="medicine" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
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
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Метод применения:</label>
                            <textarea name="method" rows="3" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;"><?php echo $prescription['Method']; ?></textarea>
                        </div>
                        
                        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Сохранить изменения</button>
                        <a href="prescription_manage.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; margin-left: 10px;">Отмена</a>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;">
                    Рецепт не найден!
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="prescription_manage.php" style="background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Вернуться к управлению рецептами</a>
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