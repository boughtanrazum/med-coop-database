<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "med_coop");
$message = "";
$doctor = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
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
    

    if ($category !== null) {
        $sql = "UPDATE Doctor SET 
                Surname = '$surname', 
                Name = '$name', 
                Patronymic = '$patronymic', 
                Category = $category 
                WHERE IND = $id";
    } else {
        $sql = "UPDATE Doctor SET 
                Surname = '$surname', 
                Name = '$name', 
                Patronymic = '$patronymic', 
                Category = NULL 
                WHERE IND = $id";
    }
    
    if ($conn->query($sql) === TRUE) {
        $message = "Данные врача успешно обновлены!";
    } else {
        $message = "Ошибка: " . $conn->error;
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id > 0) {
    $sql = "SELECT * FROM Doctor WHERE IND = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование врача - Медицинский Кооператив</title>
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
            <h1>Редактирование врача</h1>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="doctor_manage.php">Управление врачами</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'Ошибка') !== false ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($doctor): ?>
                <h2>Редактирование данных врача</h2>
                <form method="POST" action="doctor_edit.php">
                    <input type="hidden" name="id" value="<?php echo $doctor['IND']; ?>">
                    
                    <div class="form-group">
                        <label>Фамилия:</label>
                        <input type="text" name="surname" value="<?php echo $doctor['Surname']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Имя:</label>
                        <input type="text" name="name" value="<?php echo $doctor['Name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Отчество:</label>
                        <input type="text" name="patronymic" value="<?php echo $doctor['Patronymic']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Категория:</label>
                        <select name="category">
                            <option value="">Без категории</option>
                            <?php
                            $result = $conn->query("SELECT * FROM Category");
                            while($row = $result->fetch_assoc()) {
                                $selected = ($row['IND'] == $doctor['Category']) ? 'selected' : '';
                                echo "<option value='{$row['IND']}' $selected>{$row['Naiming']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Сохранить изменения</button>
                        <a href="doctor_manage.php" class="btn-cancel-form">Отмена</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-message">Врач не найден!</div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="doctor_manage.php" class="btn-add">Вернуться к управлению врачами</a>
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