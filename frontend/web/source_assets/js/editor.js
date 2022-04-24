function Canvas(editor_id) {
    var thisObject = this;

    this.editor = document.getElementById(editor_id);
    this.canvas = document.getElementById(editor_id + '-canvas');
    this.context = this.canvas.getContext('2d');

    this.width = 0;
    this.height = 0;

    this.id_picture = null; // ID from photo_edited table

    this.historyBox = this.editor.getElementsByClassName('history-box')[0];
    this.historyUndoBtn = this.editor.getElementsByClassName('undo-btn')[0];
    this.historyRedoBtn = this.editor.getElementsByClassName('redo-btn')[0];

    this.scale = 1;
    this.zoomInput = this.editor.getElementsByClassName('zoom')[0];
    this.zoomInBtn = this.editor.getElementsByClassName('zoom-in-btn')[0];
    this.zoomOutBtn = this.editor.getElementsByClassName('zoom-out-btn')[0];

    this.moveBtn = this.editor.getElementsByClassName('move-btn')[0];
    this.resizeBtn = this.editor.getElementsByClassName('resize-btn')[0];
    this.rotateBtn = this.editor.getElementsByClassName('rotate-btn')[0];
    this.pathBtn = this.editor.getElementsByClassName('path-btn')[0];
    this.pathPoints = [];
    this.activeTool = "move";
    this.toolsBox = this.editor.getElementsByClassName('active-tools')[0];

    this.saveBtn = this.editor.getElementsByClassName('save-image')[0];

    this.imagesBox = this.editor.getElementsByClassName('images-box')[0];
    $(this.imagesBox).sortable({
        update: function () {
            thisObject.refreshObjectsOrder();
        }
    });

    this.objects = [];
    this.historyPointer = 0;
    this.history = [{ state: "start", objects: [] }];

    this.needRefresh = false;
    this.editingObject = false;
    this.dragging = false;
    this.draggingX = false;
    this.draggingY = false;

    this.canvas.addEventListener('click', function (e) {
        var pos = thisObject.getMouseCanvasPosition(e);

        var object = thisObject.selectObject(pos.x, pos.y);

        if (thisObject.editingObject !== false && thisObject.editingObject !== object) {
            thisObject.editingObject.blurHandler(thisObject);
        }

        if (thisObject.activeTool === "path") {
            thisObject.drawPath(pos);
        }

        if (object !== false) {
            object.clickHandler(e, thisObject);
            thisObject.editingObject = object;

            if (thisObject.activeTool === "resize") {
                thisObject.editingObject.editing = true;
                thisObject.needRefresh = true;
            }

        } else {
            thisObject.editingObject = false;
        }

        if (thisObject.needRefresh) {
            thisObject.refresh();
        }
    });

    this.canvas.addEventListener('mousedown', function (e) {
        var pos = thisObject.getMouseCanvasPosition(e);

        var object = thisObject.selectObject(pos.x, pos.y);

        if (thisObject.editingObject !== false && thisObject.editingObject !== object) {
            thisObject.editingObject.blurHandler(thisObject);
        }

        if (object !== false) {
            object.clickHandler(e, thisObject);
            thisObject.editingObject = object;
        } else {
            thisObject.editingObject = false;
        }

        if (thisObject.editingObject !== false && thisObject.editingObject === object) {
            switch (thisObject.activeTool) {
                case 'move':
                    thisObject.editingObject.moving = true;
                    thisObject.dragging = true;
                    thisObject.draggingX = pos.x;
                    thisObject.draggingY = pos.y;
                    break;
                case 'rotate':
                    thisObject.dragging = true;
                    thisObject.editingObject.rotating = true;
                    break;
                case 'resize':
                    thisObject.dragging = true;
                    thisObject.draggingX = pos.x;
                    thisObject.draggingY = pos.y;
                    break;
            }
        }
    });

    this.canvas.addEventListener('mouseup', function (e) {
        if (thisObject.dragging) {
            thisObject.dragging = false;
            if (thisObject.activeTool === "move") {
                thisObject.addHistoryState('move');
            } else if (thisObject.activeTool === "rotate") {
                thisObject.addHistoryState('rotate');
            }
        }
    });

    this.canvas.addEventListener('mousemove', function (e) {
        if (thisObject.dragging) {
            var pos = thisObject.getMouseCanvasPosition(e);
            thisObject.editingObject.moveHandler(e, pos, thisObject);
            thisObject.draggingX = pos.x;
            thisObject.draggingY = pos.y;
        }

        if (thisObject.needRefresh) {
            thisObject.refresh();
        }
    });

    this.zoomOutBtn.addEventListener('click', function () {
        var scale = (thisObject.scale - 0.1).toFixed(2);
        if (scale < 0.01) {
            scale = 0.01;
        }

        thisObject.changeScale(scale);
        thisObject.refreshZoomInput();
    });

    this.zoomInBtn.addEventListener('click', function () {
        var scale = (parseFloat(thisObject.scale) + 0.1).toFixed(2);

        thisObject.changeScale(scale);
        thisObject.refreshZoomInput();
    });

    this.zoomInput.addEventListener('change', function () {
        var value = (Math.round(parseInt(this.value)) / 100).toFixed(2);

        if (!isNaN(value) && value >= 0.01) {
            thisObject.changeScale(value);
        }

        thisObject.refreshZoomInput();
    });

    this.historyUndoBtn.addEventListener('click', function () {
        thisObject.undo();
    });

    this.historyRedoBtn.addEventListener('click', function () {
        thisObject.redo();
    });

    this.moveBtn.addEventListener('click', function () {
        thisObject.changeTool('move');
    });

    this.resizeBtn.addEventListener('click', function () {
        thisObject.changeTool('resize');
    });

    this.rotateBtn.addEventListener('click', function () {
        thisObject.changeTool('rotate');
    });

    this.saveBtn.addEventListener('click', function () {
        thisObject.saveImage();
    });

    this.pathBtn.addEventListener('click', function () {
        //thisObject.startPath();
        thisObject.changeTool('path');
    });

    $("#editor .history").on('click', 'li', function () {
        thisObject.changeHistoryPointer(this.dataset.index);
    });

    $(this.imagesBox).on('change', '.opacity', function () {
        thisObject.changeObjectOpacity(this.parentElement.dataset.index, this.value, this);
    });

    $(this.imagesBox).on('click', '.transform-btn', function () {
        thisObject.transformObject(this.parentElement.dataset.index, this);
    });
}

