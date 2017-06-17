import EXIF from 'exif-js';
import React from 'react';

class ImageCropper extends React.Component {

	constructor() {
		super();
		this.state = {
			imageData:"",
			previewImageData:"",
			cropperLeft:0,
			cropperTop:0,
			cropperMaxWidth:500,
			cropperMaxHeight:500,
			mainWidth:500,
			mainHeight:500,
			previewWidth:300,
			previewHeight:300
		};
		this.startx;
		this.starty;
		this.image = new Image();
		this.preview = document.createElement("canvas");
		this.loadImage = this.loadImage.bind(this);
		this.takePreview = this.takePreview.bind(this);
		this.getImageData = this.getImageData.bind(this);
		this.drag = this.drag.bind(this);
		this.dragStart = this.dragStart.bind(this);
		this.dragEnd = this.dragEnd.bind(this);
		this.dragOver = this.dragOver.bind(this);
	}

	loadImage()
	{
		if (this.fileInput.files && this.fileInput.files[0])
		{
			let reader = new FileReader();
			reader.onloadend = function()
			{
				this.image.src = reader.result;
				let exif = EXIF.readFromBinaryFile(this.base64ToArrayBuffer(reader.result));
				this.image.onload = function(){
					let ratio = this.image.height / this.image.width;
					console.log(exif.Orientation);
					let tempCanvas = document.createElement("canvas");
					if (exif.Orientation > 4)
					{
						ratio = 1 / ratio;
					}
					let canWidth = tempCanvas.width;
					let canHeight = tempCanvas.height;
					if (ratio > 1)
					{
						tempCanvas.width = 1000 / ratio;
						tempCanvas.height = 1000;
						if (exif.Orientation > 4)
						{
							canWidth = tempCanvas.height;
							canHeight = tempCanvas.width;
						}
					}
					else if (ratio < 1)
					{
						tempCanvas.height = 1000 * ratio;
						tempCanvas.width = 1000;
					}
					let context = tempCanvas.getContext("2d");
					context.clearRect(0, 0, tempCanvas.width, tempCanvas.height);
					context.setTransform(1, 0, 0, 1, 0, 0);
					switch (exif.Orientation)
					{
						case 2:
							context.translate(canWidth, 0);
							context.scale(-1,1);
							break;
						case 3:
							context.translate(canWidth,canHeight);
							context.rotate(Math.PI);
							break;
						case 4:
							context.translate(0,canHeight);
							context.scale(1,-1);
							break;
						case 5:
							context.rotate(0.5 * Math.PI);
							context.scale(1,-1);
							break;
						case 6:
							context.rotate(.5 * Math.PI);
							context.translate(0, -canHeight);
							break;
						case 7:
							context.rotate(0.5 * Math.PI);
							context.translate(canWidth,-canHeight);
							context.scale(-1,1);
							break;
						case 8:
							context.rotate(-0.5 * Math.PI);
							context.translate(-canWidth,0);
							break;
						default:
							break;
					}
					if (exif.Orientation > 4)
					{
						context.drawImage(this.image, 0, 0, tempCanvas.height, tempCanvas.width);
					}
					else
					{
						context.drawImage(this.image, 0, 0, tempCanvas.width, tempCanvas.height);
					}
					this.image.onload = function(){
						let newState = this.state;
						let ratio = this.image.height / this.image.width;
						console.log(this.image.height + " " + this.image.width);
						if (ratio > 1)
						{
							this.preview.width = 1000 / ratio;
							this.preview.height = 1000;
							newState.mainWidth = 500 / ratio;
							newState.mainHeight = 500;
							newState.previewWidth = 300 / ratio;
							newState.previewHeight = 300;
						}
						else
						{
							this.preview.height = 1000 * ratio;
							this.preview.width = 1000;
							newState.mainWidth = 500;
							newState.mainHeight = 500 * ratio;
							newState.previewWidth = 300;
							newState.previewHeight = 300 * ratio;
						}
						context = this.preview.getContext("2d");
						context.drawImage(this.image, 0, 0, this.preview.width, this.preview.height);
						if (this.cropper)
						{
							newState.cropperTop = 100;
							newState.cropperLeft = 100;
							newState.cropperMaxWidth = newState.mainWidth;
							newState.cropperMaxHeight = newState.mainHeight;
						}
						this.setState(newState);
					}.bind(this);
					this.image.src = tempCanvas.toDataURL("image/png");
					let newState = this.state;
					newState.imageData = this.image.src;
					newState.previewImageData = this.image.src;
					this.setState(newState);
				}.bind(this);
			}.bind(this);
			reader.readAsDataURL(this.fileInput.files[0]);
		}
	}

	base64ToArrayBuffer(base64)
	{
	    base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
	    let binaryString = atob(base64);
	    let len = binaryString.length;
	    let bytes = new Uint8Array(len);
	    for (let i = 0; i < len; i++) {
	        bytes[i] = binaryString.charCodeAt(i);
	    }
	    return bytes.buffer;
	}

