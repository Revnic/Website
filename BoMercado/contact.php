<?PHP
include_once ("includes/class.TemplatePower.inc.php");
 
$content = new TemplatePower("./html/contact.htm");
$content->prepare();


$content->assign("hide","hidden");
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$email = $_POST["email"];
	$title = $_POST["title"];
	$text = $_POST["mensage"];
	
	if(empty($email)|| empty($title)|| empty($text))
	{
		$content->newBlock("warning");
	}
	else
	{
		$to = "ironnic26@gmail.com";
		$subject = $title;
		$txt = $title;
		$headers = "From: ".$email ."." . "\r\n";

		mail($to,$subject,$txt,$headers);
		$content->assign("hide","");
		
		
	}
}

?>