Canvas.prototype.drawPath = function (pos) {
    this.pathPoints.push(pos);

    if (this.pathPoints.length > 1) {
        this.context.beginPath();
        var lastPoint = this.pathPoints[this.pathPoints.length - 2];
        this.context.moveTo(lastPoint.x, lastPoint.y);
        this.context.lineTo(pos.x, pos.y);
        this.context.stroke();
        this.context.closePath();

        // if the user clicked back in the original point then complete the clip
        var dx = pos.x - this.pathPoints[0].x;
        var dy = pos.y - this.pathPoints[0].y;
        if (dx * dx + dy * dy < 5 * 5) {
            this.endPath();
        }
    } else {
        // this.context.beginPath();
        this.context.arc(pos.x, pos.y, 5, 0, 2 * Math.PI);
        this.context.stroke();
        // this.context.closePath();
    }
};

Canvas.prototype.endPath = function () {
    // https://stackoverflow.com/questions/27213413/canvas-cropping-images-in-different-shapes

    // calculate the size of the user's clipping area
    var minX = 10000;
    var minY = 10000;
    var maxX = -10000;
    var maxY = -10000;
    for (var i = 1; i < this.pathPoints.length; i++) {
        var p = this.pathPoints[i];
        if (p.x < minX) {
            minX = p.x;
        }
        if (p.y < minY) {
            minY = p.y;
        }
        if (p.x > maxX) {
            maxX = p.x;
        }
        if (p.y > maxY) {
            maxY = p.y;
        }
    }

    minX -= 20;
    maxX += 20;
    minY -= 20;
    maxY += 20;

    var width = maxX - minX;
    var height = maxY - minY;

    if (width > this.width) {
        width = this.width;
    }
    if (height > this.height) {
        height = this.height;
    }

    var mask = document.createElement('canvas');
    var mctx = mask.getContext('2d');

    mask.width = this.width;
    mask.height = this.height;

    mctx.shadowColor = 'black';
    mctx.shadowOffsetX = 0;
    mctx.shadowOffsetY = 0;
    mctx.shadowBlur = 20;

    mctx.beginPath();
    mctx.moveTo(this.pathPoints[0].x, this.pathPoints[0].y);
    for (var i = 1; i < this.pathPoints.length; i++) {
        var p = this.pathPoints[i];
        mctx.lineTo(p.x, p.y);
    }
    mctx.closePath();
    mctx.strokeSyle = "#000";
    mctx.fillStyle = 'black';
    mctx.fill();
    mctx.fill();
    mctx.fill();
    mctx.fill();
    mctx.fill();
    mctx.fill();

    this.refresh();

    // this.context.clearRect(0, 0, this.width, this.height);
    this.context.globalCompositeOperation = 'destination-in';
    this.context.drawImage(mask, 0, 0);

    // create a new canvas
    var c = document.createElement('canvas');
    var cx = c.getContext('2d');

    // resize the new canvas to the size of the clipping area
    c.width = width;
    c.height = height;

    // draw the clipped image from the main canvas to the new canvas
    cx.drawImage(this.canvas, minX, minY, width, height, 0, 0, width, height);

    // create a new Image() from the new canvas
    var image_src = new CanvasImage(c.toDataURL());
    image_src.width = c.width;
    image_src.height = c.height;
    image_src.x = minX;
    image_src.y = minY;
    image_src.center = { x: image_src.width / 2, y: image_src.height / 2 };
    this.addObject(image_src);

    // clear the previous points
    this.pathPoints = [];
};

