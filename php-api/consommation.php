<?php

                            //                                                                           Requête API                                                         
                            //                                                                                 ||
                            //                             GET :                                               ||                              POST :
                            // +Obtenir toute les valeur de consommation présente dans la base de donnée:      ||              +Changer etat relais:
                            //     http://192.168.5.74/php-api/index.php                                       ||                  http://192.168.5.74/php-api/index.php?etat=valeur
                            // +Obtenir la dernière valeur de consommation reçu dans la base de donnée:        ||                      valeur = oui ou non 
                            //     http://192.168.5.74/php-api/index.php?ID=1                                  ||                      oui = relais OFF
                            // +Savoir si la maisson est occupé ou non                                         ||                      non  = relais ON
                            //      http://192.168.5.74/php-api/index.php?ID=3                                 ||               +Informer d'un départ, d'une abscence
                            // +Obtenir le dernier état du relais reçu dans la base de donnée:                 ||                   http://192.168.5.74/php-api/index.php?presence=valeur
                            //     http://192.168.5.74/php-api/index.php?ID=2                                  ||                       valeur = oui ou non 
                            //                                                                                 ||                       oui = Maison occupée alors script détection fuite non activée
                            //                                                                                 ||                       non = Maison vide alors script détection fuite est lancée
 

    include("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];




    function janvier()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220101000000 AND date<20220201000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function fevrier()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220201000000 AND date<20220301000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function mars()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220301000000 AND date<20220401000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function avril()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220401000000 AND date<20220501000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function mai()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220501000000 AND date<20220601000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function juin()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220601000000 AND date<20220701000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    function juillet()
    {
        global $conn;
        $query = "SELECT SUM(consommation) FROM CONSOMMATION WHERE date >=20220701000000 AND date<20220801000000";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    

    



    switch($request_method)
    {
        case 'GET':
            // Retrive Products
            if(!empty($_GET["ID"]))
            {
            $ID=intval($_GET["ID"]);
            if($ID==01)
            {
                janvier();
            }
            if($ID==02)
            {
                fevrier();
            }
            if($ID==03)
            {
                mars();
            }
            if($ID==04)
            {
                avril();
            }
            if($ID==05)
            {
                mai();
            }
            if($ID==06)
            {
                juin();
            }
            if($ID==07)
            {
                juillet();
            }
            }
    
            break;
        default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
            break;
       
    }

?>