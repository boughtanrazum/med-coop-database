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
    <title>Осмотры - Медицинский Кооператив</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Медицинский Кооператив</h1>
            <p>Медицинские осмотры</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patients.php">Пациенты</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="checkups.php" class="nav-active">Осмотры</a></li>
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
            <h2>История осмотров</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Пациент</th>
                            <th>Врач</th>
                            <th>Место осмотра</th>
                            <th>Диагноз</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                                c.Date,
                                CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                                CONCAT(d.Surname, ' ', d.Name, ' ', d.Patronymic) as Doctor,
                                CASE 
                                    WHEN cab.CabinetCheckup = 'True' THEN 'Кабинет'
                                    ELSE 'На дому'
                                END as Location,
                                diag.Naming as Diagnosis
                                FROM Checkup c
                                JOIN Patient p ON c.Patient = p.IND
                                JOIN Doctor d ON c.Doctor = d.IND
                                JOIN Cabinet cab ON c.Cabinet = cab.IND
                                JOIN Diagnosis diag ON c.Diagnosis = diag.IND
                                ORDER BY c.Date DESC";
                        
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['Patient']}</td>
                                    <td>{$row['Doctor']}</td>
                                    <td>{$row['Location']}</td>
                                    <td>{$row['Diagnosis']}</td>
                                    <td>{$row['Date']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='no-data'>Нет данных об осмотрах</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <footer class="footer">
            <p>© 2025-2026, Разуменко Б.В.</p>
        </footer>
    </div>
</body>
</html>