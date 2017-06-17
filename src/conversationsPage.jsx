import React from 'react';

class Conversation extends React.Component
{
	constructor(props)
	{
		super();
		this.state = {"backgroundColor":"transparent"};
		this.changeBackgroundColor = this.changeBackgroundColor.bind(this);
	}

	handleClick()
	{
		this.props.onClick(this.props.index, this.props.data.user, this.props.data.profileFilename, this.props.data.username);
	}
	
	changeBackgroundColor(color)
	{
		let newState = this.state;
		newState["backgroundColor"] = color;
		this.setState(newState);
	}

	render()
	{
		if (this.props.data)
		{
			if (this.props.data.newMessage)
			{
				return (
					<div onMouseOver={function(){this.changeBackgroundColor("#6f7785");}.bind(this)} onMouseOut={function(){this.changeBackgroundColor("transparent");}.bind(this)} onClick={this.handleClick.bind(this)} style={{backgroundColor:this.state.backgroundColor, borderRadius:"5%", padding:10, borderBottom:"1px solid #2d3142"}}>
						<img width="20%" style={{borderRadius:"50%", verticalAlign:"top"}} src={"profilePictures/" + this.props.data.profileFilename}/>
						<div style={{paddingLeft:5, display:"inline-block", width:"75%"}}>
							<div style={{height:10, width:10, borderRadius:"50%", display:"inline-block", paddingRight:5, backgroundColor:"#3abfbc"}}></div>
							<p style={{margin:0, color:"#fcc229", display:"inline-block", fontSize:20}}>{this.props.data.username}</p>
							<p style={{display:"block", overflow:"hidden", margin:0, textOverflow:"ellipsis", color:"#fefeff", lineHeight:"1.2em", maxHeight:"2.35em"}}>{this.props.data.message}</p>
						</div>
					</div>
				);
			}
			else
			{
				return (
					<div onMouseOver={function(){this.changeBackgroundColor("#6f7785");}.bind(this)} onMouseOut={function(){this.changeBackgroundColor("transparent");}.bind(this)} onClick={this.handleClick.bind(this)} style={{backgroundColor:this.state.backgroundColor, borderRadius:"5%", padding:10, borderBottom:"1px solid #2d3142"}}>
						<img width="20%" style={{borderRadius:"50%", verticalAlign:"top"}} src={"profilePictures/" + this.props.data.profileFilename}/>
						<div style={{paddingLeft:5, display:"inline-block", width:"75%"}}>
							<p style={{margin:0, color:"#e77225", fontSize:20}}>{this.props.data.username}</p>
							<p style={{display:"block", overflow:"hidden", margin:0, textOverflow:"ellipsis", color:"#fefeff", lineHeight:"1.2em", maxHeight:"2.35em"}}>{this.props.data.message}</p>
						</div>
					</div>
				);
			}
		}
		else
		{
			return(
				<div/>
			);
		}
	}			
}

class ConversationsPage extends React.Component
{
	constructor(props)
	{
		super();
	}

	setUser(index, user, profilePic, username) 
	{
		this.props.onClick(index, user, profilePic, username);
	}

	render()
	{
		let conversations = [];
		for (let i = 1; i <= this.props.data.conversationsLoaded; i++)
		{
			conversations.push(<Conversation key={i} onClick={this.setUser.bind(this)} data={this.props.data[i]} index={i}/>);
		}
		return (
			<div>
				{conversations}
			</div>
		);
	}
}

export default ConversationsPage;
