import React from 'react';
import ReactDOM from 'react-dom';
import PostView from './postView.jsx';
import UserInfo from './userInfo.jsx';
import MessagePage from './messagePage.jsx';

class ProfilePage extends React.Component
{
	constructor(props)
	{
		super();
		this.state = {
			postsAdded:0,
		}
		this.props = props;
		this.numPosts = 0;
		this.getNumPosts = this.getNumPosts.bind(this);
		this.addPosts = this.addPosts.bind(this);
		this.getNumPosts(function() {
			this.addPosts(4);
		}.bind(this));
	}

	getNumPosts(cb)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
		{
			if (xhttp.readyState == 4 && xhttp.status == 200)
			{
				this.numPosts = parseInt(xhttp.responseText);
				if (cb)
				{
					cb();
				}
			}
		}.bind(this)
		xhttp.open("GET", "https://roastr2.herokuapp.com/getUserPostCount.php?arg1=" + this.props.user, true);
		xhttp.send();
	}

	addPosts(num)
	{
		if (this.state.postsAdded + num > this.numPosts)
		{
			num = this.numPosts - this.state.postsAdded;
		}
		this.setState({postsAdded:(this.state.postsAdded + num)});
	}

	render()
	{
		let tags = [];
		for (let i = 0; i < this.state.postsAdded; i++)
		{
			tags.push(<PostView key={i} user={this.props.user} post={this.numPosts - i}/>);
		}
		return (
			<div>
				{tags}
			</div>
		);
	}
}

let user = 0;
if (location.search.split("=")[0].substr(1) == "user")
{
	user = location.search.split("=")[1];
	ReactDOM.render(<ProfilePage ref={(input) => {window.newsFeed = input}} user={user}/>, document.getElementById("profilePage"));
	ReactDOM.render(<MessagePage ref={(input) => {window.messagePage = input;}}/>, document.getElementById("messages"));
	ReactDOM.render(<UserInfo messages={window.messagePage} user={user}/>, document.getElementById("userInfo"));
	document.body.onscroll = function handleScroll()
	{
		if (document.body.clientHeight + window.scrollY >= document.body.scrollHeight - 50)
		{
			window.newsFeed.addPosts(4);
		}
	}
}
else if (location.search.split("=")[0].substr(1) == "username")
{
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (xhttp.readyState == 4 && xhttp.status == 200)
	    {
			user = parseInt(xhttp.responseText);
			ReactDOM.render(<ProfilePage ref={(input) => {window.newsFeed = input}} user={user}/>, document.getElementById("profilePage"));
			ReactDOM.render(<MessagePage ref={(input) => {window.messagePage = input;}}/>, document.getElementById("messages"));
			ReactDOM.render(<UserInfo messages={window.messagePage} user={user}/>, document.getElementById("userInfo"));
			document.body.onscroll = function handleScroll()
			{
				if (document.body.clientHeight + window.scrollY >= document.body.scrollHeight - 50)
				{
					window.newsFeed.addPosts(4);
				}
			}
	    }
    }
   	xhttp.open("GET", "https://roastr2.herokuapp.com/getIDForUser.php?arg1='" + location.search.split("=")[1] + "'", true);
   	xhttp.send();
}
