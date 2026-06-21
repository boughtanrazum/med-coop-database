<?php
$conn = new mysqli("localhost", "root", "", "med_coop");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление предписаниями - Медицинский Кооператив</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <style>
        .form-container {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            border: 1px solid #e9ecef;
            display: none;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            border: 2px solid #28a745;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 1.5rem;
        }
        
        .btn-add:hover {
            background: #218838;
            border-color: #1e7e34;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: 2px solid #6c757d;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-left: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            border-color: #545b62;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
            border: 2px solid #ffc107;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-edit:hover {
            background: #e0a800;
            border-color: #d39e00;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: 2px solid #dc3545;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-delete:hover {
            background: #c82333;
            border-color: #c82333;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .error-msg {
            color: #dc3545;
            text-align: center;
            padding: 2rem;
            font-weight: 600;
        }
        
        .no-data {
            color: #6c757d;
            text-align: center;
            padding: 2rem;
            font-style: italic;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
            font-weight: 600;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
            font-weight: 600;
        }
        
        .button-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="floating-bg">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <div class="container">
        <header class="header">
            <h1>Управление предписаниями</h1>
            <p>Добавление, изменение и удаление записей предписаний</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patient_manage.php">Управление пациентами</a></li>
                <li><a href="doctor_manage.php">Управление врачами</a></li>
                <li><a href="checkup_manage.php">Управление осмотрами</a></li>
                <li><a href="prescription_manage.php" class="nav-active">Управление предписаниями</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <h2>Список предписаний</h2>
            
            <div class="button-container">
                <button class="btn-add" id="showFormBtn">Добавить предписание</button>
            </div>
            
            <?php
            if (isset($_GET['success']) && $_GET['success'] == '1') {
                echo '<div class="success-message">Предписание успешно добавлено!</div>';
            }
            
            if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
                echo '<div class="success-message">Предписание успешно удалено!</div>';
            }
            
            if (isset($_GET['error'])) {
                echo '<div class="error-message">Ошибка: ' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            
            <div class="form-container" id="addForm">
                <form method="POST" action="prescription_insert.php" id="prescriptionForm">
                    <div class="form-group">
                        <label>Осмотр:</label>
                        <select name="checkup" required>
                            <option value="">Выберите осмотр</option>
                            <?php
                            if (!$conn->connect_error) {
                                $sql = "SELECT c.IND, c.Date, 
                                        CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                                        CONCAT(d.Surname, ' ', d.Name, ' ', d.Patronymic) as Doctor
                                        FROM Checkup c
                                        JOIN Patient p ON c.Patient = p.IND
                                        JOIN Doctor d ON c.Doctor = d.IND
                                        ORDER BY c.Date DESC";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Date']} - {$row['Patient']} / {$row['Doctor']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Лекарство:</label>
                        <select name="medicine" required>
                            <option value="">Выберите лекарство</option>
                            <?php
                            if (!$conn->connect_error) {
                                $result = $conn->query("SELECT * FROM Medicine ORDER BY Naming");
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Naming']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Метод применения:</label>
                        <textarea name="method" rows="3" required placeholder="Введите метод применения лекарства..."></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn-add">Добавить предписание</button>
                        <button type="button" class="btn-cancel" id="cancelBtn">Отмена</button>
                    </div>
                </form>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Дата осмотра</th>
                            <th>Пациент</th>
                            <th>Врач</th>
                            <th>Лекарство</th>
                            <th>Метод применения</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($conn->connect_error) {
                            echo "<tr><td colspan='6' class='error-msg'>Ошибка подключения к базе данных</td></tr>";
                        } else {
                            $sql = "SELECT pr.IND, c.Date,
                                    CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                                    CONCAT(d.Surname, ' ', d.Name, ' ', d.Patronymic) as Doctor,
                                    m.Naming as Medicine,
                                    pr.Method
                                    FROM Prescription pr
                                    JOIN Checkup c ON pr.Checkup = c.IND
                                    JOIN Patient p ON c.Patient = p.IND
                                    JOIN Doctor d ON c.Doctor = d.IND
                                    JOIN Medicine m ON pr.Medicine = m.IND
                                    ORDER BY c.Date DESC";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['Date']}</td>
                                        <td>{$row['Patient']}</td>
                                        <td>{$row['Doctor']}</td>
                                        <td>{$row['Medicine']}</td>
                                        <td>{$row['Method']}</td>
                                        <td>
                                            <div class='action-buttons'>
                                                <a href='prescription_edit.php?id={$row['IND']}' class='btn btn-edit'>Изменить</a>
                                                <a href='prescription_delete.php?id={$row['IND']}' class='btn btn-delete' onclick='return confirm(\"Удалить предписание для {$row['Patient']}?\")'>Удалить</a>
                                            </div>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='no-data'>Нет данных о предписаниях</td></tr>";
                            }
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            <p>© boughtanrazum, 2025-2026</p>
        </footer>
    </div>
    
    <script>
        document.getElementById('showFormBtn').addEventListener('click', function() {
            document.getElementById('addForm').style.display = 'block';
            this.style.display = 'none';
        });
        
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('addForm').style.display = 'none';
            document.getElementById('showFormBtn').style.display = 'block';
            document.getElementById('prescriptionForm').reset();
        });
    </script>
</body>
</html>