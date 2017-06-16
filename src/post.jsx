import React from 'react';
import ReactDOM from 'react-dom';
import ImageCropper from './imageCropper.jsx';

ReactDOM.render(<ImageCropper ref={(input) => {document.imageCropper = input;}}/>, document.getElementById("tester"));

window.addEventListener("mouseup", function(event){document.imageCropper.dragEnd(event);});
