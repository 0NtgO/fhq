<?
$curdir = dirname(__FILE__);

include_once "$curdir/fhq_class_security.php";

function echo_shortpage($page)
{	
	echo '
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title> Free-Hack-Quest </title>
		<link rel="stylesheet" type="text/css" href="styles/site.css" />
	';

$page->echo_head();

/*echo '
		<link rel="stylesheet" type="text/css" href="styles/body.css" />
	</head>
	<body class="main">
		<center>
			<table width="100%" height="100%">
				<tr>
					<td align="center" valign="middle">
						<table>				
							<tr>
								<td><center><br>
								<img src="images/mainlogo.png"/></center></td>
								<td > <font size=5>free-hack-quest:</font><br>'.$page->title().'
								<center>
									';
				$page->echo_content();
				echo '
								</center>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
		';
*/

echo '
		<link rel="stylesheet" type="text/css" href="styles/body.css" />
	</head>
	<body class="main">
		<center>
			<table width="100%" height="100%">
				<tr>
					<td align="center" valign="middle">
						<table>				
							<tr>
								<td><center><br>
								<img src="images/mainlogo.png"/></center></td>
							</tr>
							<tr>
								<td > 
								<center>
									<font size=5>free-hack-quest</font><br>'.$page->title().'
									';
				$page->echo_content();
				echo '
								</center>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
		';

$page->echo_onBodyEnd();		
echo '
	</body>
</html>';

};

?>
