<?php

	require("classes/phpMQTT.php");

	$server = "192.168.5.74";
	$port = 1883;
	$username_mqtt = "esp8266";
	$password_mqtt = "esp8266";
	$client_id = "script_php_mqtt";

	$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

	if(!$mqtt->connect(true, NULL, $username_mqtt, $password_mqtt))
	{
		exit(1);
	}

	$topics["topic/consommation"] = array("qos" =>0, "function" => "procmsg");
	$topics["topic/relais"] = array("qos" =>0, "function" => "procmsg");

	$mqtt->subscribe($topics, 0);

	while($mqtt->proc())
	{

	}

	$mqtt->close();

	function procmsg($topic, $value)
	{
		echo($topic. " ".$value."\n");

		//***********Connexion BASE DE DONNEE***************** */
		$servername = "localhost";
		$username_db = "snir";
		$password_db = "snir";
		$dbname = "EAU";


		$conn = new mysqli($servername, $username_db, $password_db, $dbname);

		if($conn->connect_error)
		{
			die("Echec de la connexion : " .$conn->connect_error);
		}

		if($topic == "topic/consommation")
		{
			

			
				$sql = "INSERT IGNORE INTO CONSOMMATION(consommation)
						VALUES('$value')";
			
		}
		else if ($topic == "topic/relais")
		{
			if($value == "Etat actuelle : Ouvert")
			{
				$sql = "INSERT IGNORE INTO RELAIS(etat)
					VALUES('ON')";
			}
			else if($value == "Etat actuelle : Fermer")
			{
				$sql = "INSERT IGNORE INTO RELAIS(etat)
					VALUES('OFF')";
			}
			
		}

		if($conn->query($sql)==TRUE)
		{
			echo"Nouvelle enregistrement réussi \n";
		}
		else
		{
			echo"Erreur : ".$sql."<br>".$conn->error;
		}
	}	

?>