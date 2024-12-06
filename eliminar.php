<?php
session_start();
require_once 'config/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Convertir a entero para mayor seguridad

    $db = new Database();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("DELETE FROM mypage.estudiantes WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['message'] = 'Registro eliminado exitosamente.';
        } else {
            $_SESSION['message'] = 'Error al eliminar el registro.';
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error en la base de datos: ' . $e->getMessage();
    }
} else {
    $_SESSION['message'] = 'ID no proporcionado o no válido.';
}

header('Location: ver_datos.php');
exit();
?>