Canvas.prototype.changeTool = function (type) {
    var lastActive = this.toolsBox.getElementsByClassName('active')[0];
    lastActive.classList.remove('active');

    switch (this.activeTool) {
        case 'resize':
            this.editingObject.editing = false;
            break;
        case 'rotate':
            this.editingObject.rotating = false;
            break;
        case 'move':
            this.editingObject.moving = false;
            break;
    }

    if (this.editingObject) {
        this.editingObject.blurHandler(this);
        this.refresh();
    }

    this.activeTool = type;
    switch (type) {
        case 'move':
            this.moveBtn.classList.add('active');
            break;
        case 'resize':
            this.resizeBtn.classList.add('active');
            break;
        case 'rotate':
            this.rotateBtn.classList.add('active');
        case 'path':
            this.pathBtn.classList.add('active');
            break;
    }

    //this.refresh();
};

Canvas.prototype.transformObject = function (index, input) {
    var l = this.objects.length;
    this.objects[l - index - 1].toggleActiveImage(this, input);
};

Canvas.prototype.changeObjectOpacity = function (index, opacity, input) {
    opacity = (parseInt(opacity) / 100).toFixed(2);

    if (isNaN(opacity) || opacity < 0 || opacity > 1) {
        input.value = (this.objects[index].opacity * 100) + "%";
        return;
    }

    this.objects[index].opacity = opacity;

    input.value = (opacity * 100) + "%";

    this.refresh();
};

Canvas.prototype.refreshObjectsOrder = function () {
    var children = this.imagesBox.childNodes;
    var l = children.length;

    var newObjects = [];
    for (var i = l - 1; i >= 0; --i) {
        newObjects.push(this.objects[children[i].dataset.index]);
        children[i].dataset.index = l - i - 1;
    }

    this.objects = newObjects;
    this.refresh();
    this.addHistoryState('layer order');
};

Canvas.prototype.refreshZoomInput = function () {
    this.zoomInput.value = Math.round(this.scale * 100) + '%';
};

Canvas.prototype.changeScale = function (scale) {
    if (scale === 0.1) {
        this.zoomOutBtn.classList.add('disabled');
    } else {
        this.zoomOutBtn.classList.remove('disabled');
    }

    this.scale = scale;
    this.resizeCanvas();
};

Canvas.prototype.resizeCanvas = function () {
    this.canvas.width = this.width * this.scale;
    this.canvas.height = this.height * this.scale;
    this.context.scale(this.scale, this.scale);

    this.refresh();
};

