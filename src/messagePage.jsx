import ConversationsPage from './conversationsPage.jsx';
import MessageViewer from './messageViewer.jsx';
import React from 'react';
import ReactDOM from 'react-dom';
import io from 'socket.io-client';

class MessagePage extends React.Component
{
	constructor()
	{
		super();
		this.state = {
			conversations:{
				conversationsLoaded:0
			},
			messages:{
				user:0,
				profilePicture:"",
				username:"",
				messagesLoaded:0,
				beginning:true
			}
		}
		this.numConversations = 0;
		this.numMessages = 0
		this.loadConversation = this.loadConversation.bind(this);
		this.getNumConversations = this.getNumConversations.bind(this);
		this.addConversations = this.addConversations.bind(this);
		this.getNumMessages = this.getNumMessages.bind(this);
		this.loadMessage = this.loadMessage.bind(this);
		this.addMessages = this.addMessages.bind(this);
		this.refresh = this.refresh.bind(this);
		this.sendMessage = this.sendMessage.bind(this);
		this.socket = io("http://roastr.azurewebsites.net:8000");
		this.socket.emit("addUser", {user:localStorage.getItem("userID")});
		this.socket.on("message", function(data){
			let newState = this.state;
			newState.messages.beginning = true;
			this.setState(newState);
			this.refresh();
		}.bind(this));
		this.getNumConversations(function(){
			this.addConversations(10);
		}.bind(this));
	}

	loadConversation(index)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				let newState = this.state;
				let convData = JSON.parse(xhttp.responseText);
				newState.conversations[index] = convData;
				if (index == 1 && this.state.messages.user == 0)
				{
					newState.messages.user = convData.user;
					newState.messages.profilePicture = convData.profileFilename;
					newState.messages.username = convData.username;				
					this.setState(newState);
					this.getNumMessages(function(){
						this.addMessages(20);
					}.bind(this));
				}
				else
				{
					this.setState(newState);
				}
			}
		}.bind(this)
		xhttp.open("GET", "http://roastr.azurewebsites.net/getConversationData.php?arg1=" + localStorage.getItem("userID") + "&arg2=" + index);
		xhttp.send();
	}

	getNumConversations(cb)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.numConversations = parseInt(xhttp.responseText);
				cb();
			}
		}.bind(this)
		xhttp.open("GET", "http://roastr.azurewebsites.net/getNumConversations.php?arg1=" + localStorage.getItem("userID"));
		xhttp.send();
	}

	addConversations(num)
	{
		let newLoaded = this.state.conversations.conversationsLoaded + num;
		if (newLoaded > this.numConversations)
		{
			newLoaded = this.numConversations;
		}
		for (let i = 1; i <= newLoaded; i++)
		{
			this.loadConversation(this.state.conversations.conversationsLoaded + i);
		}
		let newState = this.state;
		newState.conversations.conversationsLoaded = newLoaded;
		this.setState(newState);
	}

	getNumMessages(cb)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.numMessages = parseInt(xhttp.responseText);
				cb();
			}
		}.bind(this)
		xhttp.open("GET", "http://roastr.azurewebsites.net/getNumMessages.php?user1=" + localStorage.getItem("userID") + "&user2=" + this.state.messages.user);
		xhttp.send();
	}

	loadMessage(index)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				let newState = this.state;
				newState.messages[this.numMessages - index] = JSON.parse(xhttp.responseText);
				this.setState(newState);
			}
		}.bind(this);
		xhttp.open("GET", "http://roastr.azurewebsites.net/getMessageData.php?user1=" + localStorage.getItem("userID") + "&user2=" + this.state.messages.user + "&index=" + index);
		xhttp.send();
	}

	addMessages(num)
	{
		let newLoaded = this.state.messages.messagesLoaded + num;
		if (newLoaded > this.numMessages)
		{
			newLoaded = this.numMessages;
		}
		for (let i = this.state.messages.messagesLoaded; i < newLoaded; i++)
		{
			this.loadMessage(this.numMessages - i);
		}
		let newState = this.state;
		if (this.state.messages.messagesLoaded > 0)
		{
			newState.messages.beginning = false;
		}
		newState.messages.messagesLoaded = newLoaded;
		this.setState(newState);
	}

	sendMessage(text)
	{
		if (text == "")
		{
			alert("Please enter a message before you hit send");
		}
		else if (this.state.messages.user < 1)
		{
			alert("Please select someone to send your message to");
		}
		else
		{
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function(){
				if (xhttp.readyState == 4 && xhttp.status == 200)
				{
					this.socket.emit("sentMessage", {user:this.state.messages.user, toUser:localStorage.getItem("userID")});
					this.refresh();
				}
			}.bind(this)
			xhttp.open("GET", "http://roastr.azurewebsites.net/addMessage.php?message=" + text + "&sender=" + localStorage.getItem("userID") + "&receiver=" + this.state.messages.user);
			xhttp.send();
		}
	}

	refresh()
	{
		let prevNumMessages = this.numMessages;
		this.getNumMessages(function(){
			if (this.numMessages > prevNumMessages)
			{
				let newState = this.state;
				let messagesLoaded = newState.messages.messagesLoaded;
				//newState.messages.messagesLoaded = 0;
				for (let i = messagesLoaded - 1; i >= 0; i--)
				{
					newState.messages[i + this.numMessages - prevNumMessages] = newState.messages[i];
				}
				newState.messages.messagesLoaded = messagesLoaded + this.numMessages - prevNumMessages;
				this.setState(newState);
				for (let i = 0; i < this.numMessages - prevNumMessages; i++)
				{
					this.loadMessage(this.numMessages - i);
				}
			}
		}.bind(this));
		let prevNumConversations = this.numConversations;
		this.getNumConversations(function(){
			let conversationsToLoad = this.state.conversations.conversationsLoaded + this.numConversations - prevNumConversations;
			for (let i = 1; i <= conversationsToLoad; i++)
			{
				this.loadConversation(i);
			}
		}.bind(this));
	}

	changeUser(index, newUser, profilePic, newUsername)
	{
		if (newUser != this.state.messages.user)
		{
			let newState = this.state;
			newState.messages = {user:newUser, profilePicture:profilePic, username:newUsername, messagesLoaded:0, beginning:true};
			this.setState(newState);
			this.numMessages = 0;
			this.getNumMessages(function(){
				this.addMessages(20);
			}.bind(this));
		}
	}

	render()
	{
		return(
			<div style={{clear:"both"}}>
				<div style={{float:"left", height:"100%", overflowY:"auto", width:"20%", backgroundColor:"#3d4553"}}>
					<ConversationsPage data={this.state.conversations} onClick={this.changeUser.bind(this)}/>
				</div>
				<div style={{float:"right", height:"100%", width:"80%"}}>
					<MessageViewer data={this.state.messages} sendMessage={this.sendMessage} addMessages={this.addMessages}/>
				</div>
			</div>
		);
	}
}

export default MessagePage;
