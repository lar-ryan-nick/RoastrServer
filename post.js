//var EXIF = require('exif');
var imageData = "";
var width = 300;
var height = 300;
var image = new Image();
var ratio = 0;
function loadImage(input)
{
	if (input.files && input.files[0])
	{
		var reader = new FileReader();
		reader.onloadend = function()
		{
			image.src = reader.result;
			imageData = "";
			var exif = EXIF.readFromBinaryFile(base64ToArrayBuffer(reader.result));
			image.onload = function(){
				console.log(image.width + " " + image.height);
				ratio = image.height / image.width;
				console.log(exif.Orientation);
				var canvas = document.createElement("canvas");
				if (exif.Orientation > 4)
				{
					ratio = 1 / ratio;
				}
				var canWidth = canvas.width;
				var canHeight = canvas.height;
				if (ratio > 1)
				{
					canvas.width = 2500 / ratio;
					canvas.height = 2500;
					if (exif.Orientation > 4)
					{
						var canWidth = canvas.height;
						var canHeight = canvas.width;
					}
				}
				else if (ratio < 1)
				{
					canvas.height = 2500 * ratio;
					canvas.width = 2500;
				}
				var context = canvas.getContext("2d");
				context.clearRect(0, 0, canvas.width, canvas.height);
				context.setTransform(1, 0, 0, 1, 0, 0);
				switch (exif.Orientation)
				{
					case 2:
						context.translate(canWidth, 0);
						context.scale(-1,1);
						break;
					case 3:
						context.translate(canWidth,canHeight);
						context.rotate(Math.PI);
						break;
					case 4:
						context.translate(0,canHeight);
						context.scale(1,-1);
						break;
					case 5:
						context.rotate(0.5 * Math.PI);
						context.scale(1,-1);
						break;
					case 6:
						context.rotate(.5 * Math.PI);
						context.translate(0, -canHeight);
						break;
					case 7:
						context.rotate(0.5 * Math.PI);
						context.translate(canWidth,-canHeight);
						context.scale(-1,1);
						break;
					case 8:
						context.rotate(-0.5 * Math.PI);
						context.translate(-canWidth,0);
						break;
					default:
						break;
				}
				if (exif.Orientation > 4)
				{
					context.drawImage(image, 0, 0, canvas.height, canvas.width);
				}
				else
				{
					context.drawImage(image, 0, 0, canvas.width, canvas.height);
				}
				image.onload = function(){
					canvas = document.getElementById("canvas");
					context = canvas.getContext("2d");
					if (ratio > 1)
					{
						canvas.width = 500 / ratio;
						canvas.height = 500;
					}
					else if (ratio < 1)
					{
						canvas.height = 500 * ratio;
						canvas.width = 500;
					}
					context.clearRect(0, 0, canvas.width, canvas.height);
					context.drawImage(image, 0, 0, canvas.width, canvas.height);
					var preview = document.getElementById("preview");
					if (!preview)
					{
						preview = document.createElement("canvas");
						preview.id = "preview";
						if (ratio > 1)
						{
							preview.width = width / ratio;
							preview.height = height;
						}
						else
						{
							preview.height = height * ratio;
							preview.width = width;
						}
						preview.setAttribute("style", "background-color:#000000;left:0;position:absolute;top:" + canvas.offsetTop + "px;");
						var poster = document.getElementById("poster");
						poster.prepend(preview);
					}
					else
					{
						if (ratio > 1)
						{
							preview.width = width / ratio;
							preview.height = height;
						}
						else
						{
							preview.height = height * ratio;
							preview.width = width;
						}
						preview.setAttribute("style", "background-color:#000000;left:0;position:absolute;top:" + canvas.offsetTop + "px;");
					}
					context = preview.getContext("2d");
					context.drawImage(image, 0, 0, preview.width, preview.height);
					imageData = preview.toDataURL("image/jpeg", 1.0).split(",")[1];
					imageData.replace(/[+]/g, "%2B");
					var cropper = document.getElementById("cropper");
					if (!cropper)
					{
						cropper = document.createElement("div");
						cropper.id = "cropper";
						cropper.draggable = "true";
						var leftOffset = 0;
						var topOffset = 0;
						cropper.setAttribute("style", "border:solid 2px #fffefe;position:absolute;width:100px;height:100px;max-height:" + (canvas.height - 2 * topOffset - 4) + ";max-width:" + (canvas.width - 2 * leftOffset - 4) + ";resize:both;overflow:auto;left:" + (canvas.offsetLeft + leftOffset) + ";top:" + (canvas.offsetTop + topOffset) + ";");
						var poster = document.getElementById("poster");
						poster.appendChild(cropper);
					}
					else
					{
						var leftOffset = 0;
						var topOffset = 0;
						cropper.setAttribute("style", "border:solid 2px #fffefe;position:absolute;width:100px;height:100px;max-height:" + (canvas.height - 2 * topOffset - 4) + ";max-width:" + (canvas.width - 2 * leftOffset - 4) + ";resize:both;overflow:auto;left:" + (canvas.offsetLeft + leftOffset) + ";top:" + (canvas.offsetTop + topOffset) + ";");
					}
				};
				image.src = canvas.toDataURL("image/png");
			};
		}
		reader.readAsDataURL(input.files[0]);
	}
}
function base64ToArrayBuffer (base64) {
    base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
    var binaryString = atob(base64);
    var len = binaryString.length;
    var bytes = new Uint8Array(len);
    for (var i = 0; i < len; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }
    return bytes.buffer;
}
var startx;
var starty;
document.addEventListener("mouseup", function(event)
{
	var cropper = document.getElementById("cropper");
	if (cropper)
	{
		takePreview(cropper);
	}
});
document.addEventListener("drag", function(event) 
{
	canvas = document.getElementById("canvas");
	var ratio = image.height / image.width;
	if (ratio < 1)
	{
		ratio = 1;
	}
	if (event.target.offsetLeft + event.target.offsetWidth + event.screenX - startx < canvas.offsetLeft + canvas.offsetWidth && event.target.offsetLeft + event.screenX - startx > canvas.offsetLeft)
	{
		event.target.style.left = event.target.offsetLeft + event.screenX - startx;
		event.target.style.maxWidth = canvas.offsetWidth + canvas.offsetLeft - event.target.offsetLeft - 4;
	}
	else if (event.screenX - startx > 0)
	{
		event.target.style.left = canvas.offsetLeft - event.target.offsetWidth + canvas.offsetWidth;
		event.target.style.maxWidth = canvas.offsetWidth + canvas.offsetLeft - event.target.offsetLeft - 4;
	}
	else if (event.screenX - startx < 0)
	{
		event.target.style.left = canvas.offsetLeft;
		event.target.style.maxWidth = canvas.offsetWidth + canvas.offsetLeft - event.target.offsetLeft - 4;
	}
	ratio = image.height / image.width;
	if (ratio > 1)
	{
		ratio = 1;
	}
	if (event.target.offsetTop + event.target.offsetHeight + event.screenY - starty < canvas.offsetTop + canvas.offsetHeight && event.target.offsetTop + event.screenY - starty > canvas.offsetTop)
	{
		event.target.style.top = event.target.offsetTop + event.screenY - starty;
		event.target.style.maxHeight = canvas.offsetHeight + canvas.offsetTop - event.target.offsetTop - 4;
	}
	else if (event.screenY - starty > 0)
	{
		event.target.style.top = canvas.offsetTop - event.target.offsetHeight + canvas.offsetHeight;
		event.target.style.maxHeight = canvas.offsetHeight + canvas.offsetTop - event.target.offsetTop - 4;
	}
	else if (event.screenY - starty < 0)
	{
		event.target.style.top = canvas.offsetTop;
		event.target.style.maxHeight = canvas.offsetHeight + canvas.offsetTop - event.target.offsetTop -4;
	}
	startx = event.screenX;
	starty = event.screenY;
}, false);
document.addEventListener("dragstart", function(event) 
{
	startx = event.screenX;
	starty = event.screenY;
	var image = new Image(0, 0);
	image.src = "images/transparent.png";
	image.style.visibility = "hidden";
	image.style.display = "none";
	event.dataTransfer.setDragImage(image, document.offsetWidth, document.offsetHeight);
}, false);
document.addEventListener("dragend", function(event) 
{
	event.preventDefault();
	takePreview();
}, false);
document.addEventListener("dragover", function(event) 
{
	event.preventDefault();
}, false);
function takePreview()
{
	var cropper = document.getElementById("cropper");
	var canvas = document.getElementById("canvas");
	var preview = document.getElementById("preview");
	var context = preview.getContext("2d");
	context.clearRect(0, 0, preview.width, preview.height);
	var ratio = image.height / image.width;
	if (ratio < 1)
	{
		ratio = 1;
	}
	var widthRatio = image.width / (canvas.width);
	ratio = image.height / image.width;
	if (ratio > 1)
	{
		ratio = 1;
	}
	var heightRatio = image.height / (canvas.height);
	var cropperRatio = cropper.offsetHeight / cropper.offsetWidth;
	if (cropperRatio > 1)
	{
		preview.width = width / cropperRatio;
		preview.height = height;
	}
	else
	{
		preview.height = height * cropperRatio;
		preview.width = width;
	}
	ratio = image.height / image.width;
	if (ratio > 1)
	{
		var left = (cropper.offsetLeft - canvas.offsetLeft) * widthRatio;
		if (left < 0)
		{
			left = 0;
		}
		console.log(cropper.offsetHeight);
		console.log((cropper.offsetHeight) * heightRatio);
		var cropWidth = (cropper.offsetWidth) * widthRatio;
		if (cropWidth > image.width - left)
		{
			cropWidth = image.width - left;
		}
		context.drawImage(image, left, (cropper.offsetTop - canvas.offsetTop) * heightRatio, cropWidth, (cropper.offsetHeight) * heightRatio, 0, 0, preview.width, preview.height);
	}
	else
	{
		var top = (cropper.offsetTop - canvas.offsetTop) * heightRatio;
		if (top < 0)
		{
			top = 0;
		}
		var cropHeight = (cropper.offsetHeight) * heightRatio;
		if (cropHeight > image.height - top)
		{
			cropHeight = image.height - top;
		}
		context.drawImage(image, (cropper.offsetLeft - canvas.offsetLeft) * widthRatio , top, (cropper.offsetWidth) * widthRatio, cropHeight, 0, 0, preview.width, preview.height);
	}
}
function post()
{
	var preview = document.getElementById("preview");
	var caption = document.getElementById("postCaption");
	if (caption.value || preview)
	{
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				console.log(this.responseText);
				window.location.replace("http://roastr.azurewebsites.net/mainpage.html");
			}
			else if (this.status == 404)
			{
				alert("Did not post properly. Please try again");
				postButton.disabled = false;
			}
		}
		//preview.width = width;
		//preview.height = height;
		imageData = preview.toDataURL("image/jpeg", 1.0).split(",")[1];
		imageData = imageData.replace(/[+]/g, "%2B");
		console.log(imageData);
		xhttp.open("POST", "http://roastr.azurewebsites.net/addPost.php", true);
		xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhttp.send("image=" + imageData + "&caption=\"" + caption.value + "\"&userID=" + localStorage.getItem("userID"));
		var postButton = document.getElementById("post");
		postButton.disabled = true;
	}
	else
	{
		alert("please write a caption or enter an image before posting");
	}
}
