import React from 'react';

class TopBar extends React.Component
{
    constructor(props)
    {
        super();
    }

    render()
    {
        return(
            <div style={{padding:10, backgroundColor:"#3d4553"}}>
                <img style={{width:46, height:46, borderRadius:23, verticalAlign:"top"}} src={"profilePictures/" + this.props.profileFilename}/>
                <a href={"http://35.164.1.3/profile.html?user=" + this.props.user} style={{margin:0, display:"inline-block", fontSize:40, verticalAlign:"top", color:"#fcc229", paddingLeft:15, textDecoration:"none"}}>{this.props.username}</a>
            </div>
        )
    }
}

class BottomBar extends React.Component
{
    constructor()
    {
        super();
        this.handleLike = this.handleLike.bind(this);
        this.handleUnlike = this.handleUnlike.bind(this);
    }

    handleLike()
    {
        this.props.addLike();
    }

    handleUnlike()
    {
        this.props.removeLike();
    }

    render()
    {
        let buttonFunction = this.handleLike;
        let buttonCaption = "Hate";
		let hateText = "hates";
		if (this.props.likes == 1)
		{
			hateText = "hate";
		}
		let roastText = "roasts";
		if (this.props.comments == 1)
		{
			roastText = "roast";
		}
        if (this.props.liked)
        {
            buttonFunction = this.handleUnlike;
            buttonCaption = "Unhate";
        }
        return(
            <div style={{padding:10, backgroundColor:"#3d4553"}}>
                <button onClick={buttonFunction}>{buttonCaption}</button>
                <p style={{paddingLeft:15, margin:0, display:"inline-block", color:"#fcc229"}}>{this.props.likes} hates</p>
                <p style={{display:"inline-block", margin:0, float:"right", paddingRight:15, color:"#fcc229"}}>{this.props.comments} roasts</p>
            </div>
        )
    }
}

class Comments extends React.Component
{
    constructor(props)
    {
        super();
        this.state = {
            comments:[]
        }
		this.tags = [];
        this.commentsLoaded = 0;
        this.addComment = this.addComment.bind(this);
        this.loadComment = this.loadComment.bind(this);
        this.loadComments = this.loadComments.bind(this);
		this.props = props;
		this.loadComments(4);
    }

    addComment()
    {
        let xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function()
	    {
		    if (xhttp.readyState == 4 && xhttp.status == 200)
	    	{
				let temp = this.props.comments;
				this.props.refresh(function()
				{
					let temp2 = this.props.comments;
					let temp3 = this.commentsLoaded;
					this.commentsLoaded = 0;
					this.loadComments(temp2 - temp);
					this.commentsLoaded += temp3;
				}.bind(this));
	    	}
    	}.bind(this)
    	xhttp.open("GET", "http://35.164.1.3/addComment.php?comment=\"" + this.textField.value + "\"&post=" + this.props.post + "&user=" + localStorage.getItem("userID"));
    	xhttp.send();
		this.textField.value = "";
   }

   loadComments(num)
   {
        let end = this.commentsLoaded + num;
        if (end > this.props.comments)
        {
            end = this.props.comments;
        }
        while (this.commentsLoaded < end)
        {
            this.loadComment();
			this.commentsLoaded++;
        }
   }

