var http = require("http");
var url = require('url');
var fs = require('fs');
var io = require('socket.io');

var server = http.createServer(function(request, response){
	var path = url.parse(request.url).pathname;

	switch(path){
		case '/socket.html':
			fs.readFile(__dirname + path, function(error, data){
			if (error){
				response.writeHead(404);
				response.write("opps this doesn't exist - 404");
				response.end();
			}
			else{
				response.writeHead(200, {"Content-Type": "text/html"});
				response.write(data, "utf8");
				response.end();
			}});
			break;
		default:
			response.writeHead(404);
		    response.write("opps this doesn't exist - 404");
		    response.end();
		    break;
	}
});

server.listen(8000);

var listener = io.listen(server);
var clients = {};
var clientInverse = {};

listener.sockets.on('connection', function(socket){
	socket.on('addUser', function(data){
		clients[data.user] = socket.id;
		clientInverse[socket.id] = data.user;
	});

	socket.on('sentMessage', function(data)
	{
		var current = clients[data.user];
		if (current)
		{
	    	listener.sockets.connected[clients[data.user]].emit('message', data);
		}
	});

	socket.on('disconnect', function(){
		delete clients[clientInverse[socket.id]];
		delete clientInverse[socket.id];
	});
});
/*
listener.sockets.on('disconnect', function(socket){
	delete clients[clientInverse[socket.id]];
	delete clientInverse[socket.id];
});
*/
