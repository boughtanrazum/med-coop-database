<?php
$conn = new mysqli("localhost", "root", "", "med_coop");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление осмотрами - Медицинский Кооператив</title>
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
            <h1>Управление осмотрами</h1>
            <p>Добавление, изменение и удаление записей осмотров</p>
        </header>

        <nav class="nav-menu">
            <ul class="main-nav">
                <li><a href="index.php">Главная</a></li>
                <li><a href="patient_manage.php">Управление пациентами</a></li>
                <li><a href="doctor_manage.php">Управление врачами</a></li>
                <li><a href="checkup_manage.php" class="nav-active">Управление осмотрами</a></li>
                <li><a href="prescription_manage.php">Управление предписаниями</a></li>
            </ul>
        </nav>

        <section class="content-section">
            <h2>Список осмотров</h2>
            
            <div class="button-container">
                <button class="btn-add" id="showFormBtn">Добавить осмотр</button>
            </div>
            
            <?php
            if (isset($_GET['success']) && $_GET['success'] == '1') {
                echo '<div class="success-message">Осмотр успешно добавлен!</div>';
            }
            
            if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
                echo '<div class="success-message">Осмотр успешно удален!</div>';
            }
            
            if (isset($_GET['error'])) {
                echo '<div class="error-message">Ошибка: ' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            
            <div class="form-container" id="addForm">
                <form method="POST" action="checkup_insert.php" id="checkupForm">
                    <div class="form-group">
                        <label>Пациент:</label>
                        <select name="patient" required>
                            <option value="">Выберите пациента</option>
                            <?php
                            if (!$conn->connect_error) {
                                $result = $conn->query("SELECT * FROM Patient ORDER BY Surname, Name");
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Surname']} {$row['Name']} {$row['Patronymic']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Врач:</label>
                        <select name="doctor" required>
                            <option value="">Выберите врача</option>
                            <?php
                            if (!$conn->connect_error) {
                                $result = $conn->query("SELECT * FROM Doctor ORDER BY Surname, Name");
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Surname']} {$row['Name']} {$row['Patronymic']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Место осмотра:</label>
                        <select name="cabinet" required>
                            <option value="">Выберите место осмотра</option>
                            <option value="1">На дому</option>
                            <option value="2">Кабинет</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Диагноз:</label>
                        <select name="diagnosis" required>
                            <option value="">Выберите диагноз</option>
                            <?php
                            if (!$conn->connect_error) {
                                $result = $conn->query("SELECT * FROM Diagnosis ORDER BY Naming");
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['IND']}'>{$row['Naming']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата осмотра:</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div>
                        <button type="submit" class="btn-add">Добавить осмотр</button>
                        <button type="button" class="btn-cancel" id="cancelBtn">Отмена</button>
                    </div>
                </form>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Пациент</th>
                            <th>Врач</th>
                            <th>Место осмотра</th>
                            <th>Диагноз</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($conn->connect_error) {
                            echo "<tr><td colspan='6' class='error-msg'>Ошибка подключения к базе данных</td></tr>";
                        } else {
                            $sql  = "SELECT c.IND, c.Date, 
                            CONCAT(p.Surname, ' ', p.Name, ' ', p.Patronymic) as Patient,
                            CONCAT(d.Surname, ' ', d.Name, ' ', d.Patronymic) as Doctor,
                            c.Cabinet,
                            diag.Naming as Diagnosis
                            FROM Checkup c
                            JOIN Patient p ON c.Patient = p.IND
                            JOIN Doctor d ON c.Doctor = d.IND
                            JOIN Diagnosis diag ON c.Diagnosis = diag.IND
                            ORDER BY c.Date DESC";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $location = ($row['Cabinet'] == 1) ? 'На дому' : 'Кабинет';
                                    echo "<tr>
                                        <td>{$row['Date']}</td>
                                        <td>{$row['Patient']}</td>
                                        <td>{$row['Doctor']}</td>
                                        <td>{$location}</td>
                                        <td>{$row['Diagnosis']}</td>
                                        <td>
                                            <div class='action-buttons'>
                                                <a href='checkup_edit.php?id={$row['IND']}' class='btn btn-edit'>Изменить</a>
                                                <a href='checkup_delete.php?id={$row['IND']}' class='btn btn-delete' onclick='return confirm(\"Удалить осмотр от {$row['Date']}?\")'>Удалить</a>
                                            </div>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='no-data'>Нет данных об осмотрах</td></tr>";
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
            document.getElementById('checkupForm').reset();
        });
    </script>
</body>
</html>