<?php
$conn = new mysqli("localhost", "root", "", "med_coop");
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пациентами - Медицинский Кооператив</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
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
    <div class="container">
        <header class="header">
            <h1>Управление пациентами</h1>
            <p>Добавление, изменение и удаление записей пациентов</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patient_manage.php" class="nav-active">Управление пациентами</a></li>
                <li><a href="doctor_manage.php">Управление врачами</a></li>
                <li><a href="checkup_manage.php">Управление осмотрами</a></li>
                <li><a href="prescription_manage.php">Управление предписаниями</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <h2>Список пациентов</h2>
            
            <div class="button-container">
                <button class="btn-add" id="showFormBtn">Добавить пациента</button>
            </div>
            
            <?php
            if (isset($_GET['success']) && $_GET['success'] == '1') {
                echo '<div class="success-message">Пациент успешно добавлен!</div>';
            }
            
            if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
                echo '<div class="success-message">Пациент успешно удален!</div>';
            }
            
            if (isset($_GET['error'])) {
                echo '<div class="error-message">Ошибка: ' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            
            <div class="form-container" id="addForm">
                <form method="POST" action="patient_insert.php" id="patientForm">
                    <div class="form-group">
                        <label>Фамилия:</label>
                        <input type="text" name="surname" required>
                    </div>
                    <div class="form-group">
                        <label>Имя:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Отчество:</label>
                        <input type="text" name="patronymic" required>
                    </div>
                    <div class="form-group">
                        <label>Пол:</label>
                        <select name="gender" required>
                            <option value="">Выберите пол</option>
                            <?php
                            if (!$conn->connect_error) {
                                $result = $conn->query("SELECT * FROM Gender");
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Naming']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата рождения:</label>
                        <input type="date" name="birth" required>
                    </div>
                    <div class="form-group">
                        <label>Адрес:</label>
                        <input type="text" name="address" required>
                    </div>
                    <div>
                        <button type="submit" class="btn-add">Добавить пациента</button>
                        <button type="button" class="btn-cancel" id="cancelBtn">Отмена</button>
                    </div>
                </form>
            </div>
            
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
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($conn->connect_error) {
                            echo "<tr><td colspan='7' class='error-msg'>Ошибка подключения к базе данных</td></tr>";
                        } else {
                            $sql = "SELECT p.*, g.Naming as GenderName 
                                    FROM Patient p 
                                    LEFT JOIN Gender g ON p.Gender = g.IND 
                                    ORDER BY p.Surname, p.Name";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['Surname']}</td>
                                        <td>{$row['Name']}</td>
                                        <td>{$row['Patronymic']}</td>
                                        <td>{$row['GenderName']}</td>
                                        <td>{$row['Birth']}</td>
                                        <td>{$row['Address']}</td>
                                        <td>
                                            <div class='action-buttons'>
                                                <a href='patient_edit.php?id={$row['IND']}' class='btn btn-edit'>Изменить</a>
                                                <a href='patient_delete.php?id={$row['IND']}' class='btn btn-delete' onclick='return confirm(\"Удалить пациента {$row['Surname']} {$row['Name']}?\")'>Удалить</a>
                                            </div>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='no-data'>Нет данных о пациентах</td></tr>";
                            }
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            <p>© 2025-2026, Разуменко Б.В.</p>
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
            document.getElementById('patientForm').reset();
        });
    </script>
</body>
</html>