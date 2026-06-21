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
    <title>Предписания - Медицинский Кооператив</title>
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
            <p>Просмотр выписанных предписаний</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patients.php">Пациенты</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="checkups.php">Осмотры</a></li>
                <li><a href="prescriptions.php" class="nav-active">Предписания</a></li>
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
            <h2>Выписанные предписания</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Пациент</th>
                            <th>Лекарство</th>
                            <th>Метод применения</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                                CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                                m.Naming as Medicine,
                                pr.Method
                                FROM Prescription pr
                                JOIN Checkup c ON pr.Checkup = c.IND
                                JOIN Patient p ON c.Patient = p.IND
                                JOIN Medicine m ON pr.Medicine = m.IND
                                ORDER BY p.Surname, p.Name";
                        
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['Patient']}</td>
                                    <td>{$row['Medicine']}</td>
                                    <td>{$row['Method']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='no-data'>Нет данных о предписаниях</td></tr>";
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