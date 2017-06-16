import React from 'react';
import ReactDOM from 'react-dom';
import PostView from './postView.jsx';
import UserInfo from './userInfo.jsx';
import MessagePage from './messagePage.jsx';

class NewsFeed extends React.Component
{
	constructor()
	{
		super();
		this.state = {
			postsAdded:0,
		}
		this.numPosts = 0;
		this.getNumPosts = this.getNumPosts.bind(this);
		this.addPosts = this.addPosts.bind(this);
		this.getNumPosts(function()
		{
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
		xhttp.open("GET", "http://php-empty-site-p7x3h7.azurewebsites.net/getPostCount.php", true);
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
			tags.push(<PostView key={i} user="0" post={this.numPosts - i}/>);
		}
		return (
			<div>
				{tags}
			</div>
		);
	}
}

document.body.onscroll = function handleScroll()
{
	if (document.body.clientHeight + window.scrollY >= document.body.scrollHeight - 50)
	{
		window.newsFeed.addPosts(4);
	}
}

window.onresize = function()
{
	window.userInfo.picture.style.height = window.userInfo.picture.offsetWidth;
}

ReactDOM.render(<NewsFeed ref={(input) => {window.newsFeed = input;}}/>, document.getElementById("newsFeed"));
ReactDOM.render(<UserInfo ref={(input) => {window.userInfo = input;}} user={localStorage.getItem("userID")}/>, document.getElementById("userInfo"));
ReactDOM.render(<MessagePage/>, document.getElementById("messages"));
