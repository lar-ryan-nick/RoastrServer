var webpack = require('webpack');
var path = require('path');

var APP_DIR = path.resolve(__dirname, 'src');
var BUILD_DIR = path.resolve(__dirname, 'bin');

var config = {
	entry: APP_DIR + '/profile.jsx',
	//entry: APP_DIR + '/messages.jsx',
	//entry: APP_DIR + '/mainpage.jsx',
	//entry: APP_DIR + '/post.jsx',
	output: {
		path: BUILD_DIR,
		filename: 'profile.js'
		//filename: 'messagePage.js'
		//filename: 'bundle.js'
		//filename: 'post.js'
	},
	module : {
		loaders : [
			{
				test : /\.jsx?/,
				include : APP_DIR,
				loader : 'babel-loader'
			}
		]
	}
};

module.exports = config;
