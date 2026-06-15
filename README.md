Medical Cooperative Management System
-------------------------------------

This is a lightweight administrative dashboard for managing a medical cooperative database. Built with PHP and MySQl.

Installation

Start local server (XAMPP/WAMP/MAMP)

Import database:
  Open phpMyAdmin
  Create database: med_coop
  Import med_coop.sql

Configure connection:
  Edit connection parameters in PHP files if needed:
  $conn = new mysqli("localhost", "root", "", "med_coop");

Place files in your web server directory (htdocs for XAMPP).

Open http://localhost/med_coop/ in browser.

-------------------------------------

Простая админская панель для управления данными медицинского кооператива. Сделанная с помощью PHP и MySQL.

Установка

Запустите локальный сервер (XAMPP/WAMP/MAMP)

Импортируйте базу данных:
  Откройте phpMyAdmin
  Создайте базу: med_coop
  
Импортируйте файл med_coop.sql
  Настройте подключение (при необходимости):
  $conn = new mysqli("localhost", "root", "", "med_coop");

Поместите файлы в директорию веб-сервера (для XAMPP — htdocs).
Откройте http://localhost/med_coop/ в браузере.

