<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new mysqli("localhost", "root", "", "med_coop");
    $db_error = $conn->connect_error;
} catch (mysqli_sql_exception $e) {
    $db_error = true;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пациенты - Медицинский Кооператив</title>
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
            <h1>Медицинский Кооператив</h1>
            <p>Список всех пациентов медицинского кооператива</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patients.php" class="nav-active">Пациенты</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="checkups.php">Осмотры</a></li>
                <li><a href="prescriptions.php">Предписания</a></li>
            </ul>
        </nav>

        <?php if ($db_error): ?>
        <section class="content-section">
            <div class="alert alert-danger">
                <h3>Ошибка подключения к базе данных!</h3>
                <p>База данных не найдена или недоступна. Пожалуйста, установите базу данных.</p>
                <div style="margin-top: 15px;">
                    <a href="install.php" class="btn" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                        Установить базу данных
                    </a>
                </div>
            </div>
        </section>
        <?php else: ?>
        <section class="content-section">
            <h2>Список пациентов</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th>
                            <th>Пол</th>
                            <th>Дата рождения</th>
                            <th>Адрес</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT p.*, g.Naming as GenderName 
                                FROM Patient p 
                                LEFT JOIN Gender g ON p.Gender = g.IND 
                                ORDER BY p.Surname, p.Name";
                        $result = $conn->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['Surname']}</td>
                                    <td>{$row['Name']}</td>
                                    <td>{$row['Patronymic']}</td>
                                    <td>{$row['GenderName']}</td>
                                    <td>{$row['Birth']}</td>
                                    <td>{$row['Address']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-data'>Нет данных о пациентах</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <footer class="footer">
            <p>© boughtanrazum, 2025-2026</p>
        </footer>
    </div>
</body>
</html>