<?
include_once "engine/fhq.php";

// ---------------------------------------------------------------------

class fhq_registration
{
	function title()
	{
		return 'Registration<br><font size=2><a class="btn btn-small btn-info" href="index.php">&larr; go to main page</a></font>';
	}

	function echo_head()
	{
		echo '';
	}
	
	function echo_onBodyEnd() {
		echo '';
	}
	
	function echo_content()
	{
		echo '
			<form method="POST" action="">
				<table cellspacing=10px cellpadding=10px>
					<tr>
						<td>Write your e-mail:</td>
						<td><input name="email" id="user_email" value="" type="text"></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<img src="captcha.php" id="captcha-image"/><br>
							<a class="btn" href="javascript:void(0);" onclick="document.getElementById(\'captcha-image\').src = \'captcha.php?rid=\' + Math.random();">Refresh Capcha</a>
							<br>
							
						</td>
					</tr>
					<tr>
						<td>Captcha</td>
						<td><input name="captcha" id="user_captcha" value="" type="text"></td>
					</tr>
					<tr>
						<td colspan = "2">
							
							<center>
								<br>
<script>
function sendQuery(str)
{
  document.getElementById("answer").innerHTML="<img width=100px src=\'images/sending.gif\'>";

  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp=new XMLHttpRequest();
  };  
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	  document.getElementById("answer").innerHTML=xmlhttp.responseText;
	}
  }
  var email = document.getElementById(\'user_email\').value;
  var captcha = document.getElementById(\'user_captcha\').value;
  xmlhttp.open("GET","registration.php?email="+email + "&captcha=" + captcha,true);
  xmlhttp.send();
}
</script>
									<a class="btn btn-small btn-info" href="javascript:void(0);" onclick="sendQuery();">Send query</a>
									<br><br>
									
								</center>
							</td>
						</tr>
					</table>
					</form>
					<center>
						<br>
						<div id="answer"></div>
					</center>';
	}
};

// ---------------------------------------------------------------------

class fhq_foractivate
{
	function title()
	{
		return 'Activate account<br><font size=2><a href="index.php">&larr; go to main page</a></font>';
	}
	
	function echo_head()
	{
		echo '';
	}
	
	function echo_onBodyEnd() {
		echo '';
	}
	
