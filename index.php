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

require_once './config/db.php';

// Configurações do usuário
$stmt = $conn->prepare("SELECT * FROM configs WHERE id_usuario = ?");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$configs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$data = [];
foreach ($configs as $row) {
    $data[$row['nome']] = $row['valor'];
}

// Horários nominais
$stmt = $conn->prepare("SELECT * FROM nominal WHERE id_usuario = ?");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$nominais = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$data_h = [];
foreach ($nominais as $row) {
    $ds = $row['dia_semana'];
    for ($i = 1; $i <= 6; $i++) {
        $data_h[$ds]['h'.$i] = $row['hora'.$i];
    }
}

// Fechamento
$fechamento = isset($data['fechamento']) ? $data['fechamento'] : 25;
$mes = isset($_GET['mes']) ? $_GET['mes'] : date("m");
$ano = isset($_GET['ano']) ? $_GET['ano'] : date("Y");

$diaUltimo = $fechamento + 1;
$dataInicio = new DateTime("$ano-$mes-$diaUltimo");
$dataInicio->modify('-1 month');
$dataFim = new DateTime("$ano-$mes-$fechamento");

// Registros no período
$stmt = $conn->prepare("SELECT * FROM registros WHERE id_usuario = ? AND data BETWEEN ? AND ? ORDER BY data ASC");
$di = $dataInicio->format('Y-m-d');
$df = $dataFim->format('Y-m-d');
$stmt->bind_param("iss", $_SESSION['user']['id'], $di, $df);
$stmt->execute();
$regs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Mapeia registros por data
$registrosPorData = [];
foreach ($regs as $r) {
    $registrosPorData[$r['data']] = $r;
}

function calcularMetaDiaria($diaSemanaIngles, $data_h) {
    $map = [
        'Sunday'=>'Domingo','Monday'=>'Segunda','Tuesday'=>'Terça',
        'Wednesday'=>'Quarta','Thursday'=>'Quinta','Friday'=>'Sexta','Saturday'=>'Sábado'
    ];
    $dia = $map[$diaSemanaIngles] ?? null;
    if (!$dia) return 0;

    $meta = 0;
    for ($i = 1; $i <= 5; $i += 2) {
        $in = $data_h[$dia]['h'.$i] ?? null;
        $out = $data_h[$dia]['h'.($i+1)] ?? null;
        if ($in && $out) {
            $meta += (strtotime($out) - strtotime($in)) / 3600;
        }
    }
    return $meta;
}

function transleteDia($diaSemanaIngles){
    $map = [
        'Sunday'=>'Domingo','Monday'=>'Segunda','Tuesday'=>'Terça',
        'Wednesday'=>'Quarta','Thursday'=>'Quinta','Friday'=>'Sexta','Saturday'=>'Sábado'
    ];

    return $map[$diaSemanaIngles] ?? null;
}

$hoje = date('Y-m-d');
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
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#f2f2f2; }
        .periodo-info { margin:15px 0; font-size:1.1em; font-weight:bold; }
        input[type=time] { border:none; padding:0; margin:0; }
        .mode-select{
            width: 100px; /* Largura do select */
            padding: 10px; /* Espaçamento interno */
            margin: 5px 0; /* Margem ao redor do select */
            border: 1px solid #ccc; /* Borda cinza clara */
            border-radius: 5px; /* Bordas arredondadas */
            background-color: #f9f9f9; /* Cor de fundo cinza claro */
        }
    </style>
