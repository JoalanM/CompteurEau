<?php
			
            //importation classes phpMQTT
            require("../classes/phpMQTT.php");
            //**************************** */
			require('presence02.php');
            //Informations de connexion au serveur Mosquitto
            $server = "192.168.5.74";
            $port = 1883;
            $username_mqtt = "esp8266";
            $password_mqtt = "esp8266";
            $client_id = "PhpMqtt2";
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

					if($consommation >= 1 && $consommation<3)
					{
						$sql = "SELECT date FROM FUITE ORDER BY ID DESC LIMIT 1";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) 
						{
							// output data of each row
							while($row = $result->fetch_assoc()) 
							{
								$reponse01 = $row["date"];
								if($date_depart2 == $reponse01)
								{
									echo "\n mail déja envoyée ";
								}
								else
								{
									echo "\nATTENTION FUITE EVENTUELLE ";
									$destinataire = "myrtil.joalan1@gmail.com";
									$sujet = "Consommation détectée";
									$message .= "Bonjour, \n";
									$message.= "\n\n";
									$message.= "Nous avons constatée une consommation de $consommation litres en votre abscence.\n";
									$message.= "Nous vous conseillons de fermée votre électrovanne si vous estimez que cette consommation est non justifier.\n";
									$message.= "Si votre consommation dépasse 20 litres en votre abscence le système fermera automatiquement l'électrovanne afin d'éviter une inondation ou une surconsommation non justifier.\n";
									$message.= "\n\n\n\n";
									$message.= "Mail envoyée automatiquement depuis le système 'compteur d'eau connectée' veuillez ne pas répondre à ce mail. ";
									$headers.= "From : esp8266projet@gmail.com \n";
									$headers.= "Le ". date('d-m-y à h:i:s');
									mail($destinataire, $sujet, $message, $headers); 
									$sql = "INSERT IGNORE INTO FUITE (date) VALUES ($date_depart2)";
									if($conn -> query($sql)==TRUE)
									{
										echo "\n Nouvelle enregistrement réussi";
									}
								}
							
							}
						} 
						else 
						{
							echo "0 résultat";
						}
						$conn->close();
						
						

						
					} 
					else if($consommation>3)
					{
						$sql = "SELECT etat FROM RELAIS ORDER BY ID DESC LIMIT 1";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) 
						{
							// output data of each row
							while($row = $result->fetch_assoc()) 
							{
								$etat = $row["etat"];
								if($etat == "OFF")
								{
									$postdata = http_build_query(
										array(
											'etat' => 'ON',
			
										)
									);
									$opts = array('http' =>
										array(
											'method' => 'POST',
											'header' => 'Content-type: application/x-www-form-urlencoded',
											'content' => $postdata
										)
									);
									$context = stream_context_create($opts);
									$result = file_get_contents('http://192.168.5.74/php-api/index.php', false, $context);
									echo $result;
			
								}
							}
						} 
						else 
						{
							echo "0 résultat";
						}
						$conn->close();
						
						
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