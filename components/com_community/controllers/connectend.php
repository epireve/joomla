<html>
	<head>
		<script src="../assets/cookies-1.0.js" type="text/javascript"></script>
		<script type="text/javascript">
		// Delete all numeric cookies from facebook that is causing the stupid
		// "Illegal variable _files ..." error
		var myCookies = getCookies();
		for (cook in myCookies)
		{
			if (isNumber(cook) ){
				eraseCookie(cook);
			}
		}
		
		// all cleared? redirect to the correct one
		var url = getParameterByName('redirect_to');
		window.location = url
		</script>
	</head>
	<body>
	</body>
</html>