Canvas.prototype.getMouseCanvasPosition = function (e) {
    var scrollLeft = $('.editor-content').scrollLeft();
    var scrollTop = $('.editor-content').scrollTop();

    var x = e.clientX + scrollLeft - (scrollLeft > 0 ? $('.editor-content').offset().left : $(this.canvas).offset().left);
    var y = e.clientY + scrollTop - (scrollTop > 0 ? $('.editor-content').offset().top : $(this.canvas).offset().top);

    return { x: x, y: y };
};

Canvas.prototype.selectObject = function (x, y) {
    var l = this.objects.length;
    for (var i = l - 1; i >= 0; --i) {
        if (this.objects[i].contains(x, y)) {
            return this.objects[i];
        }
    }

    return false;
};

Canvas.prototype.draw = function () {
    // draw objects into canvas
    var objectsLength = this.objects.length;
    console.log("objectsLength: " + objectsLength);
    for (var i = 0; i < objectsLength; ++i) {
        this.objects[i].draw(this.context);
    }

    this.needRefresh = false;
};

/**
 * Redraw all objects into canvas.
 */
Canvas.prototype.refresh = function () {
    // clear canvas
    this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

    this.draw();
};

Canvas.prototype.refreshHistoryBox = function () {
    this.historyBox.textContent = "";

    var l = this.history.length;
    for (var i = 0; i < l; ++i) {
        var li = document.createElement('li');
        li.textContent = this.history[i].state;
        li.dataset.index = i;

        // set last items as active
        if (i === this.historyPointer) {
            li.classList.add('active');
        } else if (i > this.historyPointer) {
            li.classList.add('inactive');
        }

        this.historyBox.append(li);
    }

    this.historyBox.scrollTop = this.historyBox.scrollHeight;
};

Canvas.prototype.changeHistoryPointer = function (value) {
    value = parseInt(value);

    if (this.historyPointer === value || isNaN(value)) {
        return;
    }

    if (value < this.historyPointer) {
        // undo

        if (value < 0) {
            return;
        }

        this.historyPointer = value;

        if (this.historyPointer === 0) {
            this.historyUndoBtn.classList.add('disabled');
        }

        this.historyRedoBtn.classList.remove('disabled');

    } else if (this.historyPointer < value) {
        // redo
        var l = this.history.length;

        if (value >= l) {
            return;
        }

        this.historyPointer = value;

        if (this.historyPointer === (l - 1)) {
            this.historyRedoBtn.classList.add('disabled');
        }

        this.historyUndoBtn.classList.remove('disabled');

    }

    this.objects = this.history[this.historyPointer].objects;

    this.refreshHistoryBox();
    this.refreshImagesBox();
    this.refresh();
};

Canvas.prototype.undo = function () {
    this.changeHistoryPointer(this.historyPointer - 1);
};

Canvas.prototype.redo = function () {
    this.changeHistoryPointer(this.historyPointer + 1);
};

Canvas.prototype.addHistoryState = function (state) {
    this.historyPointer++;
    this.historyUndoBtn.classList.remove('disabled');

    if (this.historyPointer < this.history.length) {
        this.history = this.history.slice(0, this.historyPointer);
    }

    var l = this.objects.length;
    var objectsCopy = [];
    for (var i = 0; i < l; ++i) {
        objectsCopy.push(Object.assign(new CanvasImage(), this.objects[i]));
    }

    this.history.push({ state: state, objects: objectsCopy });
    this.refreshHistoryBox();
};

