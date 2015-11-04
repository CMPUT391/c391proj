<html>
    <body>
	<?php
		session_start();
		session_unset();
		session_destroy();
		session_commit();
		header('Location: LoginModule.html');
	?>
    </body>
</html>
