<?php
session_start();
require_once 'config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener el ID del estudiante a editar
$id = $_GET['id'];
$sql = "SELECT * FROM mypage.estudiantes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar la actualización si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $genero = $_POST['genero'];
    $intereses = isset($_POST['intereses']) ? implode(", ", $_POST['intereses']) : '';

    $sql = "UPDATE mypage.estudiantes SET nombre = ?, edad = ?, email = ?, curso = ?, genero = ?, intereses = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$nombre, $edad, $email, $curso, $genero, $intereses, $id])) {
        $_SESSION['message'] = 'Datos actualizados correctamente.';
        $_SESSION['alert_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error al actualizar los datos.';
        $_SESSION['alert_type'] = 'error';
    }
    header('Location: index.php'); // Redirigir a la página principal
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Editar Estudiante</h2>
        
        <form action="editar.php?id=<?php echo $id; ?>" method="POST" id="editForm">
            <div class="form-group">
                <label for="fullname">Nombre completo:</label>
                <input type="text" class="form-control" id="fullname" name="nombre" value="<?php echo htmlspecialchars($student['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Edad:</label>
                <input type="number" class="form-control" id="age" name="edad" value="<?php echo htmlspecialchars($student['edad']); ?>" required min="10" max="100">
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="course">Curso de interés:</label>
                <select class="form-control" id="course" name="curso" required>
                    <option value="">Seleccione un curso</option>
                    <option value="Matemáticas" <?php echo ($student['curso'] == 'Matemáticas') ? 'selected' : ''; ?>>Matemáticas</option>
                    <option value="Ciencias" <?php echo ($student['curso'] == 'Ciencias') ? 'selected' : ''; ?>>Ciencias</option>
                    <option value="Literatura" <?php echo ($student['curso'] == 'Literatura') ? 'selected' : ''; ?>>Literatura</option>
                </select>
            </div>

            <div class="form-group">
                <label>Género:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="male" name="genero" value="Masculino" <?php echo ($student['genero'] == 'Masculino') ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="male">Masculino </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="female" name="genero" value="Femenino" <?php echo ($student['genero'] == 'Femenino') ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="female">Femenino</label>
                </div>
            </div>

            <div class="form-group">
                <label>Áreas de interés:</label><br>
                <?php $intereses = explode(", ", $student['intereses']); ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Deportes" id="interest1" <?php echo in_array('Deportes', $intereses) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="interest1">Deportes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Arte" id="interest2" <?php echo in_array('Arte', $intereses) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="interest2">Arte</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Tecnología" id="interest3" <?php echo in_array('Tecnología', $intereses) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="interest3">Tecnología</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mostrar alertas de éxito o error
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['alert_type']; ?>',
                title: '<?php echo $_SESSION['message']; ?>',
                confirmButtonText: 'Aceptar'
            });
            <?php unset($_SESSION['message']); unset($_SESSION['alert_type']); ?>
        <?php endif; ?>
    </script>
</body>
</html>