Canvas.prototype.refreshImagesBox = function () {
    this.imagesBox.textContent = '';

    var l = this.objects.length;
    for (var i = l - 1; i >= 0; i--) {

        var list = document.createElement('li');


        // append opacity input
        var input = document.createElement('input');
        input.min = 0;
        input.max = 100;
        input.value = "100%";
        input.classList.add('opacity');

        var visible = document.createElement("input");
        visible.classList.add("visibility_checkbox");
        visible.setAttribute("type", "checkbox");
        visible.checked = true;
        visible.this = this;
        visible.i = i;
        visible.input = input;
        visible.addEventListener("click", function (evt) {
            if (this.checked) {
                evt.currentTarget.this.changeObjectOpacity (evt.currentTarget.i, "100", evt.currentTarget.input);
            } else {
                evt.currentTarget.this.changeObjectOpacity (evt.currentTarget.i, "0", evt.currentTarget.input);
            }
            evt.currentTarget.this.addHistoryState('hide layer');
        });

        list.setAttribute('id', this.objects[i].id);
        list.dataset.index = i;

        var del = document.createElement("button");
        del.classList.add("delete_button");
        del.setAttribute('id', this.objects[i].id);
        del.innerHTML = "❌";
        del.this = this;
        del.i = i;
        del.addEventListener("click", function (evt) {
            evt.currentTarget.this.remove_layer(evt.currentTarget.i);
        });

        list.appendChild(visible);
        list.appendChild(this.objects[i].image);
        list.appendChild(input);
        list.appendChild(del);


        this.imagesBox.append(list);
    }
};

Canvas.prototype.remove_layer = function (i) {
    this.objects.splice(i, 1);
    this.addHistoryState('remove layer');
    this.refreshImagesBox();
    this.refresh();
}

/**
 * Add actual state into history, clear objects and redraw canvas.
 */
Canvas.prototype.clear = function () {
    this.objects = [];
    this.refresh();
};

Canvas.prototype.addObject = function (object) {
    var canvas = this;

    object.image.onload = function () {
        canvas.objects.push(object);

        if (object.width > canvas.width || canvas.width === 0) {
            canvas.width = object.width;
        }
        if (object.height > canvas.height || canvas.height === 0) {
            canvas.height = object.height;
        }

        canvas.resizeCanvas();
        canvas.addHistoryState('open');
        canvas.refreshImagesBox();
        $(canvas.imagesBox).sortable("refresh");
    };
};

// async/promise function for retrieving image dimensions for a URL
// from https://stackoverflow.com/questions/8903854/check-image-width-and-height-before-upload-with-javascript
function imageSize(url) {
    const img = document.createElement("img");

    const promise = new Promise((resolve, reject) => {
        img.onload = () => {
            // Natural size is the actual image size regardless of rendering.
            // The 'normal' `width`/`height` are for the **rendered** size.
            const width = img.naturalWidth;
            const height = img.naturalHeight;

            // Resolve promise with the width and height
            resolve({ width, height });
        };

        // Reject promise on error
        img.onerror = reject;
    });

    // Setting the source makes it start downloading and eventually call `onload`
    img.src = url;

    return promise;
}

function CanvasImage(src, canvas, photo_id) {
    console.log("src " + src)
    console.log("photo_id " + photo_id)
    this.id = null;
    this.x = 0;
    this.y = 0;
    this.editing = false;
    this.image = new Image();
    this.image.src = src;
    this.opacity = 1;

    this.rotation = 0;

    this.moving = false;
    this.rotating = false;
    this.resizing = false;

    this.initialDraw = true;

    this.transformationControlsVisible = false;

    this.activeImage = "main";
    this.imageMain = this.image;
    this.imageTransformed = null;

    this.width = this.image.width;
    this.height = this.image.height;
    this.center = { x: this.width / 2, y: this.height / 2 };

    // (async () => {
    //     console.log("src: " + src);
    //     const imageDimensions = await imageSize(src);
    //     if (this.width === undefined || this.width < imageDimensions.width) {
    //         this.width = imageDimensions.width;
    //     }
    //     if (this.height === undefined || this.height < imageDimensions.height) {
    //         this.height = imageDimensions.height;
    //     }
    //     this.center = {x: this.width / 2, y: this.height / 2};
    //     this.id = photo_id;
    //     canvas.addObject(this);
    //     console.log("added " + photo_id)
    // })(src, this, canvas, photo_id);
}

