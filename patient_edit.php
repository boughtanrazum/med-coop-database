<?php
$conn = new mysqli("localhost", "root", "", "med_coop");
$message = "";
$patient = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $surname = $conn->real_escape_string($_POST['surname']);
    $name = $conn->real_escape_string($_POST['name']);
    $patronymic = $conn->real_escape_string($_POST['patronymic']);
    $gender = intval($_POST['gender']);
    $birth = $_POST['birth'];
    $address = $conn->real_escape_string($_POST['address']);
    
    $sql = "UPDATE Patient SET 
            Surname = '$surname', 
            Name = '$name', 
            Patronymic = '$patronymic', 
            Gender = $gender,
            Birth = '$birth', 
            Address = '$address' 
            WHERE IND = $id";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Данные пациента успешно обновлены!";
    } else {
        $message = "Ошибка: " . $conn->error;
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id > 0) {
    $sql = "SELECT * FROM Patient WHERE IND = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пациента - Медицинский Кооператив</title>
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
            <h1>Редактирование пациента</h1>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patient_manage.php">Управление пациентами</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'Ошибка') !== false ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($patient): ?>
                <h2>Редактирование данных пациента</h2>
                <form method="POST" action="patient_edit.php">
                    <input type="hidden" name="id" value="<?php echo $patient['IND']; ?>">
                    
                    <div class="form-group">
                        <label>Фамилия:</label>
                        <input type="text" name="surname" value="<?php echo $patient['Surname']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Имя:</label>
                        <input type="text" name="name" value="<?php echo $patient['Name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Отчество:</label>
                        <input type="text" name="patronymic" value="<?php echo $patient['Patronymic']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Пол:</label>
                        <select name="gender" required>
                            <option value="">Выберите пол</option>
                            <?php
                            $result = $conn->query("SELECT * FROM Gender");
                            while($row = $result->fetch_assoc()) {
                                $selected = ($row['IND'] == $patient['Gender']) ? 'selected' : '';
                                echo "<option value='{$row['IND']}' $selected>{$row['Naming']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата рождения:</label>
                        <input type="date" name="birth" value="<?php echo $patient['Birth']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Адрес:</label>
                        <input type="text" name="address" value="<?php echo $patient['Address']; ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Сохранить изменения</button>
                        <a href="patient_manage.php" class="btn-cancel-form">Отмена</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-message">Пациент не найден!</div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="patient_manage.php" class="btn-add">Вернуться к управлению пациентами</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>© boughtanrazum, 2025-2026</p>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>