    loadComment()
    {
	    let xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function()
	    {
	    	if (xhttp.readyState == 4 && xhttp.status == 200)
	    	{
    			if (xhttp.responseText != "No comments")
    			{
                    let temp = this.state.comments.splice(0);
                    temp.push(JSON.parse(xhttp.responseText));
                    temp.sort(function(p1, p2)
		            {
	            		var dateData1 = p1["timeCommented"].split(" ");
		            	var dayData1 = dateData1[0].split("-");
            			var timeData1 = dateData1[1].split(":");
            			var dateData2 = p2["timeCommented"].split(" ");
	            		var dayData2 = dateData2[0].split("-");
	            		var timeData2 = dateData2[1].split(":");
	            		var d1 = new Date(dayData1[0], dayData1[1] - 1, dayData1[2], timeData1[0], timeData1[1], timeData1[2]);
	            		var d2 = new Date(dayData2[0], dayData2[1] - 1, dayData2[2], timeData2[0], timeData2[1], timeData2[2]);
	            		return d1.getTime() - d2.getTime();
	            	});
    				this.setState({
                        comments:temp.slice(0)
                    });
    			}
    		}
    	}.bind(this)
    	xhttp.open("GET", "http://35.164.1.3/getCommentData.php?arg1=" + this.props.post + "&arg2=" + (this.props.comments - this.commentsLoaded));
    	xhttp.send();
    }

    render()
    {
        let comments = [];
        this.state.comments.forEach((comment, key) => {
			let tags = [];
			let text = comment.comment.split(" ");
			for (let i = 0; i < text.length; i++)
			{
				let word = text[i];
				if (word)
				{
					if (word.substr(0, 1) == "@")
					{
           	    	    tags.push(<a key={i} href={"http://35.164.1.3/profile.html?username=" + word.substr(1)} style={{verticalAlign:"top", margin:0, color:"#e77225", paddingLeft:5, display:"inline-block", textDecoration:"none"}}>{word.substr(1)}</a>);
					}
					else
					{
           	    	    tags.push(<p key={i} style={{verticalAlign:"top", margin:0, color:"#ffffff", paddingLeft:5, display:"inline-block"}}>{word}</p>);
					}
				}
			}
			if (comment.profileFilename)
			{
               	comments.push(
            	    <div style={{padding:10}} key={key}>
            	        <img style={{width:40, height:40, borderRadius:20}} src={"profilePictures/" + comment.profileFilename}/>
						<div style={{display:"inline-block", verticalAlign:"top", width:"calc(100% - 40px)"}}>
            	        	<a href={"http://35.164.1.3/profile.html?user=" + comment.user} style={{paddingLeft:5, verticalAlign:"top", margin:0, display:"inline-block", color:"#fcc229", textDecoration:"none"}}>{comment.username}:</a>
							{tags}
						</div>
					</div>
            	);
			}
			else
			{
            	comments.push(
            	    <div style={{padding:10}} key={key}>
						<div style={{display:"inline-block", verticalAlign:"top", width:"calc(100% - 40px)"}}>
            	        	<a href={"http://35.164.1.3/profile.html?user=" + comment.user} style={{paddingLeft:5, verticalAlign:"top", margin:0, display:"inline-block", color:"#fcc229", textDecoration:"none"}}>{comment.username}:</a>
							{tags}
						</div>
               		</div>
				);
			}
        });
		if (this.props.comments > this.commentsLoaded)
		{
        	return(
        	    <div style={{padding:10, backgroundColor:"#2d3142"}}>
        	        <button style={{marginBottom:20}} onClick={this.loadComments.bind(this, 4)}>Load previous roasts</button>
        	        {comments}
        	        <div style={{padding:0}}>
						<p style={{color:"#fcc229"}}>Write a Roast</p>
        	            <input style={{width:"70%"}} type="text" ref={(input) => {this.textField = input;}}></input>
        	            <button style={{marginLeft:10, width:"20%"}} onClick={this.addComment}>Roast</button>
        	        </div>
        	    </div>
        	)
		}
		else
		{
        	return(
        	    <div style={{padding:10, backgroundColor:"#2d3142"}}>
        	        {comments}
        	        <div style={{padding:0}}>
						<p style={{color:"#fcc229"}}>Write a Roast</p>
        	            <input style={{width:"70%"}} type="text" ref={(input) => {this.textField = input;}}></input>
        	            <button style={{marginLeft:10, width:"20%"}} onClick={this.addComment}>Roast</button>
        	        </div>
        	    </div>
        	)
		}
    }
}

