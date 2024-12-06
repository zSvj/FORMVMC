<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $genero = $_POST['genero'];
    $intereses = isset($_POST['intereses']) ? implode(", ", $_POST['intereses']) : '';

    $sql = "INSERT INTO mypage.estudiantes (nombre, edad, email, curso, genero, intereses) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$nombre, $edad, $email, $curso, $genero, $intereses])) {
        $_SESSION['message'] = 'Registro exitoso.';
    } else {
        $_SESSION['message'] = 'Error al registrar.';
    }

    header('Location: ver_datos.php');
    exit();
}