<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_FILES['csvFile'])) {
        $file = $_FILES['csvFile'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        if($fileError === 0) {
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            if($fileExt === 'csv') {
                require_once '../config/db.php';
                
                $fileHandle = fopen($fileTmpName, 'r');
                if($fileHandle === false) {
                    header("Location: ./registroCsv.php?error=3");
                    exit();
                }
                
                $conn->begin_transaction(); // Iniciar transação para inserções
                
                try {
                    // Pular cabeçalho se existir
                    fgetcsv($fileHandle, 1000, ",");
                    
                    while (($data = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
                        $data = array_map('trim', $data); // Remover espaços em branco
                        
                        // Validar dados (verificar se a data está presente)
                        if (empty($data[0])) continue;
                        
                        // Converter campos vazios para NULL
                        $date = !empty($data[0]) ? $conn->real_escape_string($data[0]) : NULL;
                        $hora1 = !empty($data[1]) ? $conn->real_escape_string($data[1]) : NULL;
                        $hora2 = !empty($data[2]) ? $conn->real_escape_string($data[2]) : NULL;
                        $hora3 = !empty($data[3]) ? $conn->real_escape_string($data[3]) : NULL;
                        $hora4 = !empty($data[4]) ? $conn->real_escape_string($data[4]) : NULL;
                        $hora5 = !empty($data[5]) ? $conn->real_escape_string($data[5]) : NULL;
                        $hora6 = !empty($data[6]) ? $conn->real_escape_string($data[6]) : NULL;
                        $obs = !empty($data[7]) ? $conn->real_escape_string($data[7]) : NULL;
                        
                        // Converter formato de data de DD-MM-AA para YYYY-MM-DD
                        $dateParts = explode('-', $date);
                        if(count($dateParts) === 3) {
                            $day = $dateParts[0];
                            $month = $dateParts[1];
                            $year = '20' . $dateParts[2]; // Assumindo século 21 para anos de 2 dígitos
                            $mysqlDate = "$year-$month-$day";
                        } else {
                            continue; // Pular linha se a data for inválida
                        }

                        // Inserir no banco de dados
                        $sql_insert = "INSERT INTO registros 
                            (id_usuario, data, registo1, registo2, registo3, registo4, registo5, registo6, obs, mode) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
                        
                        $stmt = $conn->prepare($sql_insert);
                        if ($stmt === false) {
                            throw new Exception("Erro na preparação da consulta: " . htmlspecialchars($conn->error));
                        }
                        
                        // Converter strings de hora para formato time (HH:MM:SS)
                        $time1 = $hora1 ? date('H:i:s', strtotime($hora1)) : NULL;
                        $time2 = $hora2 ? date('H:i:s', strtotime($hora2)) : NULL;
                        $time3 = $hora3 ? date('H:i:s', strtotime($hora3)) : NULL;
                        $time4 = $hora4 ? date('H:i:s', strtotime($hora4)) : NULL;
                        $time5 = $hora5 ? date('H:i:s', strtotime($hora5)) : NULL;
                        $time6 = $hora6 ? date('H:i:s', strtotime($hora6)) : NULL;
                        
                        if (!$stmt->bind_param("issssssss", 
                            $_SESSION['user']['id'], 
                            $mysqlDate, 
                            $time1, 
                            $time2, 
                            $time3, 
                            $time4, 
                            $time5, 
                            $time6, 
                            $obs)) {
                            throw new Exception("Erro ao vincular parâmetros: " . htmlspecialchars($stmt->error));
                        }
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Erro ao inserir registro: " . htmlspecialchars($stmt->error));
                        }
                    }
                    
                    $conn->commit(); // Confirmar transação
                    fclose($fileHandle);
                    header("Location: ./registroCsv.php?success=1");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback(); // Reverter transação em caso de erro
                    fclose($fileHandle);
                    error_log($e->getMessage()); // Registrar erro para depuração
                    header("Location: ./registroCsv.php?error=4");
                    exit();
                }
            } else {
                header("Location: ./registroCsv.php?error=1");
                exit();
            }
        } else {
            header("Location: ./registroCsv.php?error=2");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../styles/fav.png" type="image/x-icon">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>Importar Registros</title>
    <style>
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        .enviar {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
        .success {
            color: green;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../styles/fav.png" alt="Logo">
        </div>
        <div class="user-info">
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></p>
        </div>
        <div class="menu">
            <a href="../">Home</a>
            <a href="./feriados.php">Feriados</a>
            <a href="./registroCsv.php">Importar Registros</a>
            <a href="./configs.php">Configurações</a>
        </div>
        <form action="../" method="get">
            <button type="submit" class="btn-logout" name="logout">Sair</button>
        </form>
    </div>
    <div class="container">
        <h1>Importar Registros</h1>
        <form class="form" action="./registroCsv.php" method="post" enctype="multipart/form-data">
            <label for="csvFile">Selecione o arquivo CSV:</label>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" required> <br>
            <i style="font-size: 12px;">Formato aceito: Data (DD-MM-AA), Hora 1, Hora 2, Hora 3, Hora 4, Hora 5, Hora 6 e Observação</i><br><br>
            <button class="enviar" type="submit">Importar</button>
        </form>

        <?php
            if (isset($_GET['success'])) {
                echo '<div class="success">Arquivo importado com sucesso!</div>';
            } elseif (isset($_GET['error'])) {
                $errorMsg = "Erro ao importar o arquivo.";
                switch($_GET['error']) {
                    case 1: $errorMsg = "Formato de arquivo inválido. Apenas arquivos CSV são aceitos."; break;
                    case 2: $errorMsg = "Erro ao enviar o arquivo."; break;
                    case 3: $errorMsg = "Erro ao abrir o arquivo."; break;
                    case 4: $errorMsg = "Erro ao processar o arquivo. Verifique o formato dos dados."; break;
                }
                echo '<div class="error">' . $errorMsg . '</div>';
            }
        ?>
    </div>
</body>
</html>