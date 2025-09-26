<?
    session_start();

    if(!isset($_SESSION['user'])){
        exit();
    }

    function calc_saldo(){
        require_once('db.php');
        //verifica o mes anterior
        $mes = date('m');
        


    }

?>