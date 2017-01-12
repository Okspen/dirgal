<h1>Oh my God!</h1>
<h3>An error occured!</h3>

<?php 
	switch($e->getMessage())
	{
		case '403':
			echo "<h4>I can't access the folder. Maybe it doesn't exist or the access is denied.</h4>";
			break;
	}
?>