CanvasImage.prototype.toggleActiveImage = function (context, input) {
    var progress = $('#main-progress');
    progress.show();

    if (this.activeImage === "main") {
        input.classList.add('active');
        if (this.imageTransformed === null) {
            var image = this;

            var id_main = null;
            for (var i = 0, n = context.objects.length; i < n; i++) {
                if (context.objects[i].id !== this.id) {
                    id_main = context.objects[i].id;
                    break;
                }
            }

            $.get('/editor/homography', { id_main: id_main, id_transform: this.id }, function (data) {
                image.imageTransformed = new Image();
                image.imageTransformed.src = data.url;

                image.imageTransformed.onload = function () {
                    image.activeImage = "transformed";
                    image.image = image.imageTransformed;
                    context.refresh();
                    progress.hide();
                }
            });

            return;
        }

        this.image = this.imageTransformed;
        this.activeImage = "transformed";
    } else {
        input.classList.remove('active');
        this.image = this.imageMain;
        this.activeImage = "main";
    }

    context.refresh();
    progress.hide();
};

Canvas.prototype.saveImage = function () {
    var canvasObject = this;
    yii.confirm('Do you really want to save image into your profile?', function () {
        $.post('/editor/save-picture', {
            id: canvasObject.id_picture,
            data: canvasObject.canvas.toDataURL("image/png")
        }, function (data) {
            canvasObject.id_picture = data.id;
            refreshFlashMessages();
        });
    });
};

// CanvasImage.prototype.getCanvasScale = function (context) {
//     return Math.min((context.width / this.image.width), (context.height / this.image.height))
// };

CanvasImage.prototype.draw = function (context) {
    console.log(this);
    var opacity = null;
    if (this.opacity !== 1) {
        opacity = context.globalAlpha;
        context.globalAlpha = this.opacity;
    }

    context.save();

    console.log("this.center: " + this.center);
    context.translate(this.x + this.center.x, this.y + this.center.y);
    context.rotate(this.rotation);

    context.drawImage(this.image, -this.width / 2, -this.height / 2, this.width, this.height);

    context.restore();

    if (opacity !== null) {
        context.globalAlpha = opacity;
    }

    if (this.editing) {
        this.showTransformationControls(context);
    }
};

CanvasImage.prototype.clickHandler = function (e, canvas) {

};

CanvasImage.prototype.blurHandler = function (canvas) {
    this.transformationControlsVisible = false;
    this.editing = false;
    this.rotating = false;
    this.moving = false;
    this.resizing = false;

    canvas.needRefresh = true;
};

