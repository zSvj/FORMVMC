<?php
session_start();
require_once 'config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener datos registrados
$sql = "SELECT * FROM mypage.estudiantes"; 
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar el registro si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $_SESSION['alert_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error al registrar.';
        $_SESSION['alert_type'] = 'error';
    }
    header('Location: index.php'); // Redirigir a la misma página
    exit();
}

// Procesar la eliminación si se pasa un ID
if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM mypage.estudiantes WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        $_SESSION['message'] = 'Registro eliminado exitosamente.';
        $_SESSION['alert_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error al eliminar el registro.';
        $_SESSION['alert_type'] = 'error';
    }

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro y Datos de Estudiantes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Registro de Estudiantes</h2>
        
        <form action="index.php" method="POST" id="registrationForm" class="mb-5">
            <div class="form-group">
                <label for="fullname">Nombre completo:</label>
                <input type="text" class="form-control" id="fullname" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="age">Edad:</label>
                <input type="number" class="form-control" id="age" name="edad" required min="10" max="100">
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="course">Curso de interés:</label>
                <select class="form-control" id="course" name="curso" required>
                    <option value="">Seleccione un curso</option>
                    <option value="Matemáticas">Matemáticas</option>
                    <option value="Ciencias">Ciencias</option>
                    <option value="Literatura">Literatura</option>
                </select>
            </div>

            <div class="form-group">
                <label>Género:</label><br>
                <div class="form -check form-check-inline">
                    <input class="form-check-input" type="radio" id="male" name="genero" value="Masculino" required>
                    <label class="form-check-label" for="male">Masculino</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="female" name="genero" value="Femenino" required>
                    <label class="form-check-label" for="female">Femenino</label>
                </div>
            </div>

            <div class="form-group">
                <label>Áreas de interés:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Deportes" id="interest1">
                    <label class="form-check-label" for="interest1">Deportes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Arte" id="interest2">
                    <label class="form-check-label" for="interest2">Arte</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="intereses[]" value="Tecnología" id="interest3">
                    <label class="form-check-label" for="interest3">Tecnología</label>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-block">Registrar</button>
        </form>

        <h2 class="text-center mb-4">Datos Registrados</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Edad</th>
                    <th>Correo Electrónico</th>
                    <th>Curso</th>
                    <th>Género</th>
                    <th>Áreas de Interés</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($results) > 0): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['edad']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['curso']); ?></td>
                            <td><?php echo htmlspecialchars($row['genero']); ?></td>
                            <td><?php echo htmlspecialchars($row['intereses']); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay datos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás recuperar este registro después de eliminarlo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?action=eliminar&id=' + id;
                }
            });
        }

        // Mostrar alertas de éxito o error
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['alert_type']; ?>',
                title: '<?php echo $_SESSION['message']; ?> confirmButtonText: 'Aceptar'
            });
            <?php unset($_SESSION['message']); unset($_SESSION['alert_type']); ?>
        <?php endif; ?>
    </script>
</body>
</html>