<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
	
</head>
<body>

	<div id="vacances">
		<?php
			
			//importation classes phpMQTT
			require("classes/phpMQTT.php");
			//**************************** */

			//Informations de connexion au serveur Mosquitto
			$server = "192.168.5.74";
			$port = 1883;
			$username_mqtt = "esp8266";
			$password_mqtt = "esp8266";
			$client_id = "PhpMqtt";
			//************************* */
			
			//***********Information de connexion BASE DE DONNEE***************** */
			$servername = "localhost";
			$username_db = "snir";
			$password_db = "snir";
			$dbname = "EAU";
			
			// Création de la connexion
			$conn = new mysqli($servername, $username_db, $password_db, $dbname);
			// Verification de la connexion
			if ($conn->connect_error) 
			{
				die("Echec de connexion : " . $conn->connect_error);
			}
			
			//************************************************************************** */
			//************************************************************************** */
			
			$sql = "SELECT etat FROM ETAT ORDER BY ID DESC LIMIT 1";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) 
			{
				// output data of each row
				while($row = $result->fetch_assoc()) 
				{	
					$reponse = $row["etat"];
					echo $reponse;
					if($reponse == "ON")
					{
						$message = "1";
						$mqtt = new bluerhinos\phpMQTT($server, $port, $client_id);
						
						if ($mqtt->connect(true,NULL,$username_mqtt,$password_mqtt)) 	
						{
							$mqtt->publish("topic/relais",$message);
							$mqtt->close();
						}
						else
						{
							echo "Echec ou expiration du délai";
						}
					}
					else if($reponse == "OFF")
					{
						$message = "0";
						$mqtt = new bluerhinos\phpMQTT($server, $port, $client_id);
							
						if ($mqtt->connect(true,NULL,$username_mqtt,$password_mqtt)) 
						{
							$mqtt->publish("topic/relais",$message);
							$mqtt->close();
						}
						else
						{
							echo "Echec ou expiration du délai";
						}
					}	
				}

			} 
			else 
			{
				echo "0 résultat";
			}
			$conn->close();
	
			//************************************************************************** */
			//************************************************************************** */


		?>

	</div>	
    

	<script>
		setInterval('load_presence()', 500);
		function load_presence()
		{
			$('#vacances').load('ScriptRelais2.php');
		}
	</script>




</body>
</html>


