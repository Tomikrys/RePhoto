<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photoNew \common\models\Photo */
/* @var $photoOld \common\models\Photo */

$this->title = 'Align new rephoto';
$this->h1 = $this->title;
$this->bodyClasses[] = 'place-add-photo';

\frontend\assets\PhotoAlignerAsset::register($this);
?>

<?= $this->render('partial/stepper', ['active' => 2]) ?>


    <section class="container">
        <div class="white-box">
            <h2>Manual alignment</h2>
            <p>Select at least 4 same points by clicking on specific position on one photo and then other. To place
                marker more
                accurate click and hold on point to show magnifier glass.</p>
        </div>
    </section>

    <section class="container">
        <button id="delete-points-btn" class="btn btn-primary">Delete all points</button>
    </section>

    <section class="container main-info">
        <div class="row">
            <div class="col-md-6">
                <div id="old-photo-wrapper" class="valign-wrapper magnifier-wrapper">
                    <img id="old-photo" class="photo" src="<?= $photoOld->getUrl() ?>">
                    <div id="old-points-sm" class="points-sm"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="new-photo-wrapper" class="valign-wrapper magnifier-wrapper">
                    <img id="new-photo" class="photo" src="<?= $photoNew->getUrl() ?>">
                    <div id="new-points-sm" class="points-sm"></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="<?= \yii\helpers\Url::to(['/place/upload-photo', 'id_place' => $place->id]) ?>"
               class="btn btn-back">Upload
                another photo</a>
            <button class="btn waves-effect waves-light align-btn">Align</button>
        </div>

    </section>

    <script>

        function magnify(imgID, zoom) {
            var img, glass, w, h, bw, hline, vline, glassImg, points;

            vline = document.createElement("hr");
            vline.classList.add('vline');

            hline = document.createElement("hr");
            hline.classList.add('hline');

            img = document.getElementById(imgID);
            /*create magnifier glass:*/
            glass = document.createElement("div");
            glassImg = document.createElement("img");
            glass.setAttribute("class", "magnifier-glass");
            /*add lines*/
            glass.appendChild(vline);
            glass.appendChild(hline);

            points = document.createElement("div");
            points.id = imgID + '-points-lg';
            points.classList.add('points-lg');
            glass.appendChild(points);

            /*insert magnifier glass:*/
            img.parentElement.insertBefore(glass, img);
            /*set background properties for the magnifier glass:*/
            glassImg.src = img.src;
            glassImg.id = imgID + "-mg";
            glass.appendChild(glassImg);
            //glass.style.backgroundRepeat = "no-repeat";
            //glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
            bw = 3;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;
            /*execute a function when someone moves the magnifier glass over the image:*/
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);
            /*and also for touch screens:*/
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);

            function moveMagnifier(e) {
                var pos, x, y;
                /*prevent any other actions that may occur when moving over the image*/
                e.preventDefault();
                /*get the cursor's x and y positions:*/
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                /*prevent the magnifier glass from being positioned outside the image:*/
                if (x > img.width) {
                    x = parseInt(img.width);
                }
                if (x < 0) {
                    x = 0;
                }
                if (y > img.height) {
                    y = img.height;
                }
                if (y < 0) {
                    y = 0;
                }
                /*set the position of the magnifier glass:*/
                glass.style.left = parseInt(x - w) + "px";
                glass.style.top = parseInt(y - h) + "px";
                /*display what the magnifier glass "sees":*/
                glassImg.style.left = parseInt((-1 * ((x) / img.width) * glassImg.width) + w) + "px";
                glassImg.style.top = parseInt((-1 * ((y) / img.height) * glassImg.height) + h) + "px";

                points.style.position = 'absolute';
                points.style.left = parseInt((-1 * ((x) / img.width) * glassImg.width) + w) + "px";
                points.style.top = parseInt((-1 * ((y) / img.height) * glassImg.height) + h) + "px";

                glassImg.dataset.leftsm = parseInt(x);
                glassImg.dataset.topsm = parseInt(y);
                glassImg.dataset.leftlg = parseInt((((x) / img.width) * glassImg.width));
                glassImg.dataset.toplg = parseInt((((y) / img.height) * glassImg.height));
            }

            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                /*get the x and y positions of the image:*/
                a = img.getBoundingClientRect();
                /*calculate the cursor's x and y coordinates, relative to the image:*/
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                /*consider any page scrolling:*/
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x: x, y: y};
            }
        }

        magnify("old-photo", 1);
        magnify("new-photo", 1);
        var aligner;
    </script>

<?php
$points = json_encode($points);
$alignUrl = \yii\helpers\Url::to(['/place/review-photo', 'id_photo' => $photoNew->id]);
$this->registerJs(<<<JS
    $(document).on('click', '.card img', function() {
        $("#active-photo").attr('src', $(this).data('url-original'));
    });

    $('.photos .toggle-editor-btn').on('click', function(e){
        e.preventDefault();
        var photo = $(this).parents('.photo');
        console.log(photo);
        editorListToggle(photo);
    });
    
    
    $(".scroll-right-btn").on('click', function(){
       var box = $(this).siblings('.horizontal-scroll-box');
       box.animate({scrollLeft: box[0].scrollLeft + 200}, 400);
    });

    $(".scroll-left-btn").on('click', function(){
       var box = $(this).siblings('.horizontal-scroll-box');
       box.animate({scrollLeft: box[0].scrollLeft - 200}, 400);
    });
    
    $('.align-btn').on('click', function(){
        var points = aligner.getOriginalPoints();
        
        if (points.old.length < 4){
            alert('Set minimal 4 points.');
        } else {
            $.post('{$alignUrl}', {points: points}, function(url){
                window.location.href = url;
            });   
        }
    });
    
    aligner = new PhotoAligner({$points});
JS
);