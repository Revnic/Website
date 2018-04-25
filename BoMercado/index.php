<?php
include("header.php");


if(!empty($_GET['id']))
{

    $sql= "SELECT * FROM webpage WHERE id=".$_GET['id']."";
	$result = $conn->query($sql);
	
    if($result->num_rows > 0 )
    {
        $pagina = $result->fetch_array(MYSQLI_ASSOC);
        
        if($pagina['folder'] != "") 
        {
            $checklist= $pagina['folder']."/".$pagina['page'];
        }
        else
        {		
            $checklist = $pagina['page'];
        }
 
        if(file_exists($checklist)) 
        {
			include($checklist);
        }
        else
        {
			//page not found ERROR: 404
            header("Location: index.php?id=404");
        }
    }
    else
    {
		//error no data found in database
        header("Location: index.php?id=404");
    }
}
else
{
    include("content.php");
}           

include("footer.php");
?>