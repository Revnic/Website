<?PHP
function priceConverter($amount)
{
	
	$int = explode(".", $amount);
	$sInt = str_split($int[0]);
	$lInt = strlen($int[0]);
	$posInt = "";
	$x = 0;
	for($i = $lInt; $i > -1; $i--)
	{
		if($i == 4 || $i == 7 || $i == 10)
		{
			
			$posInt .= $sInt[$x].".";
		}
		else
		{
			$posInt .= $sInt[$x];
		}
		
		$x++;
	}

	if(!empty($int[1]))
	{
		if(strlen($int[1])== 1)
		{
			$money = $posInt.",".$int[1]."0";
		}
		else
		{
			$money = $posInt.",".$int[1];
		}
	}
	else
	{
		$money = $posInt.",00";
	}
	
	return $money;
}
?>