function loadXMLDoc() 
{
	var xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() 
	{
    	if (this.readyState == 4 && this.status == 200)
		{
      		if (this.responseText == "Password is correct")
			{
				loadUserID(function()
				{
					window.location.replace("http://roastr.azurewebsites.net/mainpage.html");
				});
			}
			else
			{
				var errorMessage = document.getElementById("errorMessage");
				errorMessage.innerHTML = this.responseText;
			}
    	}
  	};
  	xhttp.open("GET", "http://roastr.azurewebsites.net/checkPassword.php?arg1='" + document.getElementById("user").value + "'&arg2=" + document.getElementById("pass").value, true);
  	xhttp.send();
}	
function loadUserID(cb)
{
	var xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() 
	{
    	if (this.readyState == 4 && this.status == 200)
		{
			localStorage.setItem("userID", this.responseText);
			cb();
    	}
  	};
  	xhttp.open("GET", "http://roastr.azurewebsites.net/getIDForUser.php?arg1='" + document.getElementById("user").value + "'", true);
  	xhttp.send();
}
