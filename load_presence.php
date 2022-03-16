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



			
			
			$sql = "SELECT vac FROM VACANCES ORDER BY ID DESC LIMIT 1";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) 
			{
				// output data of each row
				while($row = $result->fetch_assoc()) 
				{
						
					$reponse = $row["vac"];
					echo $reponse;
                    
					
				}
			} 
			else 
			{
				echo "0 résultat";
			}
			$conn->close();

			
			if($reponse == "non")
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
					else if($reponse == "oui")
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

			//************************************************************************** */
			//************************************************************************** */


		?>