CanvasImage.prototype.showTransformationControls = function (context) {
    context.save();

    context.translate(this.x + this.center.x, this.y + this.center.y);
    context.rotate(this.rotation);

    var w = -this.width / 2;
    var h = -this.height / 2;

    // draw borders
    context.strokeRect(w, h, this.width, this.height);

    var tl = [w, h];
    var tr = [w + this.width, h];
    var bl = [w, h + this.height];
    var br = [w + this.width, h + this.height];

    var rectSize = 5;

    // draw border rectangles
    context.strokeRect(tl[0] - rectSize, tl[1] - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect(tr[0] - rectSize, tr[1] - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect(bl[0] - rectSize, bl[1] - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect(br[0] - rectSize, br[1] - rectSize, 2 * rectSize, 2 * rectSize);

    // draw center rectangles
    context.strokeRect((this.width / 2 + tl[0]) - rectSize, tr[1] - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect(br[0] - rectSize, (this.height / 2 + tr[1]) - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect((this.width / 2 + bl[0]) - rectSize, br[1] - rectSize, 2 * rectSize, 2 * rectSize);
    context.strokeRect(bl[0] - rectSize, (this.height / 2 + tl[1]) - rectSize, 2 * rectSize, 2 * rectSize);

    this.transformationControlsVisible = true;

    context.restore();
};

CanvasImage.prototype.checkAnchors = function (x, y) {
    var w = this.x;
    var h = this.y;

    var tl = [w, h];
    var t = [w + (this.width / 2), h];
    var tr = [w + this.width, h];
    var r = [w + this.width, h + (this.height / 2)];
    var bl = [w, h + this.height];
    var b = [w + (this.width / 2), h + this.height];
    var br = [w + this.width, h + this.height];
    var l = [w, h + (this.height / 2)];

    var rectSize = 5;

    // top left
    if ((x >= (tl[0] - rectSize) && x <= (tl[0] + rectSize))
        && ((y >= (tl[1] - rectSize) && y <= (tl[1] + rectSize)))) {
        return 1;
    }

    // center
    if ((x >= (t[0] - rectSize) && x <= (t[0] + rectSize))
        && ((y >= (t[1] - rectSize) && y <= (t[1] + rectSize)))) {
        return 2;
    }

    // top right
    if ((x >= (tr[0] - rectSize) && x <= (tr[0] + rectSize))
        && ((y >= (tr[1] - rectSize) && y <= (tr[1] + rectSize)))) {
        return 3;
    }

    // right
    if ((x >= (r[0] - rectSize) && x <= (r[0] + rectSize))
        && ((y >= (r[1] - rectSize) && y <= (r[1] + rectSize)))) {
        return 4;
    }

    // bottom right
    if ((x >= (br[0] - rectSize) && x <= (br[0] + rectSize))
        && ((y >= (br[1] - rectSize) && y <= (br[1] + rectSize)))) {
        return 5;
    }

    // bottom
    if ((x >= (b[0] - rectSize) && x <= (b[0] + rectSize))
        && ((y >= (b[1] - rectSize) && y <= (b[1] + rectSize)))) {
        return 6;
    }

    // bottom left
    if ((x >= (bl[0] - rectSize) && x <= (bl[0] + rectSize))
        && ((y >= (bl[1] - rectSize) && y <= (bl[1] + rectSize)))) {
        return 7;
    }

    // left
    if ((x >= (l[0] - rectSize) && x <= (l[0] + rectSize))
        && ((y >= (l[1] - rectSize) && y <= (l[1] + rectSize)))) {
        return 8;
    }

    return 0;
};

/**
 * Check if object this coordinates.
 * @param x horizontal position
 * @param y vertical position
 */
CanvasImage.prototype.contains = function (x, y) {
    // check editing anchors
    if (this.editing) {
        this.resizing = this.checkAnchors(x, y);
        if (this.resizing > 0) {
            return true;
        }
    }

    // check image
    return (x >= this.x) && (x <= (this.x + this.width)) && (y >= this.y) && (y <= (this.y + this.height));
};

CanvasImage.prototype.moveHandler = function (e, pos, canvas) {
    if (this.moving) {
        this.x += pos.x - canvas.draggingX;
        this.y += pos.y - canvas.draggingY;

    } else if (this.rotating) {
        var cx = pos.x - this.center.x;
        var cy = pos.y - this.center.y;
        this.rotation = Math.atan2(cy, cx);
    } else if (this.resizing > 0) {
        switch (this.resizing) {
            case 1:
                this.x += pos.x - canvas.draggingX;
                this.y += pos.y - canvas.draggingY;
                this.width -= pos.x - canvas.draggingX;
                this.height -= pos.y - canvas.draggingY;
                this.center.x = this.width / 2;
                this.center.y = this.height / 2;
                break;
            case 2:
                this.y += pos.y - canvas.draggingY;
                this.height -= pos.y - canvas.draggingY;
                this.center.y = this.height / 2;
                break;
            case 3:
                this.y += pos.y - canvas.draggingY;
                this.width += pos.x - canvas.draggingX;
                this.height -= pos.y - canvas.draggingY;
                this.center.x = this.width / 2;
                this.center.y = this.height / 2;
                break;
            case 4:
                this.width += pos.x - canvas.draggingX;
                this.center.x = this.width / 2;
                break;
            case 5:
                this.width += pos.x - canvas.draggingX;
                this.height += pos.y - canvas.draggingY;
                this.center.x = this.width / 2;
                this.center.y = this.height / 2;
                break;
            case 6:
                this.height += pos.y - canvas.draggingY;
                this.center.y = this.height / 2;
                break;
            case 7:
                this.x += pos.x - canvas.draggingX;
                this.width -= pos.x - canvas.draggingX;
                this.height += pos.y - canvas.draggingY;
                this.center.x = this.width / 2;
                this.center.y = this.height / 2;
                break;
            case 8:
                this.x += pos.x - canvas.draggingX;
                this.width -= pos.x - canvas.draggingX;
                this.center.x = this.width / 2;
                break;
        }
    }

    canvas.needRefresh = true;
};