class PostView extends React.Component
{
    constructor(props)
    {
        super();
		this.state = {
			id:0,
			username:"",
			image:"",
			profilePicture:"",
			user:0,
			timePosted:"",
			caption:"",
			liked:0,
			likes:0,
			comments:0,
			imageFilename:"Roastr App icon.png",
			profileFilename:"roastrtransparent.png"
		}
        this.refresh = this.refresh.bind(this);
        this.addLike = this.addLike.bind(this);
        this.removeLike = this.removeLike.bind(this);
		this.props = props;
		this.refresh();
    }
    
    refresh(cb)
    {
        let xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function()
	    {
	    	if (xhttp.readyState == 4 && xhttp.status == 200)
	    	{
	    		this.setState(JSON.parse(xhttp.responseText));
				if (cb)
				{
					cb();
				}
	    	}
    	}.bind(this)
    	xhttp.open("GET", "http://35.164.1.3/getPostData.php?arg1=" + this.props.user + "&arg2=" + this.props.post + "&arg3=" + localStorage.getItem("userID"), true);
    	xhttp.send();
    }

    addLike()
    {
        var xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function()
	    {
	    	if (xhttp.readyState == 4 && xhttp.status == 200)
	    	{
                this.refresh();
	    	}
    	}.bind(this)
    	xhttp.open("GET", "http://35.164.1.3/addLike.php?user=" + localStorage.getItem("userID") + "&post=" + this.state.id, true);
    	xhttp.send();
    }

    removeLike()
    {
        var xhttp = new XMLHttpRequest();
	    xhttp.onreadystatechange = function()
	    {
	    	if (xhttp.readyState == 4 && xhttp.status == 200)
	    	{
	    		this.refresh();
    		}
    	}.bind(this)
    	xhttp.open("GET", "http://35.164.1.3/removeLike.php?user=" + localStorage.getItem("userID") + "&post=" + this.state.id, true);
    	xhttp.send();
    }

    render()
    {
		let tags = [];
		let text = this.state.caption.split(" ");
		for (let i = 0; i < text.length; i++)
		{
			let word = text[i];
			if (word)
			{
				if (word.substr(0, 1) == "@")
				{
          	        tags.push(<a key={i} href={"http://35.164.1.3/profile.html?username=" + word.substr(1)} style={{verticalAlign:"top", margin:0, color:"#e77225", paddingLeft:5, display:"inline-block", textDecoration:"none"}}>{word.substr(1)}</a>);
				}
				else
				{
         	    	tags.push(<p key={i} style={{verticalAlign:"top", margin:0, color:"#ffffff", paddingLeft:5, display:"inline-block"}}>{word}</p>);
				}
			}
		}
        if (this.state.image != "null")
		{
        	return (
        	    <div style={{paddingBottom:40}}>
        	        <TopBar user={this.state.user} username={this.state.username} profileFilename={this.state.profileFilename}/>
        	        <img style={{width:"100%"}} src={"images/" + this.state.imageFilename}/>
					<div style={{padding:10, backgroundColor:"#2d3142"}}>
						{tags}
					</div>
        	        <BottomBar likes={this.state.likes} liked={this.state.liked} comments={this.state.comments} addLike={this.addLike} removeLike={this.removeLike}/>
        	        <Comments post={this.state.id} comments={this.state.comments} refresh={this.refresh}/>
        	    </div>
        	)
		}
		else
		{
        	return (
        	    <div style={{paddingBottom:40}}>
        	        <TopBar user={this.state.user} username={this.state.username} profileFilename={this.state.profileFilename}/>
					<div style={{padding:10, backgroundColor:"#2d3142"}}>
						{tags}
					</div>
        	        <BottomBar likes={this.state.likes} liked={this.state.liked} comments={this.state.comments} addLike={this.addLike} removeLike={this.removeLike}/>
        	        <Comments post={this.state.id} comments={this.state.comments} refresh={this.refresh}/>
        	    </div>
			)
		}
    }
}

export default PostView;
