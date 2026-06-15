
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский Кооператив</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Медицинский Кооператив</h1>
            <p>Система управления медицинскими данными</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php" class="nav-active">Главная</a></li>
                <li><a href="patients.php">Пациенты</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="checkups.php">Осмотры</a></li>
                <li><a href="prescriptions.php">Предписания</a></li>
            </ul>
        </nav>


        <section class="content-section">
            <h2>Управление базой данных</h2>
            <div class="quick-actions">
                <div class="action-card" onclick="location.href='patient_manage.php'">
                    <div class="action-icon">👥</div>
                    <div class="action-text">Управление пациентами</div>
                </div>
                <div class="action-card" onclick="location.href='doctor_manage.php'">
                    <div class="action-icon">👨‍⚕️</div>
                    <div class="action-text">Управление врачами</div>
                </div>
                <div class="action-card" onclick="location.href='checkup_manage.php'">
                    <div class="action-icon">📋</div>
                    <div class="action-text">Управление осмотрами</div>
                </div>
                <div class="action-card" onclick="location.href='prescription_manage.php'">
                    <div class="action-icon">💊</div>
                    <div class="action-text">Управление предписаниями</div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <p>© 2025-2026, Разуменко Б.В.</p>
        </footer>
    </div>
</body>
</html>