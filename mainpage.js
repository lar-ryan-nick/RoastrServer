var numPosts = 0;
var loading = false;
var postsLoaded = 0;
var postData = [];
function goToPostPage()
{
	window.location = "http://roastr.azurewebsites.net/post.html";
}
function loadNumPosts(cb)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			numPosts = parseInt(this.responseText);
			console.log(numPosts);
			cb();
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/getPostCount.php", true);
	xhttp.send();
}
function loadPost(arg1, arg2, cb)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			//console.log(this.responseText);
			//console.log(this.response);
			postData.push(JSON.parse(this.responseText));
			//console.log(JSON.parse(this.responseText));
			cb();
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/getPostData.php?arg1=" + arg1 + "&arg2=" + arg2 + "&arg3=" + localStorage.getItem("userID"), true);
	xhttp.timeout = 0;
	xhttp.send();
}
function loadPosts(num, cb)
{
	loading = true;
	var end = postData.length + num > numPosts ? numPosts : postData.length + num;
	for (var i = postData.length; i < end; i++)
	{
		loadPost(0, numPosts - i, function()
		{
			if (postData.length == end)
			{
				loading = false;
				cb();
			}
		});
	}
}
function printPosts()
{
	postData.sort(function(p1, p2)
	{
		var dateData1 = p1["timePosted"].split(" ");
		var dayData1 = dateData1[0].split("-");
		var timeData1 = dateData1[1].split(":");
		var dateData2 = p2["timePosted"].split(" ");
		var dayData2 = dateData2[0].split("-");
		var timeData2 = dateData2[1].split(":");
		var d1 = new Date(dayData1[0], dayData1[1] - 1, dayData1[2], timeData1[0], timeData1[1], timeData1[2]);
		var d2 = new Date(dayData2[0], dayData2[1] - 1, dayData2[2], timeData2[0], timeData2[1], timeData2[2]);
		return d2.getTime() - d1.getTime();
	});
	//console.log(postData);
	var page = document.getElementById("post");
	/*
	while(page.firstChild) 
	{
		page.removeChild(page.firstChild);
	}
	*/
	for (var j = postsLoaded; j < postData.length; j++)
	{
		var post = document.createElement("div");
		page.appendChild(post);
		var topBar = document.createElement("div");	
		topBar.setAttribute("class", "tabbar");
		post.appendChild(topBar);
		var profilePic = new Image(40, 40);
		profilePic.setAttribute("style", "border-radius: 20px");
		if (postData[j]["profileFilename"])
		{
			profilePic.src = "profilePictures/" + postData[j]["profileFilename"];
		}
		else
		{
			profilePic.src = "profilePictures/roastrtransparent.png";
		}	
		topBar.appendChild(profilePic);
		var username = new Text(" " + postData[j]["username"]);
		topBar.appendChild(username);
		var imageContainer = document.createElement("div");
		post.appendChild(imageContainer);
		var image = new Image();
		image.setAttribute("style", "width:100%");
		image.src = "images/" + postData[j]["imageFilename"];
		if (postData[j]["imageFileName"] != "")
		{
			imageContainer.appendChild(image);
		}
		var captionContainer = document.createElement("div");
		captionContainer.setAttribute("class", "caption");
		post.appendChild(captionContainer);
		var caption  = new Text(postData[j]["caption"]);
		captionContainer.appendChild(caption);
		var bottomBar = document.createElement("div");
		bottomBar.setAttribute("class", "tabbar");
		var leftSpan = document.createElement("div");
		var rightSpan = document.createElement("div");
		var clearDiv = document.createElement("div");
		clearDiv.setAttribute("style", "clear:both;");
		leftSpan.width = "50%";
		rightSpan.width = "50%";
		leftSpan.setAttribute("style", "float:left;");
		rightSpan.setAttribute("style", "float:right;text-align:right;");
		bottomBar.appendChild(leftSpan);
		bottomBar.appendChild(rightSpan);
		bottomBar.appendChild(clearDiv);
		post.appendChild(bottomBar);
		var hate = document.createElement("button");
		if (postData[j]["liked"])
		{
			hate.textContent = "Unhate";
			hate.id = postData[j]["id"];
			hate.onclick = function()
			{
				unhatePost(this.id);
			};
		}
		else
		{
			hate.textContent = "Hate";
			hate.id = postData[j]["id"];
			hate.onclick = function() 
			{
				hatePost(this.id);
			}
		}
		leftSpan.appendChild(hate);
		var numHates = document.createElement("button");
		numHates.id = postData[j]["id"] + "hate counter";
		if (postData[j]["likes"] == 1)
		{
			numHates.textContent = postData[j]["likes"] + " Hate";
		}
		else
		{	
			numHates.textContent = postData[j]["likes"] + " Hates";
		}
		leftSpan.appendChild(numHates);
		var numRoasts = document.createElement("button");
		numRoasts.id = postData[j]["id"] + " roasts";
		if (postData[j]["comments"] == 1)
		{
			numRoasts.textContent = postData[j]["comments"] + " Roast";
		}
		else
		{	
			numRoasts.textContent = postData[j]["comments"] + " Roasts";
		}
		numRoasts.setAttribute("style", "text-align:right");
		rightSpan.appendChild(numRoasts);
		var commentsArea = document.createElement("div");
		commentsArea.id = postData[j]["id"] + "commentsArea";
		commentsArea.setAttribute("style", "width:100%;background-color:#2d3142;");
		post.appendChild(commentsArea);
		var comments = document.createElement("div");
		comments.id = postData[j]["id"] + "comments";
		comments.setAttribute("style", "background-color:#2d3142;padding:10px;");
		commentsArea.appendChild(comments);
		var start = postData[j]["comments"] - 3 > 1 ? postData[j]["comments"] - 3 : 1;
		loadComments(postData[j]["id"], start, postData[j]["comments"], function(comments)
		{
			comments.sort(function(p1, p2)
			{
				var dateData1 = p1["timeCommented"].split(" ");
				var dayData1 = dateData1[0].split("-");
				var timeData1 = dateData1[1].split(":");
				var dateData2 = p2["timeCommented"].split(" ");
				var dayData2 = dateData2[0].split("-");
				var timeData2 = dateData2[1].split(":");
				var d1 = new Date(dayData1[0], dayData1[1] - 1, dayData1[2], timeData1[0], timeData1[1], timeData1[2]);
				var d2 = new Date(dayData2[0], dayData2[1] - 1, dayData2[2], timeData2[0], timeData2[1], timeData2[2]);
				return d2.getTime() - d1.getTime();
			});
			var commentsArea = document.getElementById(comments[0]["post"] + "comments");
			for (var k = 0; k < comments.length; k++)
			{
				var comment = document.createElement("div");
				comment.setAttribute("style", "padding:10px");
				commentsArea.insertBefore(comment, commentsArea.firstChild);
				var profilePic = new Image(40, 40);
				profilePic.setAttribute("style", "border-radius:20px;vertical-align:bottom;");
				if (comments[k]["profileFilename"])
				{
					profilePic.src = "profilePictures/" + comments[k]["profileFilename"];
				}
				else
				{
					profilePic.src = "profilePictures/roastrtransparent.png";
				}
				comment.appendChild(profilePic);
				var text = document.createElement("text");
				text.textContent = comments[k]["username"] + ": " + comments[k]["comment"];
				text.setAttribute("style", "color:#fcc229");
				comment.appendChild(text);
			}
		});
		if (start > 1)
		{
			var loadMoreComments = document.createElement("button");
			loadMoreComments.textContent = "Load previous comments";
			loadMoreComments.id = postData[j]["id"] + " loadComments";
			loadMoreComments.onclick = function()
			{
				var postID = this.id.split(" ")[0];
				var comments = document.getElementById(postID + "comments");
				var numRoasts = document.getElementById(postID + " roasts");
				var end = parseInt(numRoasts.textContent.split(" ")[0]) - comments.childElementCount > 1 ? parseInt(numRoasts.textContent.split(" ")[0]) - comments.childElementCount : 1;
				var start = end - 3 > 1 ? end - 3 : 1;
				//console.log("Start: " + start + " End: " + end);
				loadComments(postID, start, end, function(comments)
				{
					comments.sort(function(p1, p2)
					{
						var dateData1 = p1["timeCommented"].split(" ");
						var dayData1 = dateData1[0].split("-");
						var timeData1 = dateData1[1].split(":");
						var dateData2 = p2["timeCommented"].split(" ");
						var dayData2 = dateData2[0].split("-");
						var timeData2 = dateData2[1].split(":");
						var d1 = new Date(dayData1[0], dayData1[1] - 1, dayData1[2], timeData1[0], timeData1[1], timeData1[2]);
						var d2 = new Date(dayData2[0], dayData2[1] - 1, dayData2[2], timeData2[0], timeData2[1], timeData2[2]);
						return d2.getTime() - d1.getTime();
					});
					var commentsArea = document.getElementById(comments[0]["post"] + "commentsArea");
					var commentsBlock = document.getElementById(comments[0]["post"] + "comments");
					var loadMoreComments = document.getElementById(comments[0]["post"] + " loadComments");
					var numRoasts = document.getElementById(comments[0]["post"] + " roasts");
					for (var k = 0; k < comments.length; k++)
					{
						var comment = document.createElement("div");
						comment.setAttribute("style", "padding:10px");
						commentsBlock.insertBefore(comment, commentsBlock.firstChild);
						var profilePic = new Image(40, 40);
						profilePic.setAttribute("style", "border-radius:20px;vertical-align:bottom;");
						if (comments[k]["profileFilename"])
						{
							profilePic.src = "profilePictures/" + comments[k]["profileFilename"];
						}
						else
						{
							profilePic.src = "profilePictures/roastrtransparent.png";
						}
						comment.appendChild(profilePic);
						var text = document.createElement("text");
						text.textContent = comments[k]["username"] + ": " + comments[k]["comment"];
						text.setAttribute("style", "color:#fcc229");
						comment.appendChild(text);
					}
					if (commentsBlock.childElementCount >= numRoasts.textContent.split(" ")[0])
					{
						commentsArea.removeChild(loadMoreComments);
					}
				});
			}
			commentsArea.insertBefore(loadMoreComments, commentsArea.firstChild);
		}
		var commentTop = document.createElement("div");
		commentTop.setAttribute("style", "text-align:left");
		commentsArea.appendChild(commentTop);
		var commentPrompt = document.createElement("text");
		commentPrompt.textContent = "Write a Roast";
		commentPrompt.setAttribute("style", "width:100%;color:#fcc229;text-align:left");
		commentTop.appendChild(commentPrompt);
		var commentEntry = document.createElement("div");
		commentEntry.setAttribute("style", "text-align:left");
		commentsArea.appendChild(commentEntry);
		var commentInput = document.createElement("input");
		commentInput.setAttribute("type", "text");
		commentInput.setAttribute("style", "width:80%");
		commentInput.id = postData[j]["id"] + " commentInput";
		commentEntry.appendChild(commentInput);
		var postComment = document.createElement("button");
		postComment.textContent = "Roast";
		postComment.id = postData[j]["id"] + " commentButton";
		postComment.setAttribute("style", "width:20%");
		postComment.onclick = function()
		{
			var postID = this.id.split(" ")[0];
			var commentInput = document.getElementById(postID + " commentInput");
			addComment(postID, commentInput.value, function()
			{
				var oldNumRoasts = parseInt(document.getElementById(postID + " roasts").textContent.split(" ")[0]);
				getNumComments(postID, function()
				{
					var numRoasts = document.getElementById(postID + " roasts");
					loadComments(postID, oldNumRoasts + 1, parseInt(numRoasts.textContent.split(" ")[0]), function(comments)
					{
						//console.log(comments);
						comments.sort(function(p1, p2)
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
						var commentsArea = document.getElementById(comments[0]["post"] + "comments");
						for (var k = 0; k < comments.length; k++)
						{
							var comment = document.createElement("div");
							comment.setAttribute("style", "padding:10px");
							commentsArea.append(comment);
							var profilePic = new Image(40, 40);
							profilePic.setAttribute("style", "border-radius:20px;vertical-align:bottom;");
							if (comments[k]["profileFilename"])
							{
								profilePic.src = "profilePictures/" + comments[k]["profileFilename"];
							}
							else
							{
								profilePic.src = "profilePictures/roastrtransparent.png";
							}
							comment.appendChild(profilePic);
							var text = document.createElement("text");
							text.textContent = comments[k]["username"] + ": " + comments[k]["comment"];
							text.setAttribute("style", "color:#fcc229");
							comment.appendChild(text);
						}
					});
				});
				commentInput.value = "";
			});
		};
		commentEntry.appendChild(postComment);
		var bottomSpace = document.createElement("div");
		bottomSpace.setAttribute("style", "height:40px");
		page.appendChild(bottomSpace);
	}
	postsLoaded = postData.length;
}
function handleScroll()
{
	if(document.body.clientHeight + window.scrollY >= document.body.scrollHeight - 100) 
	{
		if (!loading)
		{
			loadPosts(4, function()
			{
				printPosts();
			});
		}
	}
}
function hatePost(postID)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var hate = document.getElementById(postID);
			hate.onclick = function()
			{
				unhatePost(hate.id);
			}
			hate.textContent = "Unhate";
			getNumHates(hate.id);
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/addLike.php?user=" + localStorage.getItem("userID") + "&post=" + postID, true);
	xhttp.send();
}
function unhatePost(postID)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var hate = document.getElementById(postID);
			hate.onclick = function()
			{
				hatePost(hate.id);
			}
			hate.textContent = "Hate";
			getNumHates(hate.id);
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/removeLike.php?user=" + localStorage.getItem("userID") + "&post=" + postID, true);
	xhttp.send();
}
function getNumHates(postID)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var numHates = document.getElementById(postID + "hate counter");
			if (this.responseText == 1)
			{
				numHates.textContent = this.responseText + " Hate";
			}
			else
			{	
				numHates.textContent = this.responseText + " Hates";
			}
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/getNumLikes.php?arg1=" + postID, true);
	xhttp.send();
}
function loadComment(postID, index, cb)
{
	var xhttp = new XMLHttpRequest();
	//console.log("http://roastr.azurewebsites.net/getCommentData.php?arg1=" + postID + "&arg2=" + index);
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			if (this.responseText != "No comments")
			{
				// console.log("Fuck " + this.responseText)
				cb(JSON.parse(this.responseText));
			}
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/getCommentData.php?arg1=" + postID + "&arg2=" + index);
	xhttp.send();
}
function loadComments(postID, start, end, cb)
{
	var responses = [];
	var commentsLoaded = start;
	for (var i = start; i <= end; i++)
	{
		loadComment(postID, i, function(response)
		{
			//console.log("CommentsLoaded: " + commentsLoaded);
			commentsLoaded++;
			//console.log("CommentsLoaded: " + commentsLoaded);
			responses.push(response);
			//console.log(responses);
			if (commentsLoaded > end)
			{
				responses.push();
				cb(responses);
			}
		});
	}
}
function getNumComments(postID, cb)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var numRoasts = document.getElementById(postID + " roasts");
			if (this.responseText == 1)
			{
				numRoasts.textContent = this.responseText + " Roast";
			}
			else
			{	
				numRoasts.textContent = this.responseText + " Roasts";
			}
			cb();
		}
	}
	xhttp.open("GET", "http://roastr.azurewebsites.net/getNumComments.php?arg1=" + postID, true);
	xhttp.send();
}
function addComment(postID, comment, cb)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			cb(this.responseText);
		}
	}
	//console.log(comment);
	xhttp.open("GET", "http://roastr.azurewebsites.net/addComment.php?comment=\"" + comment + "\"&post=" + postID + "&user=" + localStorage.getItem("userID"));
	xhttp.send();
}
function getUserData(cb)
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			cb(this.responseText);
		}
	}
	//console.log(comment);
	xhttp.open("GET", "http://roastr.azurewebsites.net/getUserData.php?&arg1=" + localStorage.getItem("userID") + "&arg2=" + localStorage.getItem("userID"));
	xhttp.send();
}
function handleUserInfo()
{
	getUserData(function(response)
	{
		var userData = JSON.parse(response);
		var profilePic = document.getElementById("profilePic");
		var profileFilename = userData['profileFilename'];
		profilePic.src = "profilePictures/" + profileFilename;
		profilePic.setAttribute("style", "width:200px;border-radius:100px");
		var username = document.getElementById("username");
		username.innerHTML = userData['username'];
		var likesCount = document.getElementById("likesCount");
		likesCount.innerHTML = userData['likes'];
		var postsCount = document.getElementById("postsCount");
		postsCount.innerHTML = userData['posts'];
		var friendsCount = document.getElementById("friendsCount");
		friendsCount.innerHTML = userData['friends'];
	});
}
