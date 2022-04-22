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
		

	</div>	
    

	<script>
		setInterval('load_presence()', 100);
		function load_presence()
		{
			$('#vacances').load('ScriptRelais2.php');
		}
	</script>




</body>
</html>


