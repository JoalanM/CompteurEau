<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
	
</head>
<body>

	<div id="presence">
		

	</div>	

	<div id ="consommation">
	
	
	</div>
    

	<script>
		setInterval('presence()', 1000);
		function presence()
		{
			$('#presence').load('presence02.php');
	
		}


		setInterval('consommation()', 1000);
		function presence()
		{
			$('#consommation').load('fuite.php');
			
		}
	</script>



</body>
</html>


