import React from 'react';

class Message extends React.Component
{
	constructor(props)
	{
		super();
	}

	render()
	{
		if (!this.props.data || this.props.data.user == 0)
		{
			return(
				<div></div>
			);
		}
		else if (this.props.data.user == localStorage.getItem("userID"))
		{
			return(
				<div style={{clear:"both", width:"30%", borderRadius:"5%", margin:5, backgroundColor:"#3abfbc", float:"right"}}>
					<p style={{padding:5, color:"#2d3142", fontSize:"1.2em", margin:0}}>{this.props.data.message}</p>
				</div>
			);
		}
		else
		{
			return(
				<div style={{clear:"both", width:"30%", borderRadius:"5%", margin:5, backgroundColor:"#fcc229"}}>
					<p style={{padding:5, color:"#2d3142", fontSize:"1.2em", margin:0}}>{this.props.data.message}</p>
				</div>
			);
		}
	}
}

class MessageViewer extends React.Component
{
	constructor(props)
	{
		super();
		this.handleScroll = this.handleScroll.bind(this);
		this.sendMessage = this.sendMessage.bind(this);
	}

	sendMessage()
	{
		this.props.sendMessage(this.textField.value);
		this.textField.value = "";
	}

	handleScroll()
	{
		if (this.messages.scrollTop < 20)
		{
			this.props.addMessages(20);
		}
	}

	componentDidUpdate()
	{
		//console.log(this.props.data.beginning);
		if (this.props.data.beginning)
		{
			this.messages.scrollTop = this.messages.scrollHeight;
		}
	}
	

	render()
	{
		let messages = [];
		for (let i = 0; i < this.props.data.messagesLoaded; i++)
		{
			messages.splice(0, 0, <Message key={i} data={this.props.data[i]}/>);
		}
		if (this.props.data.profilePicture == "")
		{
			return (
				<div style={{height:"100%"}}>
					<div style={{backgroundColor:"#fefeff", height:"10%"}}></div>
					<div ref={(input) => {this.messages = input;}} onScroll={this.handleScroll} style={{height:"85%", overflowY:"auto", backgroundColor:"#2d3142"}}>
						{messages}
					</div>
					<input ref={(input) => {this.textField = input;}} onKeyDown={function(event){if (event.keyCode == 13) this.sendMessage();}.bind(this)} style={{width:"80%", height:"5%", fontSize:"1.2em"}} type="text" placeholder="Write a message"/>
					<button style={{width:"20%", height:"5%", verticalAlign:"top", display:"inline-block"}} onClick={this.sendMessage}>Send</button>
				</div>
			);
		}
		else
		{
			return (
				<div style={{height:"100%"}}>
					<div style={{backgroundColor:"#fefeff", height:"10%"}}>
						<center>
							<img height="100%" style={{display:"inline-block", borderRadius:"50%"}} src={"profilePictures/" + this.props.data.profilePicture}/>
							<a href={"http://roastr.azurewebsites.net/profile.html?user=" + this.props.data.user} style={{paddingLeft:10, margin:0, textDecoration:"none", verticalAlign:"top", display:"inline-block", fontSize:"2em", color:"#000000"}}>{this.props.data.username}</a>
						</center>
					</div>
					<div ref={(input) => {this.messages = input;}} onScroll={this.handleScroll} style={{height:"85%", overflowY:"auto", backgroundColor:"#2d3142"}}>
						{messages}
					</div>
					<input ref={(input) => {this.textField = input;}} onKeyDown={function(event){if (event.keyCode == 13) this.sendMessage();}.bind(this)} style={{width:"80%", height:"5%", fontSize:"1.2em"}} type="text" placeholder="Write a message"/>
					<button style={{width:"20%", height:"5%", verticalAlign:"top", display:"inline-block"}} onClick={this.sendMessage}>Send</button>
				</div>
			);
		}
	}
}

export default MessageViewer;
