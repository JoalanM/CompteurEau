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
						$sujet = "Consommation détectée";
						$message .= "Bonjour, \n";
						$message.= "\n\n";
						$message.= "NOus avons constatée une consommation de $consommation litres en votre abscence.\n";
						$message.= "Nous vous conseillons de fermée votre électrovanne si vous estimez que cette consommation est non justifier.\n";
						$message.= "Si votre consommation dépasse 25 litres en votre abscence le système fermera automatiquement l'électrovanne afin d'éviter une inondation ou une surconsommation non justifier.\n";
						$headers = "From : esp8266projet@gmail.com";
						mail($destinataire, $sujet, $message, $headers); 
					} 
					else if($consommation>20)
					{
						
						$postdata = http_build_query(
							array(
								'etat' => 'OFF',

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