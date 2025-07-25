<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

define('DB_HOST', 'localhost');
define('DB_NAME', 'voluntariado');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SMTP_USER', 'notificaciones@cruzrojapuebla.org');
define('SMTP_PASS', 'rnyxjnfgzrjkalgy');

// ACTIVAR LOGGING DE ERRORES
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

// Función para logging personalizado
function debug_log($mensaje) {
    error_log(date('Y-m-d H:i:s') . " - " . $mensaje . PHP_EOL, 3, 'debug.log');
}

function correoPareceValido($correo) {
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;
    $dominio = substr(strrchr($correo, "@"), 1);
    return checkdnsrr($dominio, "MX");
}

function enviarCorreo($correoDestino, $usuario) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom(SMTP_USER, 'Sistema Voluntariado');
        $mail->addAddress($correoDestino);

        $mail->isHTML(true);
        $mail->Subject = 'Credenciales de acceso al sistema';
        $mail->Body = "
            <p>Bienvenido al sistema de voluntariado.</p>
            <p><strong>Usuario:</strong> $usuario</p>
            <p><strong>Contraseña:</strong> $usuario</p>
            <p>Recuerda cambiar tu contraseña al iniciar sesión.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("=== INICIO DEL PROCESO ===");
    debug_log("Datos POST recibidos: " . json_encode($_POST));
    
    // Función para obtener valor o vacío
    function obtenerValor($array, $key, $default = '') {
        return isset($array[$key]) && $array[$key] !== '' ? trim($array[$key]) : $default;
    }
    
    // Sanitizar datos con valores por defecto
    $datos = [
        'nombre' => obtenerValor($_POST, 'nombre'),
        'apellido_paterno' => obtenerValor($_POST, 'apellido_paterno'),
        'apellido_materno' => obtenerValor($_POST, 'apellido_materno'),
        'correo' => obtenerValor($_POST, 'correo'),
        'telefono_fijo' => obtenerValor($_POST, 'telefono_fijo'),
        'celular' => obtenerValor($_POST, 'celular'),
        'curp' => obtenerValor($_POST, 'curp'),
        'tipo_sangre' => obtenerValor($_POST, 'tipo_sangre'),
        'fecha_registro' => obtenerValor($_POST, 'fecha_registro', date('Y-m-d')),
        'departamento' => obtenerValor($_POST, 'departamento'),
        
        // Dirección
        'calle' => obtenerValor($_POST, 'calle'),
        'numero_exterior' => obtenerValor($_POST, 'numero_exterior'),
        'codigo_postal' => obtenerValor($_POST, 'codigo_postal'),
        'colonia' => obtenerValor($_POST, 'colonia'),
        'ciudad' => obtenerValor($_POST, 'ciudad'),
        'municipio' => obtenerValor($_POST, 'municipio'),
        'estado' => obtenerValor($_POST, 'estado'),
        
        // Contacto emergencia
        'nombre_completo' => obtenerValor($_POST, 'nombre_completo'),
        'parentesco' => obtenerValor($_POST, 'parentesco'),
        'telefono' => obtenerValor($_POST, 'telefono'),
        
        // Información institucional
        'delegacion_local' => obtenerValor($_POST, 'delegacion_local'),
        'fecha_ingreso' => obtenerValor($_POST, 'fecha_ingreso', date('Y-m-d')),
        'coordinacion' => obtenerValor($_POST, 'coordinacion'),
        'correo_institucional' => obtenerValor($_POST, 'correo_institucional'),
        'grado_estudios' => obtenerValor($_POST, 'grado_estudios'),
        'carrera_estudiada' => obtenerValor($_POST, 'carrera_estudiada'),
        'idiomas' => obtenerValor($_POST, 'idiomas'),
        
        // Datos adicionales
        'tiene_pasaporte' => obtenerValor($_POST, 'tiene_pasaporte', 'No'),
        'numero_pasaporte' => obtenerValor($_POST, 'numero_pasaporte'),
        'tiene_licencia' => obtenerValor($_POST, 'tiene_licencia', 'No'),
        'numero_licencia' => obtenerValor($_POST, 'numero_licencia'),
        'tipo_asociado' => obtenerValor($_POST, 'tipo_asociado'),
        'numero_asociado' => obtenerValor($_POST, 'numero_asociado'),
        
        // Especialidades médicas
        'nombre_especialidad' => obtenerValor($_POST, 'nombre_especialidad'),
        'fecha_inicio' => obtenerValor($_POST, 'fecha_inicio'),
        'fecha_termino' => obtenerValor($_POST, 'fecha_termino')
    ];
    
    debug_log("Datos procesados: " . json_encode($datos));
    
    // Validar campos obligatorios
    $camposObligatorios = ['nombre', 'apellido_paterno', 'correo'];
    foreach ($camposObligatorios as $campo) {
        if (empty($datos[$campo])) {
            debug_log("ERROR: Campo obligatorio vacío: $campo");
            echo "<script>alert('Error: El campo $campo es obligatorio.'); history.back();</script>";
            exit;
        }
    }

    try {
        debug_log("Intentando conectar a la base de datos...");
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        debug_log("Conexión a BD exitosa");

        // Validar dominio del correo
        if (!correoPareceValido($datos['correo'])) {
            debug_log("ERROR: Dominio de correo inválido: " . $datos['correo']);
            echo "<script>alert('❌ El dominio del correo no parece válido.'); history.back();</script>";
            exit;
        }

        // Generar usuario único
        $nombreLimpio = strtolower(explode(" ", $datos['nombre'])[0]);
        $baseUsuario = $nombreLimpio . date("dm");
        $usuarioFinal = $baseUsuario;
        
        debug_log("Generando usuario único, base: $baseUsuario");
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE inicio_usuario = ?");
        $intentos = 0;
        while (true) {
            $stmt->execute([$usuarioFinal]);
            if ($stmt->fetchColumn() == 0) break;
            $usuarioFinal = $baseUsuario . rand(10, 99);
            $intentos++;
            if ($intentos > 10) break; // Evitar bucle infinito
        }
        
        debug_log("Usuario final generado: $usuarioFinal");
        
        $passwordHash = password_hash($usuarioFinal, PASSWORD_BCRYPT);

        // TRANSACCIÓN PARA ASEGURAR CONSISTENCIA
        $pdo->beginTransaction();
        debug_log("Iniciando transacción...");

        // 1. Insertar voluntario
        debug_log("Insertando en tabla voluntarios...");
        $stmt1 = $pdo->prepare("INSERT INTO voluntarios 
            (nombre, apellido_paterno, apellido_materno, correo, telefono_fijo, celular, curp, tipo_sangre, fecha_registro, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')");
        $result1 = $stmt1->execute([
            $datos['nombre'], $datos['apellido_paterno'], $datos['apellido_materno'],
            $datos['correo'], $datos['telefono_fijo'], $datos['celular'],
            $datos['curp'], $datos['tipo_sangre'], $datos['fecha_registro']
        ]);
        debug_log("Voluntario insertado, resultado: " . ($result1 ? 'éxito' : 'fallo'));

        $id_voluntario = $pdo->lastInsertId();
        debug_log("ID del voluntario: $id_voluntario");

        // 2. Insertar usuario
        debug_log("Insertando en tabla usuarios...");
        $stmt2 = $pdo->prepare("INSERT INTO usuarios (id_voluntario, inicio_usuario, nombre, password, departamento, requiere_cambio) 
                                VALUES (?, ?, ?, ?, ?, 1)");
        $result2 = $stmt2->execute([
            $id_voluntario, $usuarioFinal, $datos['nombre'],
            $passwordHash, $datos['departamento']
        ]);
        debug_log("Usuario insertado, resultado: " . ($result2 ? 'éxito' : 'fallo'));

        // 3. Insertar dirección (solo si hay datos)
        if (!empty($datos['calle']) || !empty($datos['ciudad'])) {
            debug_log("Insertando dirección...");
            $stmt3 = $pdo->prepare("INSERT INTO direccion_voluntarios
                (id_voluntario, calle, numero_exterior, codigo_postal, colonia, ciudad, municipio, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result3 = $stmt3->execute([
                $id_voluntario, $datos['calle'], $datos['numero_exterior'], $datos['codigo_postal'],
                $datos['colonia'], $datos['ciudad'], $datos['municipio'], $datos['estado']
            ]);
            debug_log("Dirección insertada, resultado: " . ($result3 ? 'éxito' : 'fallo'));
        }

        // 4. Insertar contacto emergencia (solo si hay datos)
        if (!empty($datos['nombre_completo'])) {
            debug_log("Insertando contacto de emergencia...");
            $stmt4 = $pdo->prepare("INSERT INTO contacto_emergencia 
                        (id_voluntario, nombre_completo, parentesco, telefono)
                        VALUES (?, ?, ?, ?)");
            $result4 = $stmt4->execute([
                $id_voluntario, $datos['nombre_completo'], $datos['parentesco'], $datos['telefono']
            ]);
            debug_log("Contacto emergencia insertado, resultado: " . ($result4 ? 'éxito' : 'fallo'));
        }

        // 5. Insertar información institucional (solo si hay datos)
        if (!empty($datos['delegacion_local'])) {
            debug_log("Insertando información institucional...");
            $stmt5 = $pdo->prepare("INSERT INTO informacion_institucional 
                            (id_voluntario, delegacion_local, fecha_ingreso, coordinacion, correo_institucional, 
                            grado_estudios, carrera_estudiada, idiomas) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result5 = $stmt5->execute([
                $id_voluntario, $datos['delegacion_local'], $datos['fecha_ingreso'], $datos['coordinacion'],
                $datos['correo_institucional'], $datos['grado_estudios'], $datos['carrera_estudiada'], $datos['idiomas']
            ]);
            debug_log("Info institucional insertada, resultado: " . ($result5 ? 'éxito' : 'fallo'));
        }

        // 6. Insertar datos adicionales (siempre, con valores por defecto)
        debug_log("Insertando datos adicionales...");
        $stmt6 = $pdo->prepare("INSERT INTO datos_adicionales 
                    (id_voluntario, tiene_pasaporte, numero_pasaporte, tiene_licencia, numero_licencia, tipo_asociado, numero_asociado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result6 = $stmt6->execute([
            $id_voluntario, $datos['tiene_pasaporte'], $datos['numero_pasaporte'],
            $datos['tiene_licencia'], $datos['numero_licencia'], $datos['tipo_asociado'], $datos['numero_asociado']
        ]);
        debug_log("Datos adicionales insertados, resultado: " . ($result6 ? 'éxito' : 'fallo'));

        // 7. Insertar especialidades médicas (solo si hay datos)
        if (!empty($datos['nombre_especialidad'])) {
            debug_log("Insertando especialidades médicas...");
            $stmt7 = $pdo->prepare("INSERT INTO especialidades_medicas 
                            (id_voluntario, nombre_especialidad, fecha_inicio, fecha_termino) 
                            VALUES (?, ?, ?, ?)");
            $result7 = $stmt7->execute([
                $id_voluntario, $datos['nombre_especialidad'], $datos['fecha_inicio'], $datos['fecha_termino']
            ]);
            debug_log("Especialidades insertadas, resultado: " . ($result7 ? 'éxito' : 'fallo'));
        }

        // Confirmar transacción
        $pdo->commit();
        debug_log("Transacción confirmada exitosamente");

        // Enviar correo
        debug_log("Intentando enviar correo...");
        $correoEnviado = enviarCorreo($datos['correo'], $usuarioFinal);
        debug_log("Correo enviado: " . ($correoEnviado ? 'sí' : 'no'));

        if ($correoEnviado) {
            debug_log("=== PROCESO COMPLETADO EXITOSAMENTE ===");
            echo "<script>
                alert('¡Registro exitoso! Tu información ha sido enviada correctamente. Recibirás un correo de confirmación.');
                window.location.href = 'index.html';
            </script>";
        } else {
            debug_log("=== PROCESO COMPLETADO CON ADVERTENCIA (sin correo) ===");
            echo "<script>
                alert('¡Registro exitoso! Sin embargo, no se pudo enviar el correo de confirmación.');
                window.location.href = 'index.html';
            </script>";
        }

    } catch (PDOException $e) {
        // Rollback en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        debug_log("ERROR DE BD: " . $e->getMessage());
        debug_log("=== PROCESO FALLIDO ===");
        
        echo "<script>
            alert('Error al guardar los datos: " . addslashes($e->getMessage()) . "');
            history.back();
        </script>";
    } catch (Exception $e) {
        debug_log("ERROR GENERAL: " . $e->getMessage());
        echo "<script>
            alert('Error inesperado: " . addslashes($e->getMessage()) . "');
            history.back();
        </script>";
    }
} else {
    debug_log("Acceso inválido - método no POST");
    echo "<script>
        alert('Acceso inválido.');
        window.location.href = 'index.html';
    </script>";
}
exit();
?>