<?php

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
			
			//***************************Récupérer derniere valeure ************************************
			$sql = "SELECT presence, date FROM PRESENCE ORDER BY ID DESC LIMIT 1";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) 
			{
				// output data of each row
				while($row = $result->fetch_assoc()) 
				{
					$reponse = $row["presence"];
					$date_depart = $row["date"];

					$date_depart = str_replace(" ", "", $date_depart);
					$date_depart = str_replace("-", "", $date_depart);
					$date_depart = str_replace(":", "", $date_depart);
					
					echo $date_depart;
					return $date_depart;

					
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
			


		?>