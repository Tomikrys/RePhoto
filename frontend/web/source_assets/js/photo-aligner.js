function PhotoAligner(points) {
    this.TURN_BOTH = 0;
    this.TURN_OLD = 1;
    this.TURN_NEW = 2;

    var thisObject = this;

    this.oldPhoto = document.getElementById('old-photo');
    this.oldPhotoGlassImg = document.getElementById('old-photo-mg');
    this.oldPhotoGlass = document.getElementById('old-photo-mg').parentNode;
    this.oldPhotoPointsSmall = document.getElementById('old-points-sm');
    this.oldPhotoPointsLarge = document.getElementById('old-photo-points-lg');

    this.newPhoto = document.getElementById('new-photo');
    this.newPhotoGlassImg = document.getElementById('new-photo-mg');
    this.newPhotoGlass = document.getElementById('new-photo-mg').parentNode;
    this.newPhotoPointsSmall = document.getElementById('new-points-sm');
    this.newPhotoPointsLarge = document.getElementById('new-photo-points-lg');

    this.pointsBox = document.getElementById('points');

    this.deletePointsBtn = document.getElementById('delete-points-btn');;

    this.points = points || [];
    this.activePointIndex = 0;
    this.turn = this.TURN_BOTH;

    this.oldPhoto.addEventListener('click', function (e) {
        thisObject.addOldPoint(e);
    });

    this.newPhoto.addEventListener('click', function (e) {
        thisObject.addNewPoint(e);
    });

    this.deletePointsBtn.addEventListener('click', function (e) {
        thisObject.deleteAllPoints();
    });



    this.movingPointElement = null;
    this.movingPointIndex = null;

    this.pointLabel = 1;

    $(document).on("mousedown", '.point', function (e) {
        e.preventDefault();

        thisObject.movingPointElement = $(this);
        thisObject.movingPointIndex = thisObject.movingPointElement.prevAll().length;
        thisObject.movingPointElement.parents('.magnifier-wrapper').find('.magnifier-glass')[0].style.visibility = 'visible';
        thisObject.movingPointElement.hide();

        thisObject.movingPointElement.parents('.magnifier-wrapper').find('.points-lg')[0].childNodes[thisObject.movingPointIndex].style.visibility = 'hidden';
    });

    $(document).on("mouseup", function (e) {
        if (thisObject.movingPointIndex !== null) {
            thisObject.movingPointElement.parents('.magnifier-wrapper').find('.magnifier-glass')[0].style.visibility = 'hidden';
            thisObject.movingPointElement.show();

            var pointsLg = thisObject.movingPointElement.parents('.magnifier-wrapper').find('.points-lg')[0];
            pointsLg.childNodes[thisObject.movingPointIndex].style.visibility = 'inherit';

            thisObject.movePoint(thisObject.movingPointElement.parent()[0], pointsLg, thisObject.movingPointIndex);

            thisObject.movingPointIndex = null;
        }
    });

    for (var i = 0; i < this.points.length - 1; i++) {

        // compute small
        this.points[i]['old_point']['x1'] = this.points[i]['old_point']['x2'] / $("#old-photo-mg").width() * $("#old-photo").width();
        this.points[i]['old_point']['y1'] = this.points[i]['old_point']['y2'] / $("#old-photo-mg").height() * $("#old-photo").height();
        this.points[i]['new_point']['x1'] = this.points[i]['new_point']['x2'] / $("#new-photo-mg").width() * $("#new-photo").width();
        this.points[i]['new_point']['y1'] = this.points[i]['new_point']['y2'] / $("#new-photo-mg").height() * $("#new-photo").height();
        this.drawOldPoint(this.points[i]);
        this.drawNewPoint(this.points[i]);
        this.pointLabel++;
    }
}

PhotoAligner.prototype.movePoint = function (pointsSm, pointsLg, index) {
    if (pointsSm.id == "old-points-sm") {
        var pos = this.getMouseOldImagePosition();
    } else {
        var pos = this.getMouseNewImagePosition();
    }

    this.points[index].new_point = pos;

    var point = pointsSm.childNodes[index];

    point.style.left = pos.x1 + "px";
    point.style.top = pos.y1 + "px";

    var point = pointsLg.childNodes[index];
    point.style.left = pos.x2 + "px";
    point.style.top = pos.y2 + "px";
}

PhotoAligner.prototype.getMouseImagePosition = function (element) {
    var x1 = element.dataset.leftsm;
    var y1 = element.dataset.topsm;

    var x2 = element.dataset.leftlg;
    var y2 = element.dataset.toplg;

    return {x1: x1, y1: y1, x2: x2, y2: y2};
}

PhotoAligner.prototype.getMouseOldImagePosition = function (e) {
    return this.getMouseImagePosition(this.oldPhotoGlassImg);
};


