function favProduct() 
{
	
	if (window.XMLHttpRequest) 
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} 
	else 
	{  // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  document.getElementById("fav_star").innerHTML=xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("GET","getDataFav.php",true);
	xmlhttp.send();
}

function bidAuction() 
{
	
	if (window.XMLHttpRequest) 
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} 
	else 
	{  // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  document.getElementById("auction_bidder").innerHTML=xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("GET","product_view.php",true);
	xmlhttp.send();
}		