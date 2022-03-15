<?php

                            //                                                                           Requête API                                                         
                            //                                                                                 ||
                            //                             GET :                                               ||                              POST :
                            // +Obtenir toute les valeur de consommation présente dans la base de donnée:      ||              +Informer d'un départ en vacances:
                            //     http://192.168.5.74/php-api/index.php                                       ||                  http://192.168.5.74/php-api/index.php?presence=valeur
                            // +Obtenir la dernière valeur de consommation reçu dans la base de donnée:        ||                      valeur = oui ou non 
                            //     http://192.168.5.74/php-api/index.php?ID=1                                  ||                      oui = relais OFF
                            // +Obtenir toute les état du relais présente dans la base de donnée:              ||                      non  = relais ON
                            //     http://192.168.5.74/php-api/index.php?ID=RELAIS                             ||
                            // +Obtenir le dernier état du relais reçu dans la base de donnée:                 ||
                            //     http://192.168.5.74/php-api/index.php?ID=2                                  ||                       
 

    include("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];

    function getConsommations()
    {
        global $conn;
        $query = "SELECT * FROM CONSOMMATION";
        $reponse = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    function getLastConsommation()
    {
        global $conn;
        $query = "SELECT * FROM CONSOMMATION ORDER BY ID DESC LIMIT 1";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    function getStatutRelais()
    {
        global $conn;
        $query = "SELECT * FROM RELAIS";
        $reponse = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    function getLastStatutRelais()
    {
        global $conn;
        $query = "SELECT * FROM RELAIS ORDER BY ID DESC LIMIT 1";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;//['id'+ 'etat'+ 'date'];
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    function AddPresence()
    {
        global $conn;
        $presence = $_POST["presence"];
        if($presence=="oui")
        {
           echo $query="INSERT INTO `VACANCES`(`vac`) VALUES ('".$presence."')"; 
        }
        else if($presence=="non")
        {
           echo $query="INSERT INTO `VACANCES`(`vac`) VALUES ('".$presence."')"; 
        }
        
        if(mysqli_query($conn, $query))
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Valeur ajoutée avec succès '
            );
        }
        else
        {
            $response=array(
                'status' => 0,
                'status_message' =>'ERROR!.'. mysqli_error($conn)
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }



    switch($request_method)
    {
        case 'GET':
            // Retrive Products
            if(!empty($_GET["ID"]))
            {
            $ID=intval($_GET["ID"]);
            if($ID==1)
            {
                getLastConsommation();
            }
            if($ID==2)
            {
                getLastStatutRelais();
            }
            if($ID=="RELAIS")
            {
                getStatutRelais();
            }
            }
            else
            {
                getConsommations();
                //getStatutRelais();
            }
            break;
        default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
            break;
        case 'POST':
            // Ajouter un produit
            AddPresence();
            break;
    }

?>