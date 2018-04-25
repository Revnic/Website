<?PHP
include("db/connect.php");
session_start();

If(!empty($_SESSION['idaccount']) && !empty($_SESSION['idproduct']))
{
	$sql_select = "SELECT * FROM `favourite` 
	WHERE `account_idaccount`= '".$_SESSION['idaccount']."'
	AND `product_idproduct` = '".$_SESSION['idproduct']."' ";
	$result = $conn->query($sql_select);
	
	if($result->num_rows > 0)
	{
		//delete
		$sql_delete = "DELETE FROM `favourite` 
		WHERE `account_idaccount`= '".$_SESSION['idaccount']."'
		AND `product_idproduct` = '".$_SESSION['idproduct']."' ";
		$conn->query($sql_delete);
		
		echo '<img class="fav_icon" id="fav_icon" title="Warde den bo favorito" onclick="favProduct()" src="image/star_gray.png">';
	}
	else
	{
		//insert
		$sql_insert = "INSERT INTO `favourite`(`account_idaccount`, `product_idproduct`) 
		VALUES  ('".$_SESSION['idaccount']."','".$_SESSION['idproduct']."') ";
		$conn->query($sql_insert);
		
		echo '<img class="fav_icon" id="fav_icon" title="Warda den bo favorito" onclick="favProduct()" src="image/star_yellow.png">';
		
	}

}
else
{
	echo '<img class="fav_icon" id="fav_icon" title="Warde den bo favorito" onclick="favProduct()" src="image/star_gray.png">';
}

?>

