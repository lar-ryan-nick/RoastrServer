import React from 'react'
import EXIF from 'exif-js'

class UserInfo extends React.Component
{
	constructor(props)
	{
		super();
		this.state = {
			username:"",
			posts:0,
			likes:0,
			friends:0,
			profileFilename:"roastrtransparent.png"
		};
		this.props = props;
		this.refresh = this.refresh.bind(this);
		this.acceptFriendRequest = this.acceptFriendRequest.bind(this);
		this.addFriendRequest = this.addFriendRequest.bind(this);
		this.updateProfilePicture = this.updateProfilePicture.bind(this);
		this.removeFriendRequest = this.removeFriendRequest.bind(this);
		this.sendMessage = this.sendMessage.bind(this);
		this.refresh();
	}

	refresh()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				let userData = JSON.parse(xhttp.responseText);
				if (userData.profileFilename == "")
				{
					userData.profileFilename = "roastrtransparent.png";
				}
				this.setState(userData);
			}
		}.bind(this);
		xhttp.open("GET", "http://php-empty-site-p7x3h7.azurewebsites.net/getUserData.php?arg1=" + this.props.user + "&arg2=" + localStorage.getItem("userID"));
		xhttp.send();
	}

	acceptFriendRequest()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.refresh();
			}
		}.bind(this);
		xhttp.open("GET", "http://php-empty-site-p7x3h7.azurewebsites.net/acceptFriendRequestBetweenUsers.php?user1=" + this.props.user + "&user2=" + localStorage.getItem("userID"));
		xhttp.send();
	}

	addFriendRequest()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.refresh();
			}
		}.bind(this);
		xhttp.open("GET", "http://php-empty-site-p7x3h7.azurewebsites.net/addFriendRequest.php?user1=" + localStorage.getItem("userID") + "&user2=" + this.props.user);
		xhttp.send();
	}

	removeFriendRequest()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.refresh();
			}
		}.bind(this);
		xhttp.open("GET", "http://php-empty-site-p7x3h7.azurewebsites.net/removeFriendRequestBetweenUsers.php?user1=" + this.props.user + "&user2=" + localStorage.getItem("userID"));
		xhttp.send();
	}

	base64ToArrayBuffer(base64)
	{
    	base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
    	let binaryString = atob(base64);
    	let len = binaryString.length;
    	let bytes = new Uint8Array(len);
    	for (var i = 0; i < len; i++) {
    	    bytes[i] = binaryString.charCodeAt(i);
    	}
    	return bytes.buffer;
	}

	updateProfilePicture()
	{
		if (this.input.files && this.input.files[0])
		{
			let reader = new FileReader();
			reader.onloadend = function()
			{
				let image = new Image();
				image.src = reader.result;
				let ratio = image.height / image.width;
				let imageData = "";
				let exif = EXIF.readFromBinaryFile(this.base64ToArrayBuffer(reader.result));
				let canvas = document.createElement("canvas");
				canvas.width = 200;
				canvas.height = 200;
				let context = canvas.getContext("2d");
				context.clearRect(0, 0, canvas.width, canvas.height);
				switch (exif.Orientation)
				{
					case 2:
						context.translate(canvas.width, 0);
						context.scale(-1,1);
						break;
					case 3:
						context.translate(canvas.width,canvas.height);
						context.rotate(Math.PI);
						break;
					case 4:
						context.translate(0,canvas.height);
						context.scale(1,-1);
						break;
					case 5:
						context.rotate(0.5 * Math.PI);
						context.scale(1,-1);
						break;
					case 6:
						context.rotate(0.5 * Math.PI);
						context.translate(0,-canvas.height);
						break;
					case 7:
						context.rotate(0.5 * Math.PI);
						context.translate(canvas.width,-canvas.height);
						context.scale(-1,1);
						break;
					case 8:
						context.rotate(-0.5 * Math.PI);
						context.translate(-canvas.width,0);
						break;
				}
				if (ratio > 1)
				{
					context.drawImage(image, (canvas.width - canvas.height / ratio) / 2, 0, canvas.height / ratio, canvas.height);
				}
				else if (ratio < 1)
				{
					context.drawImage(image, 0, (canvas.height - canvas.width * ratio) / 2, canvas.width, canvas.width * ratio);
				}
				else
				{
					context.drawImage(image, 0, 0, canvas.width, canvas.height);
				}
				imageData = canvas.toDataURL("image/jpeg", 1.0).split(",")[1];
				//let temp = imageData;
				imageData = imageData.replace(/[+]/g, "%2B");
				//imageData = encodeURI(imageData);
				let xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function()
				{
					if (xhttp.readyState == 4 && xhttp.status == 200)
					{
						console.log(imageData);
						console.log(xhttp.responseText);
						this.refresh();
					}
				}.bind(this);
				xhttp.open("POST", "http://php-empty-site-p7x3h7.azurewebsites.net/setProfilePicture.php", true);
				xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xhttp.send("picture=" + imageData + "&userID=" + localStorage.getItem("userID"));
			}.bind(this);
			reader.readAsDataURL(this.input.files[0]);
		}
	}

	sendMessage()
	{
		this.props.messages.changeUser(0, this.props.user, this.state.profileFilename, this.state.username);
	}

	componentDidUpdate()
	{
		this.picture.style.height = this.picture.offsetWidth;
	}

	render()
	{
		if (this.props.user == localStorage.getItem("userID"))
		{
			return(
				<div style={{backgroundColor:"#2d3142"}}>
					<div style={{borderRadius:"50%", width:"20%", maxWidth:100, display:"inline-block", backgroundImage:"url('/profilePictures/" + this.state.profileFilename + "')", backgroundSize:"100%", height:"100px", backgroundRepeat:"no-repeat", verticalAlign:"top"}} ref={(input) => {this.picture = input;}}>
						<input style={{cursor:"pointer", display:"inline-block", opacity:0, borderRadius:"50%", width:"100%", height:"100%"}} type="file" accept="image/*" ref={(input) => {this.input = input;}} onChange={this.updateProfilePicture}/>
					</div>
					<div style={{display:"inline-block", width:"80%"}}>
						<table style={{verticalAlign:"top", padding:0, margin:0, width:"100%", display:"table", color:"#fcc229", fontSize:22, textAlign:"center"}}>
							<tbody style={{width:"100%"}}>
								<tr style={{width:"100%"}}>
									<th colSpan="3" style={{fontSize:30, textAlign:"center"}}>{this.state.username}</th>
								</tr>
								<tr style={{width:"100%"}}>
									<td>{this.state.posts}</td>
									<td style={{paddingLeft:10}}>{this.state.likes}</td>
									<td style={{paddingLeft:10}}>{this.state.friends}</td>
								</tr>
								<tr style={{width:"100%"}}>
									<td>Posts</td>
									<td style={{paddingLeft:10}}>Hates</td>
									<td style={{paddingLeft:10}}>Enemies</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			);
		}
		else if (this.state.friended == -1)
		{
			return(
				<div style={{backgroundColor:"#2d3142"}}>
					<img src={"/profilePictures/" + this.state.profileFilename} style={{width:"20%", borderRadius:"50%", maxWidth:100, display:"inline-block"}}/>
					<div style={{display:"inline-block", width:"80%"}}>
						<table style={{verticalAlign:"top", display:"table", width:"100%", padding:0, margin:0, color:"#fcc229", fontSize:22, textAlign:"center"}}>
							<tbody style={{width:"100%"}}>
								<tr style={{width:"100%"}}>
									<th colSpan="3" style={{fontSize:30, textAlign:"center"}}>{this.state.username}</th>
								</tr>
								<tr style={{width:"100%"}}>
									<td>{this.state.posts}</td>
									<td style={{paddingLeft:10}}>{this.state.likes}</td>
									<td style={{paddingLeft:10}}>{this.state.friends}</td>
								</tr>
								<tr style={{width:"100%"}}>
									<td>Posts</td>
									<td style={{paddingLeft:10}}>Hates</td>
									<td style={{paddingLeft:10}}>Enemies</td>
								</tr>
							</tbody>
						</table>
					</div>
					<button onClick={this.addFriendRequest}>Send Enemy Request</button>
				</div>
			);
		}
		else if (this.state.friended == 0)
		{
			return(
				<div style={{backgroundColor:"#2d3142"}}>
					<img src={"/profilePictures/" + this.state.profileFilename} style={{width:"20%", borderRadius:"50%", maxWidth:100, display:"inline-block"}}/>
					<div style={{display:"inline-block", width:"80%"}}>
						<table style={{verticalAlign:"top", display:"table", width:"100%", padding:0, margin:0, color:"#fcc229", fontSize:22, textAlign:"center"}}>
							<tbody style={{width:"100%"}}>
								<tr style={{width:"100%"}}>
									<th colSpan="3" style={{fontSize:30, textAlign:"center"}}>{this.state.username}</th>
								</tr>
								<tr style={{width:"100%"}}>
									<td>{this.state.posts}</td>
									<td style={{paddingLeft:10}}>{this.state.likes}</td>
									<td style={{paddingLeft:10}}>{this.state.friends}</td>
								</tr>
								<tr style={{width:"100%"}}>
									<td>Posts</td>
									<td style={{paddingLeft:10}}>Hates</td>
									<td style={{paddingLeft:10}}>Enemies</td>
								</tr>
							</tbody>
						</table>
					</div>
					<button onClick={this.removeFriendRequest}>Cancel Enemy Request</button>
				</div>
			);
		}
		else if (this.state.friended == 1)
		{
			return(
				<div style={{backgroundColor:"#2d3142"}}>
					<img src={"/profilePictures/" + this.state.profileFilename} style={{width:"20%", borderRadius:"50%", maxWidth:100, display:"inline-block"}}/>
					<div style={{display:"inline-block", width:"80%"}}>
						<table style={{verticalAlign:"top", display:"table", width:"100%", padding:0, margin:0, color:"#fcc229", fontSize:22, textAlign:"center"}}>
							<tbody style={{width:"100%"}}>
								<tr style={{width:"100%"}}>
									<th colSpan="3" style={{fontSize:30, textAlign:"center"}}>{this.state.username}</th>
								</tr>
								<tr style={{width:"100%"}}>
									<td>{this.state.posts}</td>
									<td style={{paddingLeft:10}}>{this.state.likes}</td>
									<td style={{paddingLeft:10}}>{this.state.friends}</td>
								</tr>
								<tr style={{width:"100%"}}>
									<td>Posts</td>
									<td style={{paddingLeft:10}}>Hates</td>
									<td style={{paddingLeft:10}}>Enemies</td>
								</tr>
							</tbody>
						</table>
					</div>
					<button onClick={this.acceptFriendRequest}>Accept Enemy Request</button>
					<button onClick={this.removeFriendRequest}>Remove Enemy Request</button>
				</div>
			);
		}
		else
		{
			return(
				<div style={{backgroundColor:"#2d3142"}}>
					<img src={"/profilePictures/" + this.state.profileFilename} style={{width:"20%", borderRadius:"50%", maxWidth:100, display:"inline-block"}}/>
					<div style={{display:"inline-block", width:"80%"}}>
						<table style={{verticalAlign:"top", display:"table", width:"100%", padding:0, margin:0, color:"#fcc229", fontSize:22, textAlign:"center"}}>
							<tbody style={{width:"100%"}}>
								<tr style={{width:"100%"}}>
									<th colSpan="3" style={{fontSize:30, textAlign:"center"}}>{this.state.username}</th>
								</tr>
								<tr style={{width:"100%"}}>
									<td>{this.state.posts}</td>
									<td style={{paddingLeft:10}}>{this.state.likes}</td>
									<td style={{paddingLeft:10}}>{this.state.friends}</td>
								</tr>
								<tr style={{width:"100%"}}>
									<td>Posts</td>
									<td style={{paddingLeft:10}}>Hates</td>
									<td style={{paddingLeft:10}}>Enemies</td>
								</tr>
							</tbody>
						</table>
					</div>
					<button onClick={this.sendMessage}>Send a Message</button>
					<button onClick={this.removeFriendRequest}>Unenemy</button>
				</div>
			);
		}
	}
}

export default UserInfo;