</head>
<body>
    <div class="navbar">
    <div class="logo">
            <img src="./styles/fav.png" alt="Logo">
        </div>
        <div class="user-info">
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></p>
        </div>
        <div class="menu">
            <a href="./">Home</a>
            <a href="./telas/feriados.php">Feriados</a>
            <a href="./telas/registroCsv.php">Importar Registros</a>
            <a href="./telas/configs.php">Configurações</a>
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
                    <option value="01" <?= (isset($_GET['mes']) && $_GET['mes'] == "01") || (!isset($_GET['mes']) && date("m") == "01") ? 'selected' : '' ?>>Janeiro</option>
                    <option value="02" <?= (isset($_GET['mes']) && $_GET['mes'] == "02") || (!isset($_GET['mes']) && date("m") == "02") ? 'selected' : '' ?>>Fevereiro</option>
                    <option value="03" <?= (isset($_GET['mes']) && $_GET['mes'] == "03") || (!isset($_GET['mes']) && date("m") == "03") ? 'selected' : '' ?>>Março</option>
                    <option value="04" <?= (isset($_GET['mes']) && $_GET['mes'] == "04") || (!isset($_GET['mes']) && date("m") == "04") ? 'selected' : '' ?>>Abril</option>
                    <option value="05" <?= (isset($_GET['mes']) && $_GET['mes'] == "05") || (!isset($_GET['mes']) && date("m") == "05") ? 'selected' : '' ?>>Maio</option>
                    <option value="06" <?= (isset($_GET['mes']) && $_GET['mes'] == "06") || (!isset($_GET['mes']) && date("m") == "06") ? 'selected' : '' ?>>Junho</option>
                    <option value="07" <?= (isset($_GET['mes']) && $_GET['mes'] == "07") || (!isset($_GET['mes']) && date("m") == "07") ? 'selected' : '' ?>>Julho</option>
                    <option value="08" <?= (isset($_GET['mes']) && $_GET['mes'] == "08") || (!isset($_GET['mes']) && date("m") == "08") ? 'selected' : '' ?>>Agosto</option>
                    <option value="09" <?= (isset($_GET['mes']) && $_GET['mes'] == "09") || (!isset($_GET['mes']) && date("m") == "09") ? 'selected' : '' ?>>Setembro</option>
                    <option value="10" <?= (isset($_GET['mes']) && $_GET['mes'] == "10") || (!isset($_GET['mes']) && date("m") == "10") ? 'selected' : '' ?>>Outubro</option>
                    <option value="11" <?= (isset($_GET['mes']) && $_GET['mes'] == "11") || (!isset($_GET['mes']) && date("m") == "11") ? 'selected' : '' ?>>Novembro</option>
                    <option value="12" <?= (isset($_GET['mes']) && $_GET['mes'] == "12") || (!isset($_GET['mes']) && date("m") == "12") ? 'selected' : '' ?>>Dezembro</option>
                </select>
                <select class="select" name="ano" id="ano">
                    <?php
                        $currentYear = date("Y");
                        $selectedYear = isset($_GET['ano']) ? $_GET['ano'] : $currentYear;
                        for ($i = $currentYear; $i >= 2000; $i--) {
                            echo "<option value='$i'" . ($i == $selectedYear ? ' selected' : '') . ">$i</option>";
                        }
                    ?>
                </select>
                <button class="btn-filter" type="submit">Filtrar</button>
            </form>
            <div class="cabecalho">
                total de horas: <span class="total-horas">00:00</span>
                total de horas faltantes: <span class="total-faltantes">00:00</span>
                total de horas extras 50%: <span class="total-extras">00:00</span>
                total de horas extras 100%: <span class="total-extras-50">00:00</span>
            </div>
            
            <div class="periodo-info">
                Período: <?php echo $dataInicio->format('d/m/Y').' a '.$dataFim->format('d/m/Y'); ?>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Reg1</th><th>Reg2</th><th>Reg3</th>
                        <th>Reg4</th><th>Reg5</th><th>Reg6</th>
                        <th>Total</th><th>Meta</th>
                        <th>Faltantes</th><th>Extra 50%</th><th>Extra100%</th>
                        <th>Saldo</th>
                        <th>Obs</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $period = new DatePeriod($dataInicio, new DateInterval('P1D'), $dataFim->modify('+1 day'));
                $saldo = 0;
                $saldoAnt=0;
                foreach ($period as $d) {
                    $dt = $d->format('Y-m-d');
                    $row = $registrosPorData[$dt] ?? [];
                    $dia = $d->format('l');
                    $style = ($dt==$hoje) ? "background:rgba(66,214,240,0.28)" : (($dia=='Sunday')?"background:rgba(240,66,66,0.28)":'');
                    echo "<tr style='{$style}'>";
                    $diaSemHoje = transleteDia($dia);
                    echo "<td>{$d->format('d/m/Y')} - {$diaSemHoje}</td>";
                    for ($i=1; $i<=6; $i++) {
                        $campo = 'registo'.$i;
                        $val = $row[$campo] ?? '';
                        $txt = $val && $val!='00:00:00' ? date('H:i',strtotime($val)) : '';
                        echo "<td class='editable' data-data='{$dt}' data-campo='{$campo}'>{$txt}</td>";
                    }
                    $meta = ($hoje >= $dt) ? calcularMetaDiaria($dia, $data_h) : 0;
                    
                    $totalWorked = 0;
                    $faltantes = 0;
                    $extra50 = 0;
                    $extra100 = 0;

                    // Configurações
                    $toleranciaPonto = isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] / 60 : 0; // em horas
                    $toleranciaGeral = isset($data['toleranciaGeral']) ? $data['toleranciaGeral'] / 60 : 0; // em horas
                    $maximo50 = isset($data['maximo50']) ? $data['maximo50'] / 60 : 0; // em horas
                    $mode = isset($row['mode']) ? $row['mode'] : null;
                    $diaSemana = transleteDia($d->format('l'));

                    // Ajusta meta se for folga/feriado
                    if (in_array($mode, ['Folga', 'Feriado'])) {
                        $meta = 0;
                    }

                    // Calcular horas trabalhadas com tolerância por ponto
                    for ($i = 1; $i <= 5; $i += 2) {
                        $regIn = $row['registo' . $i] ?? null;
                        $regOut = $row['registo' . ($i + 1)] ?? null;

                        if ($regIn && $regOut && $regIn != '00:00:00' && $regOut != '00:00:00') {
                            $regInTime = DateTime::createFromFormat('H:i:s', $regIn);
                            $regOutTime = DateTime::createFromFormat('H:i:s', $regOut);

                            // Obter intervalo nominal correspondente
                            $intervalIndex = ($i - 1) / 2;
                            $nominalIn = isset($data_h[$diaSemana]['h' . ($i)]) ? DateTime::createFromFormat('H:i:s', $data_h[$diaSemana]['h' . ($i)]) : null;
                            $nominalOut = isset($data_h[$diaSemana]['h' . ($i + 1)]) ? DateTime::createFromFormat('H:i:s', $data_h[$diaSemana]['h' . ($i + 1)]) : null;

                            if ($nominalIn && $nominalOut) {
                                // Aplicar tolerância por ponto
                                $diffIn = $regInTime->diff($nominalIn)->i + $regInTime->diff($nominalIn)->h * 60;
                                if ($diffIn > 0 && $diffIn <= $data['toleranciaPonto']) {
                                    $regInTime = clone $nominalIn;
                                }

                                $diffOut = $nominalOut->diff($regOutTime)->i + $nominalOut->diff($regOutTime)->h * 60;
                                if ($diffOut > 0 && $diffOut <= $data['toleranciaPonto']) {
                                    $regOutTime = clone $nominalOut;
                                }
                            }

                            // Calcular horas trabalhadas no intervalo
                            $interval = $regOutTime->diff($regInTime);
                            $totalWorked += $interval->h + $interval->i / 60;
                        }
                    }

                    // Calcular diferença
                    $difference = $totalWorked - $meta;

                    // Determinar faltantes/extras
                    if ($difference < 0) {
                        $faltantes = max(abs($difference), 0);
                    } else {
                        if ($diaSemana == 'Domingo' || $mode == 'Feriado') {
                            $extra100 = $difference;
                        } else {
                            $extra50 = min($difference, $maximo50);
                            $extra100 = max($difference - $maximo50, 0);
                        }
                    }




                    echo "<td>".str_replace('.', ':', number_format($totalWorked, 2))."</td>";

                    $cor = "style='color: rgba(255, 255, 255, 0)'";
                    echo "<td>".str_replace('.', ':', number_format($meta, 2))."</td>";
                    echo "<td ".($faltantes != 0.00 ? '' : $cor).">". str_replace('.', ':', number_format($faltantes, 2))."</td>";
                    echo "<td ".($extra50 != 0.00 ? '' : $cor).">".str_replace('.', ':', number_format($extra50, 2))."</td>";
                    echo "<td ".($extra100 != 0.00 ? '' : $cor).">".str_replace('.', ':', number_format($extra100, 2))."</td>";
                    $saldo += ($extra50 + $extra100) - $faltantes;
                    
                    echo "<td ".($saldo == $saldoAnt ? $cor : '').">" . str_replace('.', ':', number_format($saldo, 2)). "</td>";
                    echo "<td>
                        <input type='text' class='obs-input' data-data=". $r['data'] ." value=". htmlspecialchars($r['obs'] ?? '') .">
                    </td>";
                    echo "<td> 
                        <select class='mode-select' data-data=". $r['data'] .">
                            <option value=''></option>
                            <option value='Folga' ". ((isset($registrosPorData[$dt]) && isset($registrosPorData[$dt]['mode']) ? $registrosPorData[$dt]['mode'] : '') == 'Folga' ? 'selected' : ''). ">Folga</option>
                            <option value='Feriado' ". ((isset($registrosPorData[$dt]) && isset($registrosPorData[$dt]['mode']) ? $registrosPorData[$dt]['mode'] : '') == 'Feriado' ? 'selected' : '') .">Feriado</option>
                            <option value='Feriado Meio' ". ((isset($registrosPorData[$dt]) && isset($registrosPorData[$dt]['mode']) ? $registrosPorData[$dt]['mode'] : '') == 'Feriado Meio' ? 'selected' : '') .">Feriado Meio</option>
                            <option value='folga bonificada' ". ((isset($registrosPorData[$dt]) && isset($registrosPorData[$dt]['mode']) ? $registrosPorData[$dt]['mode'] : '') == 'Folga bonificada' ? 'selected' : ''). ">Folga bonificada</option>
                        </select>
                    </td>";
                    echo "</tr>";
                    $saldoAnt = $saldo;
                }
                ?>
                    <script>
                        document.querySelectorAll('.obs-input').forEach(input => {
                            input.addEventListener('change', () => {
                                const data = input.dataset.data;
                                const valor = input.value;
                                fetch('./atualizar_registro.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ campo: 'obs', valor, data })
                                });
                            });
                        });

                        document.querySelectorAll('.mode-select').forEach(select => {
                            select.addEventListener('change', () => {
                                const data = select.dataset.data;
                                const valor = select.value;
                                fetch('./atualizar_registro.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ campo: 'mode', valor, data })
                                });
                            });
                        });
                    </script>

                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.querySelectorAll('td.editable').forEach(td => {
      td.addEventListener('dblclick', () => {
        if (td.querySelector('input')) return;
        const old = td.textContent.trim();
        const inp = document.createElement('input');
        inp.type = 'time'; inp.value = old||'00:00'; inp.style.width='100%';
        td.textContent=''; td.appendChild(inp); inp.focus();
        function save(){
          const v = inp.value;
          fetch('update_registro.php',{method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`data=${td.dataset.data}&campo=${td.dataset.campo}&valorTime=${v}`
          })
          .then(r=>r.text()).then(t=>{
            td.textContent = t.trim()==='ok'?v:old;
            if(t.trim()!=='ok') alert('Erro ao salvar: '+t);
          }).catch(e=>{ alert('Erro: '+e); td.textContent=old; });
        }
        inp.addEventListener('blur', save);
        inp.addEventListener('keydown', e=>{
          if(e.key==='Enter'){ e.preventDefault(); inp.blur(); }
          if(e.key==='Escape'){ td.textContent=old; }
        });
      });
    });
    </script>
</body>
</html>
