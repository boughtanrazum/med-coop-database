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
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <div style="background: <?php echo strpos($message, 'Ошибка') !== false ? '#f8d7da' : '#d4edda'; ?>; 
                            color: <?php echo strpos($message, 'Ошибка') !== false ? '#721c24' : '#155724'; ?>; 
                            padding: 15px; border-radius: 5px; margin-bottom: 20px; 
                            border-left: 4px solid <?php echo strpos($message, 'Ошибка') !== false ? '#dc3545' : '#28a745'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($doctor): ?>
                <h2>Редактирование данных врача</h2>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <form method="POST" action="doctor_edit.php">
                        <input type="hidden" name="id" value="<?php echo $doctor['IND']; ?>">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Фамилия:</label>
                            <input type="text" name="surname" value="<?php echo $doctor['Surname']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Имя:</label>
                            <input type="text" name="name" value="<?php echo $doctor['Name']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Отчество:</label>
                            <input type="text" name="patronymic" value="<?php echo $doctor['Patronymic']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Категория:</label>
                            <select name="category" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
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
                        
                        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Сохранить изменения</button>
                        <a href="doctor_manage.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; margin-left: 10px;">Отмена</a>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;">
                    Врач не найден!
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="doctor_manage.php" style="background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Вернуться к управлению врачами</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>© 2025-2026, Разуменко Б.В.</p>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>