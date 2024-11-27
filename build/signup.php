<?php
require_once "../assets/classes/User.php"; // Verifica que la ruta y el nombre de la clase sean correctos
require_once "../controllers/connection.php"; // Verifica que la ruta de conexión sea correcta

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Recoge el valor del campo 'username'
    $password = trim($_POST['password']); // Recoge el valor del campo 'password'

    // Validación básica
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Crear un objeto de la clase User
    $user = new User($username, $password);

    // Obtener los valores del objeto User
    $usernameValue = $user->getUsername(); // Asegúrate de que tu método en la clase User se llama getName()
    $passwordValue = $user->getPassword(); // El hash de la contraseña ya está en $passwordValue

    // Crear la consulta SQL
    $sql = "INSERT INTO users (username, password) VALUES ('$usernameValue', '$passwordValue')";

    // Ejecutar la consulta y verificar si se ejecutó correctamente
    if ($conn->query($sql) === TRUE) {
        echo "User registered successfully!";
    } else {
        echo "Error registering user: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