	drag(event)
	{
		let newState = this.state;
		let ratio = this.image.height / this.image.width;
		if (ratio < 1)
		{
			ratio = 1;
		}
		if (event.target.offsetLeft + event.target.offsetWidth + event.screenX - this.startx < this.mainImage.offsetLeft + this.mainImage.offsetWidth && event.target.offsetLeft + event.screenX - this.startx > this.mainImage.offsetLeft)
		{
			newState.cropperLeft = event.target.offsetLeft + event.screenX - this.startx;
			newState.cropperMaxWidth = this.mainImage.offsetWidth + this.mainImage.offsetLeft - event.target.offsetLeft - 4;
		}
		else if (event.screenX - this.startx > 0)
		{
			newState.cropperLeft = this.mainImage.offsetLeft - event.target.offsetWidth + this.mainImage.offsetWidth;
			newState.cropperMaxWidth = this.mainImage.offsetWidth + this.mainImage.offsetLeft - event.target.offsetLeft - 4;
		}
		else if (event.screenX - this.startx < 0)
		{
			newState.cropperLeft = this.mainImage.offsetLeft;
			newState.cropperMaxWidth = this.mainImage.offsetWidth + this.mainImage.offsetLeft - event.target.offsetLeft - 4;
		}
		ratio = this.image.height / this.image.width;
		if (ratio > 1)
		{
			ratio = 1;
		}
		if (event.target.offsetTop + event.target.offsetHeight + event.screenY - this.starty < this.mainImage.offsetTop + this.mainImage.offsetHeight && event.target.offsetTop + event.screenY - this.starty > this.mainImage.offsetTop)
		{
			newState.cropperTop = event.target.offsetTop + event.screenY - this.starty;
			newState.cropperMaxHeight = this.mainImage.offsetHeight + this.mainImage.offsetTop - event.target.offsetTop - 4;
		}
		else if (event.screenY - this.starty > 0)
		{
			newState.cropperTop = this.mainImage.offsetTop - event.target.offsetHeight + this.mainImage.offsetHeight;
			newState.cropperMaxHeight = this.mainImage.offsetHeight + this.mainImage.offsetTop - event.target.offsetTop - 4;
		}
		else if (event.screenY - this.starty < 0)
		{
			newState.cropperTop = this.mainImage.offsetTop;
			newState.cropperMaxHeight = this.mainImage.offsetHeight + this.mainImage.offsetTop - event.target.offsetTop - 4;
		}
		this.startx = event.screenX;
		this.starty = event.screenY;
		this.setState(newState);
	}

	dragStart(event)
	{
		this.startx = event.screenX;
		this.starty = event.screenY;
		let blankImage = new Image(0, 0);
		blankImage.src = "images/transparent.png";
		blankImage.style.visibility = "hidden";
		blankImage.style.display = "none";
		event.dataTransfer.setDragImage(blankImage, document.offsetWidth, document.offsetHeight);
	}

	dragEnd(event)
	{
		event.preventDefault();
		this.takePreview();
	}

	dragOver(event)
	{
		event.preventDefault();
	}

	takePreview()
	{
		if (this.mainImage && this.preview && this.image)
		{
			console.log("Fuck");
			let context = this.preview.getContext("2d");
			context.clearRect(0, 0, this.preview.width, this.preview.height);
			let ratio = this.image.height / this.image.width;
			if (ratio < 1)
			{
				ratio = 1;
			}
			let widthRatio = this.image.width / this.mainImage.width;
			ratio = this.image.height / this.image.width;
			if (ratio > 1)
			{
				ratio = 1;
			}
			let heightRatio = this.image.height / this.mainImage.height;
			let cropperRatio = this.cropper.offsetHeight / this.cropper.offsetWidth;
			let newState = this.state;
			if (cropperRatio > 1)
			{
				this.preview.width = 1000 / cropperRatio;
				this.preview.height = 1000;
				newState.previewWidth = 300 / cropperRatio;
				newState.previewHeight = 300;
			}
			else
			{
				this.preview.height = 1000 * cropperRatio;
				this.preview.width = 1000;
				newState.previewWidth = 300;
				newState.previewHeight = 300 * cropperRatio;
			}
			ratio = this.image.height / this.image.width;
			if (ratio > 1)
			{
				let left = (this.cropper.offsetLeft - this.mainImage.offsetLeft) * widthRatio;
				if (left < 0)
				{
					left = 0;
				}
				let cropWidth = (this.cropper.offsetWidth) * widthRatio;
				if (cropWidth > this.image.width - left)
				{
					cropWidth = this.image.width - left;
				}
				context.drawImage(this.image, left, (this.cropper.offsetTop - this.mainImage.offsetTop) * heightRatio, cropWidth, this.mainImage.offsetHeight * heightRatio, 0, 0, this.preview.width, this.preview.height);
			}
			else
			{
				let top = (this.cropper.offsetTop - this.mainImage.offsetTop) * heightRatio;
				if (top < 0)
				{
					top = 0;
				}
				let cropHeight = (this.cropper.offsetHeight) * heightRatio;
				if (cropHeight > this.image.height - top)
				{
					cropHeight = this.image.height - top;
				}
				context.drawImage(this.image, (this.cropper.offsetLeft - this.mainImage.offsetLeft) * widthRatio , top, (this.cropper.offsetWidth) * widthRatio, cropHeight, 0, 0, this.preview.width, this.preview.height);
			}
			newState.previewImageData = this.preview.toDataURL();
			this.setState(newState);
		}
	}

