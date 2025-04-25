<?php
    session_start();

    if(!isset($_SESSION['user'])) {
        header("Location: ./login.php");
        exit();
    }

    if(isset($_GET['logout'])) {
        session_destroy();
        header("Location: ./login.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClockIn</title>
    <link rel="stylesheet" href="./styles/home.css">
    <link rel="stylesheet" href="./styles/navbar.css">
    <link rel="shortcut icon" href="./styles/fav.png" type="image/x-icon">
    <style>
        
        
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="./styles/fav.png" alt="Logo">
        </div>
        <div class="user-info">
            <p>Bem-vindo, <?php echo $_SESSION['user']['nome']; ?></p>
        </div>
        <div class="menu">
            <a href="./">Home</a>
            <a href="./historico.php">Histórico</a>
            <a href="./telas/registroCsv.php">Importar Registros</a>
            <a href="./configuracoes.php">Configurações</a>
        </div>
        <form action="./" method="get">
            <button type="submit" class="btn-logout" name="logout">Sair</button>
        </form>
    </div>
    <div class="container">
        <div class="filtro">
            <h2>Filtro</h2>
            <form action="./" method="get">
                <select class="select" name="mes" id="mes">
                    <option value="janeiro"   <?php if(isset($_GET['mes'])){if($_GET['mes'] == "janeiro" ){echo "selected"   ;}}else if(date("F") == 'January'  ) {echo "selected";}?>>Janeiro</option>
                    <option value="fevereiro" <?php if(isset($_GET['mes'])){if($_GET['mes'] == "fevereiro" ){echo "selected" ;}}else if(date("F") == 'February' ) {echo "selected";}?>>Fevereiro</option>
                    <option value="março"     <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'março' ){echo "selected"     ;}}else if(date("F") == 'March'    ) {echo "selected";}?>>Março</option>
                    <option value="abril"     <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'abril' ){echo "selected"     ;}}else if(date("F") == 'April'    ) {echo "selected";}?>>Abril</option>
                    <option value="maio"      <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'maio' ){echo "selected"      ;}}else if(date("F") == 'May'      ) {echo "selected";}?>>Maio</option>
                    <option value="junho"     <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'junho' ){echo "selected"     ;}}else if(date("F") == 'June'     ) {echo "selected";}?>>Junho</option>
                    <option value="julho"     <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'julho' ){echo "selected"     ;}}else if(date("F") == 'July'     ) {echo "selected";}?>>Julho</option>
                    <option value="agosto"    <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'agosto' ){echo "selected"    ;}}else if(date("F") == 'August'   ) {echo "selected";}?>>Agosto</option>
                    <option value="setembro"  <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'setembro' ){echo "selected"  ;}}else if(date("F") == 'September') {echo "selected";}?>>Setembro</option>
                    <option value="outubro"   <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'outubro' ){echo "selected"   ;}}else if(date("F") == 'October'  ) {echo "selected";}?>>Outubro</option>
                    <option value="novembro"  <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'novembro' ){echo "selected"  ;}}else if(date("F") == 'November' ) {echo "selected";}?>>Novembro</option>
                    <option value="dezembro"  <?php if(isset($_GET['mes'])){if($_GET['mes'] == 'dezembro' ){echo "selected"  ;}}else if(date("F") == 'December' ) {echo "selected";}?>>Dezembro</option>
                </select>
                <select class="select" name="ano" id="ano">
                    <?php
                        $currentYear = date("Y");
                        $selectedYear = isset($_GET['ano']) ? $_GET['ano'] : $currentYear;
                        for ($i = $currentYear; $i >= 2000; $i--) {
                            if ($i == $selectedYear) {
                                echo "<option value='$i' selected>$i</option>";
                            } else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                    ?>
                </select>

                <button class="btn-filter" type="submit">Filtrar</button>
            </form>
        </div>

        <table>

        </table>
    </div>
</body>
</html>