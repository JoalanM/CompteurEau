<?php
			
            //importation classes phpMQTT
            require("classes/phpMQTT.php");
            //**************************** */
			require('presence02.php');
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
			//******************************************************************* */
			

			// Création de la connexion BD
			$conn = new mysqli($servername, $username_db, $password_db, $dbname);
			// Verification de la connexion
			if ($conn->connect_error) 
			{
				die("Echec de connexion : " . $conn->connect_error);
			}
			
			//************************************************************************** */
			//************************************************************************** */


			$date_depart2 = $date_depart;
			$presence= $reponse;

			if($presence=="non")
			{

			
				
				//***************************Récupérer derniere valeure ************************************
				$sql = "SELECT consommation FROM CONSOMMATION WHERE date>= $date_depart2 ";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) 
				{
					// output data of each row
					while($row = $result->fetch_assoc()) 
					{
						echo "<br>";
						echo $row["consommation"] ." " ;
						$consommation = $consommation + $row["consommation"];
						

						
					}
					echo "<br>" .$consommation ."<br>";
					if($consommation >= 10 && $consommation<20)
					{
						echo "ATTENTION FUITE EVENTUELLE ";
						$destinataire = "myrtil.joalan1@gmail.com";
						$sujet = "Consommation";
						$message = "consommation detectée en votre abs";
						$headers = "From : joalan.myrtil1@gmail.com";
						mail($destinataire, $sujet, $message, $headers); 
					} 
					elseif($consommation>20)
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
				else 
				{
					echo "0 résultat";
				}
				$conn->close();
				//************************************************************************************ */

				//************************************************************************** */
				//************************************************************************** */

			}
		?>