	getImageData()
	{
			/*
			let tempCanvas = document.createElement("canvas")
			let context = tempCanvas.getContext("2d");
			context.clearRect(0, 0, tempCanvas.width, tempCanvas.height);
			let ratio = this.image.height / this.image.width;
			if (ratio < 1)
			{
				ratio = 1;
			}
			let widthRatio = this.image.width / this.canvas.width;
			ratio = this.image.height / this.image.width;
			if (ratio > 1)
			{
				ratio = 1;
			}
			let heightRatio = this.image.height / this.canvas.height;
			let cropperRatio = this.cropper.offsetHeight / this.cropper.offsetWidth;
			if (cropperRatio > 1)
			{
				tempCanvas.width = 1000 / cropperRatio;
				tempCanvas.height = 1000;
			}
			else
			{
				tempCanvas.height = 1000 * cropperRatio;
				tempCanvas.width = 1000;
			}
			ratio = this.image.height / this.image.width;
			if (ratio > 1)
			{
				let left = (this.cropper.offsetLeft - this.canvas.offsetLeft) * widthRatio;
				if (left < 0)
				{
					left = 0;
				}
				let cropWidth = (this.cropper.offsetWidth) * widthRatio;
				if (cropWidth > this.image.width - left)
				{
					cropWidth = this.image.width - left;
				}
				context.drawImage(this.image, left, (this.cropper.offsetTop - this.canvas.offsetTop) * heightRatio, cropWidth, this.cropper.offsetHeight * heightRatio, 0, 0, tempCanvas.width, tempCanvas.height);
			}
			else
			{
				let top = (this.cropper.offsetTop - this.canvas.offsetTop) * heightRatio;
				if (top < 0)
				{
					top = 0;
				}
				let cropHeight = (this.cropper.offsetHeight) * heightRatio;
				if (cropHeight > this.image.height - top)
				{
					cropHeight = this.image.height - top;
				}
				context.drawImage(this.image, (this.cropper.offsetLeft - this.canvas.offsetLeft) * widthRatio , top, (this.cropper.offsetWidth) * widthRatio, cropHeight, 0, 0, tempCanvas.width, tempCanvas.height);
			}
			*/
		this.takePreview();
		let imageData = this.preview.toDataURL("image/jpeg", 1.0).split(",")[1];
		imageData.replace(/[+]/g, "%2B");
		return imageData;
	}

	render()
	{
		if (this.fileInput && this.fileInput.files && this.fileInput.files[0])
		{
			let backImage = "url(" + this.state.imageData + ")";
			return (
				<div>
					<center>
						<input ref={(input) => {this.fileInput = input;}} type="file" accept="image/*" onChange={this.loadImage}/>
						<div>
							<img src={this.state.previewImageData} style={{width:this.state.previewWidth, height:this.state.previewHeight, paddingRight:100, display:"inline-block"}}/>
							<div ref={(input) => {this.mainImage = input;}} style={{display:"inline-block", backgroundImage:backImage, width:this.state.mainWidth, height:this.state.mainHeight, backgroundColor:"#000000", zIndex:-1}}>
								<div ref={(input) => {this.cropper = input;}} draggable="true" style={{top:this.state.cropperTop, left:this.state.cropperLeft, border:"2px solid #ffffff", minWidth:100, minHeight:100, maxWidth:this.state.cropperMaxWidth, maxHeight:this.state.cropperMaxHeight, resize:"both", overflow:"auto"}} onDrag={function(event){this.drag(event);}.bind(this)} onDragStart={function(event){this.dragStart(event);}.bind(this)} onDragEnd={function(event){this.dragEnd(event);}.bind(this)} onDragOver={function(event){this.dragOver(event);}.bind(this)}></div>
							</div>
						</div>
					</center>
				</div>
			);
		}
		else
		{
			return (
				<div>
					<center>
						<input ref={(input) => {this.fileInput = input;}} type="file" accept="image/*" onChange={this.loadImage}/>
						<div>
							<div ref={(input) => {this.mainImage = input;}} style={{width:500, height:500, backgroundColor:"#000000"}}/>
						</div>
					</center>
				</div>
			);
		}
	}
}

export default ImageCropper;
