<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['name'];
    $email = $_POST['email'];
    $fecha_inicio = $_POST['reservation-date'];
    $fecha_fin = $_POST['reservation-time'];
    
    // Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    // Insertar la reserva en la base de datos
    $sql = "INSERT INTO Reservas (usuario_id, apartamento_id, fecha_inicio, fecha_fin, estado, fecha_reserva) VALUES (1, 1, '$fecha_inicio', '$fecha_fin', 'Confirmada', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo "Reserva realizada con éxito.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>