PhotoAligner.prototype.getMouseNewImagePosition = function (e) {
    return this.getMouseImagePosition(this.newPhotoGlassImg);
};

PhotoAligner.prototype.changeActivePoint = function (index) {
    this.activePointIndex = index;
};

PhotoAligner.prototype.getActivePoint = function () {
    return this.points[this.activePointIndex];
};

PhotoAligner.prototype.addOldPoint = function (e) {
    if (this.turn == this.TURN_NEW) {
        return false;
    }

    var pos = this.getMouseOldImagePosition();

    if (this.turn == this.TURN_BOTH) {
        var point = this.getEmptyPoint();
        point.old_point = pos;
        this.points.push(point);
        this.pointLabel++;
        this.changeActivePoint(this.points.length - 1);

    } else {
        this.points[this.activePointIndex].old_point = pos;
        var point = this.getActivePoint();
        this.changeActivePoint(null);
    }

    this.drawOldPoint(point);

    this.turn = this.turn == this.TURN_OLD ? this.TURN_BOTH : this.TURN_NEW;

    return true;
};


PhotoAligner.prototype.addNewPoint = function (e) {
    if (this.turn == this.TURN_OLD) {
        return false;
    }

    var pos = this.getMouseNewImagePosition();

    if (this.turn == this.TURN_BOTH) {
        var point = this.getEmptyPoint();
        point.new_point = pos;
        this.points.push(point);
        this.pointLabel++;
        this.changeActivePoint(this.points.length - 1);

    } else {
        this.points[this.activePointIndex].new_point = pos;
        var point = this.getActivePoint();
        this.changeActivePoint(null);
    }

    this.drawNewPoint(point);

    this.turn = this.turn == this.TURN_NEW ? this.TURN_BOTH : this.TURN_OLD;

    return true;
};

PhotoAligner.prototype.drawPoint = function (old, point, color) {
    // small point
    var newPoint = document.createElement('div');
    var newSubPoint = document.createElement('div');
    newPoint.classList.add('point');
    newPoint.classList.add('point-sm');
    newPoint.style.left = point.x1 + "px";
    newPoint.style.top = point.y1 + "px";
    newSubPoint.style.borderColor = color;
    newPoint.appendChild(newSubPoint);
    var newLabel = document.createElement('div');
    newLabel.innerText = this.pointLabel;
    newLabel.classList.add('point-label');
    newPoint.appendChild(newLabel);
    if (old) {
        this.oldPhotoPointsSmall.appendChild(newPoint);
    } else {
        this.newPhotoPointsSmall.appendChild(newPoint);
    }

    // large point
    var newPoint = document.createElement('div');
    var newSubPoint = document.createElement('div');
    newPoint.classList.add('point');
    newPoint.classList.add('point-lg');
    newPoint.style.left = point.x2 + "px";
    newPoint.style.top = point.y2 + "px";
    newSubPoint.style.borderColor = color;
    newPoint.appendChild(newSubPoint);
    var newLabel = document.createElement('div');
    newLabel.innerText = this.pointLabel;
    newLabel.classList.add('point-label');
    newPoint.appendChild(newLabel);
    if (old) {
        this.oldPhotoPointsLarge.appendChild(newPoint);
    } else {
        this.newPhotoPointsLarge.appendChild(newPoint);
    }
};

PhotoAligner.prototype.drawOldPoint = function (point) {
    this.drawPoint(true, point.old_point, point.color);
};

PhotoAligner.prototype.drawNewPoint = function (point) {
    this.drawPoint(false, point.new_point, point.color);
};

PhotoAligner.prototype.deletePoint = function (index) {
    this.points = this.points.splice(index, 1);
};


PhotoAligner.prototype.getEmptyPoint = function () {
    return {
        old_point: null,
        new_point: null,
        color: this.getRandomColor()
    };
};

PhotoAligner.prototype.getRandomColor = function () {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
};

PhotoAligner.prototype.getOriginalPoints = function () {
    var l = this.points.length;
    var points = {
        'old': [],
        'new': []
    };

    for (var i = 0; i < l; ++i){
        var point = this.points[i];

        points.old.push([
            point.old_point.x2,
            point.old_point.y2
        ]);

        points.new.push([
            point.new_point.x2,
            point.new_point.y2
        ]);
    }

    return points;
};

PhotoAligner.prototype.deleteAllPoints = function(){
    this.points = [];
    this.activePointIndex = 0;
    this.turn = this.TURN_BOTH;
    this.pointLabel = 0;

    this.oldPhotoPointsSmall.innerText = '';
    this.newPhotoPointsSmall.innerText = '';
    this.oldPhotoPointsLarge.innerText = '';
    this.newPhotoPointsLarge.innerText = '';
};