	function echo_content()
	{
		// registration.php
		// return ' blabla ';
		
		$foractivate = $_GET['foractivate'];
		include "config/config.php";
		
		$db = new fhq_database();
		$security = new fhq_security();
		$query = "select * from user where password = 'notactivated$foractivate';";
		$result = $db->query($query);
		if( mysql_num_rows( $result ) == 1 )
		{
			$iduser = mysql_result($result, 0, 'iduser');
			$username = mysql_result($result, 0, 'username');
			$nickname = mysql_result($result, 0, 'nick');
			$email = base64_decode($username);
		
			$password = substr(md5(rand().rand()), 0, 7);
			
			$password_hash = $security->tokenByData( array($password, $username, strtoupper($email)));
			
			// echo "e-mail: $email<br> password: $password";
			
			$query2 = "update user set password = '$password_hash', score = 0 where username = '$username';";
			$db->query($query2);

			$subject = "Your account was activated";
			echo 'Please, wait... ';
			$message = "
Thank you for registration on Free-Hack-Quest!
Your login: ".strtolower($email)."
Your password: $password
Your nickname: $nickname
Now you could begin playing in this game, it here: ".$config['httpname']."
";

			$mail = new fhq_mail();
			$error = "";
			if( $mail->send($email, $subject, $message, $error) )
				echo 'Your account was activated.<br>Information for logon was sended to your email.';
			else
				echo '<font color=#ff0000>Problem with sending email. '.$error.'</font>';
		
			// create ssh user
			if(isset($config['nfs_share']))
			{
				$answer1 = md5(rand().rand());
				$answer2 = md5(rand().rand()."hhhhhh");
				$ssh_password = substr(md5(rand().rand()), 0, 7);
				$create_user_script = "
useradd -g hackers --no-user-group --home /home/$nickname -m --shell /bin/bash --password $ssh_password $nickname\n
echo $nickname:$ssh_password | chpasswd
mkdir /home/$nickname/SecureShellFirst && cd /home/$nickname/SecureShellFirst
echo \"answer:$answer1\" > answer_here
chown root:hackers answer_here
chmod o+r answer_here
";

$create_user_script .= "
mkdir /home/$nickname/SecureShellSecond
cd /home/$nickname/SecureShellSecond
echo \"answer:$answer2\" > answer_here
zip archiv.zip answer_here && rm answer_here
zip archiv2.zip archiv.zip && rm archiv.zip && mv archiv2.zip archiv.zip
";

for($i = 0; $i < 100; $i++)
	$create_user_script .= "zip archiv2.zip archiv.zip && rm archiv.zip && mv archiv2.zip archiv.zip\n";

$create_user_script .= "chown root:hackers archiv.zip && chmod o+r archiv.zip\n";

				$nfs_share = $config['nfs_share'];
				file_put_contents($nfs_share."/new_user_$nickname.sh", $create_user_script);
				
				// create personal quests
				{ // first
					$quest = new fhq_quest();
					$quest->setEmptyAll();
					$quest->setQuestName( "Secure Shell First" );
					$quest->setShortText( "Try using SSH on our's server" );
					$quest->setFullText( "Server: ssh.keva.su
Login: $nickname
Password: $ssh_password
Directory: SecureShellFirst
");
					$quest->setScore( 20 );
					$quest->setMinScore( 5 );
					$quest->setSubject( "Network:SSH" );
					$quest->setAnswer( $answer1 );
					$quest->setForPerson( $iduser );
					$quest->insert();
				}
				
				
				{ // second
					$quest = new fhq_quest();
					$quest->setEmptyAll();
					$quest->setQuestName( "Secure Shell Second" );
					$quest->setShortText( "Try using SSH on our's server" );
					$quest->setFullText( "server: ssh.keva.su
login: $nickname
password: $ssh_password
Directory: SecureShellSecond
");

					$quest->setScore( 100 );
					$quest->setMinScore( 50 );
					$quest->setSubject( "Network:SSH" );
					$quest->setAnswer( $answer2 );
					$quest->setForPerson( $iduser );
					$quest->insert();
				}
								
			};
		}
		else
		{
			echo 'It is not exists or already activated.';
		}
	}
};

// ---------------------------------------------------------------------

if(isset($_GET['email']) && isset($_GET['captcha']))
{
		$captcha = $_GET['captcha'];		
		$rem_captcha = $_SESSION['captcha_reg'];
		if( strtoupper($captcha) != strtoupper($rem_captcha) )
		{
			echo '<font color=#ff0000>Captcha is not correct,<br> please "Refresh captcha" and try again</font>';
			exit;
		};
		$email = $_GET['email'];

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "<font color=#ff0000>Invalid e-mail address.</font>";
			exit;
		}
		
		$username = base64_encode(strtoupper($email));	
		$db = new fhq_database();
		$query = "select * from user where username = '$username';";
		$result = $db->query($query);
		if( $db->count( $result ) >= 1 )
		{
			echo '<font color=#ff0000>This e-mail was already registered.</font>';
			mysql_free_result($result);
			exit;
		};
		
		$notactivated = md5(rand().rand());
		$nickname = "hacker-".substr(md5(rand().rand()), 0, 7);
		$query = "INSERT user( username, password, nick, role, score ) VALUES ('$username','notactivated$notactivated','$nickname','user', 0);";
		$result = $db->query($query);

		if($result == '1')
		{
			
			$subject = "Activation your account on FreeHackQuest.";
			
			$message = "
Registration:

If you was not tryed register on ".$config['httpname'].", just ignore this mail.

For activate your account, please visit this page:
".$config['httpname']."registration.php?foractivate=$notactivated
";
			$mail = new fhq_mail();
			$error = "";
			if( $mail->send($email, $subject, $message, $error) )
				echo "Check your your e-mail (also check spam).";
			else
				echo '<font color=#ff0000>Problem with sending email. '.$error.'</font>';
			exit;
		};
		echo "<font color=#ff0000>Registration is denied.</font>";
		exit;
};

// ---------------------------------------------------------------------

if(isset($_GET['foractivate']))
{
	echo_shortpage(new fhq_foractivate());
	exit;
};

// ---------------------------------------------------------------------

echo_shortpage(new fhq_registration());

exit;
