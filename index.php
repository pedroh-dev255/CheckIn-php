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
        if(isset($row['hora'.$i]) && $row['hora'.$i] != null || $row['hora'.$i] != '') {
            $data_h[$ds]['h'.$i] = $row['hora'.$i];
        } else {
            $data_h[$ds]['h'.$i] = "00:00";
        }
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

function timeToMinutes($time) {
    if (!$time || $time == '00:00') return 0;
    list($h, $m) = explode(':', $time);
    return ($h * 60) + $m; // ignora os segundos
}


function MinutsTohour($time) {
    if (!$time || $time == '00:00:00') return '00:00';
    $hours = floor($time / 60);
    $minutes = $time % 60;
    return sprintf('%02d:%02d', $hours, $minutes);
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
        .footer { text-align:center; padding:20px; background:#f2f2f2; }
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

            <button onclick="executarBackground()">Recalcular as horas extras em Background</button>
            
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
                $saldo = timeToMinutes("03:31");
                $saldoAnt=0;
                $saldofal = 0;
                $saldo50 = 0;
                $saldo100 = 0;
                foreach ($period as $d) {
                    $dt = $d->format('Y-m-d');
                    $row = $registrosPorData[$dt] ?? [];
                    $dia = $d->format('l');
                    $diaSemHoje = transleteDia($dia);
                    $totalWorked = MinutsTohour(0);
                    $meta = MinutsTohour(0);

                    // Verifica por ponto se o ponto esta dentro da tolerancia por ponto
                    $horario1 = (isset($row['registo1']) ? ((abs(timeToMinutes($row['registo1']) - timeToMinutes($data_h[$diaSemHoje]['h1'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo1'] : $data_h[$diaSemHoje]['h1'] ) : null);
                    $horario2 = (isset($row['registo2']) ? ((abs(timeToMinutes($row['registo2']) - timeToMinutes($data_h[$diaSemHoje]['h2'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo2'] : $data_h[$diaSemHoje]['h2'] ) : null);
                    $horario3 = (isset($row['registo3']) ? ((abs(timeToMinutes($row['registo3']) - timeToMinutes($data_h[$diaSemHoje]['h3'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo3'] : $data_h[$diaSemHoje]['h3'] ) : null);
                    $horario4 = (isset($row['registo4']) ? ((abs(timeToMinutes($row['registo4']) - timeToMinutes($data_h[$diaSemHoje]['h4'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo4'] : $data_h[$diaSemHoje]['h4'] ) : null);
                    $horario5 = (isset($row['registo5']) ? ((abs(timeToMinutes($row['registo5']) - timeToMinutes($data_h[$diaSemHoje]['h5'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo5'] : $data_h[$diaSemHoje]['h5'] ) : null);
                    $horario6 = (isset($row['registo6']) ? ((abs(timeToMinutes($row['registo6']) - timeToMinutes($data_h[$diaSemHoje]['h6'])) > (isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] : 0)) ? $row['registo6'] : $data_h[$diaSemHoje]['h6'] ) : null);
                    
                    //echo $row['registo1'] . " " . $row['registo2'] . " " . $row['registo3'] . " " . $row['registo4'] . " " . $row['registo5'] . " " . $row['registo6'] . "<br>";
                    //echo $horario1 . " " . $horario2 . " " . $horario3 . " " . $horario4 . " " . $horario5 . " " . $horario6 . "<br>";
                    //echo MinutsTohour((timeToMinutes($horario2) - timeToMinutes($horario1)) + (timeToMinutes($horario4) - timeToMinutes($horario3)) + (timeToMinutes($horario6) - timeToMinutes($horario5))) . " - " . MinutsTohour(timeToMinutes($horario2) - timeToMinutes($horario1)) . " " . MinutsTohour(timeToMinutes($horario4) - timeToMinutes($horario3)) . " " . MinutsTohour(timeToMinutes($horario6) - timeToMinutes($horario5)) . "<br><br>";
                    
                    $totalWorked = MinutsTohour((timeToMinutes((isset($horario2) ? $horario2 : 0 )) - timeToMinutes((isset($horario1) ? $horario1 : 0 ))) + (timeToMinutes((isset($horario4) ? $horario4 : 0 )) - timeToMinutes((isset($horario3) ? $horario3 : 0 ))) + (timeToMinutes((isset($horario6) ? $horario6 : 0 )) - timeToMinutes((isset($horario5) ? $horario5 : 0 ))));
                    $mode = isset($row['mode']) ? $row['mode'] : null;
                    
                    if ($dt<=$hoje){
                        if( isset($row['mode'])){
                            if($row['mode'] == 'Feriado' || $row['mode'] == 'Folga bonificada'){
                                $meta = MinutsTohour(0);

                            }else if ($row['mode'] == 'Feriado Meio'){
                                $meta = MinutsTohour((timeToMinutes('08:00') - timeToMinutes('14:00')));
                            }else { 
                                $meta = MinutsTohour((timeToMinutes($data_h[$diaSemHoje]['h2']) - timeToMinutes($data_h[$diaSemHoje]['h1'])) + (timeToMinutes($data_h[$diaSemHoje]['h4']) - timeToMinutes($data_h[$diaSemHoje]['h3'])) + (timeToMinutes($data_h[$diaSemHoje]['h6']) - timeToMinutes($data_h[$diaSemHoje]['h5'])));
                            }
                        }else {
                            $meta = MinutsTohour((timeToMinutes($data_h[$diaSemHoje]['h2']) - timeToMinutes($data_h[$diaSemHoje]['h1'])) + (timeToMinutes($data_h[$diaSemHoje]['h4']) - timeToMinutes($data_h[$diaSemHoje]['h3'])) + (timeToMinutes($data_h[$diaSemHoje]['h6']) - timeToMinutes($data_h[$diaSemHoje]['h5'])));

                        }
                    }else {
                        $meta = MinutsTohour(0);
                    }
                    
                    $totalWorked = abs(timeToMinutes($totalWorked) - timeToMinutes($meta)) >= $data['toleranciaGeral'] ? $totalWorked : $meta;

                    $style = ($dt==$hoje) ? "background:rgba(66,214,240,0.28)" : (($dia=='Sunday')?"background:rgba(240,66,66,0.28)":'');
                    echo "<tr style='{$style}'>";
                    
                    echo "<td>{$d->format('d/m/Y')} - {$diaSemHoje}</td>";
                    for ($i=1; $i<=6; $i++) {
                        $campo = 'registo'.$i;
                        $val = $row[$campo] ?? '';
                        $txt = $val && $val!='00:00:00' ? date('H:i',strtotime($val)) : '';
                        echo "<td class='editable' data-data='{$dt}' data-campo='{$campo}'>{$txt}</td>";
                    }

                    
                     
                    $faltantes = 0;
                    $extra50 = 0;
                    $extra100 = 0;

                    // Configurações
                    $toleranciaPonto = isset($data['toleranciaPonto']) ? $data['toleranciaPonto'] / 60 : 0;
                    $toleranciaGeral = isset($data['toleranciaGeral']) ? $data['toleranciaGeral'] / 60 : 0;

                    $maximo50 = isset($data['maximo50']) ? $data['maximo50'] : 0;
                    $diaSemana = transleteDia($d->format('l'));

                    $difference = 0;

                    $difference= timeToMinutes($totalWorked) - timeToMinutes($meta);

                    // Determinar faltantes/extras
                    if ($difference < 0) {
                        $faltantes =  $difference;
                    } else {
                        if ($diaSemana == 'Domingo' || $diaSemana == "Sábado" || $mode == 'Feriado') {
                            $extra100 = $difference;
                        } else {
                            if ($difference <= $maximo50) {
                                $extra50 = $difference;
                            } else {
                                $extra50 = $maximo50;
                                $difference -= $maximo50;
                                $extra100 = $difference;
                            }
                        }
                    }

                    echo "<td>".$totalWorked."</td>";

                    $cor = "style='color: rgba(255, 255, 255, 0)'";
                    echo "<td>". $meta ."</td>";
                    echo "<td ".($faltantes != 0.00 ? '' : $cor).">".'-' . MinutsTohour(($faltantes*-1))."</td>";
                    echo "<td ".($extra50 != 0.00 ? '' : $cor).">".MinutsTohour($extra50)."</td>";
                    echo "<td ".($extra100 != 0.00 ? '' : $cor).">".MinutsTohour($extra100)."</td>";
                    $saldo += ($extra50 + $extra100) + $faltantes;
                    $saldofal += $faltantes*-1;
                    $saldo50 += $extra50;
                    $saldo100 += $extra100;
                    
                    echo "<td ".($saldo == $saldoAnt ? $cor : '').">" . MinutsTohour($saldo). "</td>";
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
                <tr>
                    <td colspan="7" style="text-align: center; font-weight: bold;">Total:</td>
                    <td><?= MinutsTohour($saldo) ?></td>
                    <td></td>
                    <td>-<?= MinutsTohour($saldofal) ?></td>
                    <td><?= MinutsTohour($saldo50) ?></td>
                    <td><?= MinutsTohour($saldo100) ?></td>
                    <td><?= MinutsTohour($saldo) ?></td>
                    <td colspan="2" style="text-align: center; font-weight: bold;">---</td>
                </tr>
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
        <br><br><br><br>
        <div class="footer">
            <p>&copy; <?php echo date('Y')?> ClockIn. Todos os direitos reservados.</p>
            <p><a href="https://portifolio.phsolucoes.space">Pedro Henrique</a></p>
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


    function executarBackground() {
        fetch('calc_all.php')
            .then(response => {
                if (response.ok) {
                    console.log('Script rodando em background...');
                } else {
                    console.error('Erro ao executar script.');
                }
            })
            .catch(error => console.error('Erro:', error));
    }

    </script>
</body>
</html>
