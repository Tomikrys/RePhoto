<?php
/* @var $this yii\web\View */

$this->title = "Editor";
$this->h1 = $this->title;
$this->bodyClasses[] = 'editor';

$this->registerCss(<<<CSS
    #editor {
        height: 100%;
        background: #333;
    }
    
    .editor-main {
        display: flex;
        height: 100%;
        justify-content: space-between;
        height: calc(100% - 34px);
        overflow: hidden;
    }

    .editor-top-toolbar {
        background-color: #555;
        height: 34px;
        width: 100%;
    }
    
    .editor-top-toolbar .tools {
        display: inline-block;
        margin: 0;
    }

    .editor-top-toolbar .tools:first-child{
        padding-left: 30px;
    }
    
    .editor-top-toolbar .tools li {
        float: left;
        height: 34px;
    }
   
    .editor-top-toolbar .tools li i {
        padding: 5px;
        color: #ddd;
    }
    
    .editor-top-toolbar .tools li i:hover {
        color: #eee;
        transition: background-color 0.2s ease;
    }
    
    .editor-top-toolbar .tools li a:active i {
        color: #eee;
        background-color: #777;
    }
    
    .editor-top-toolbar .tools .disabled i {
        color: #999;
    }
    .editor-top-toolbar .tools .disabled i:hover {
        color: #999;
    }
    
    .editor-top-toolbar .tools .disabled:active i {
        color: #999;
        background-color: #555;
    }
    
    .editor-top-toolbar .tools .divider {
        width: 1px;
        height: 20px;
        display: inline-block;
        margin: 7px 7px;
    }

    .editor-top-toolbar .tools input {
        color: #eee;
        height: 16px;
        margin: 5px 0;
        border: 0;
        background-color: #333;
        font-size: 14px;
        padding: 4px 8px;
    }

    .editor-top-toolbar .tools .zoom {
        width: 35px;
    }

    .editor-right-toolbar {
        background-color: #555;
        height: 100%;
        /*width: 300px;*/
        min-width: 240px;
        border-top: 1px solid #333;
    }
    
    .editor-right-toolbar > div {
        padding: 5px 10px;
        color: #eee;
        font-size: 14px;
        border-bottom: 1px solid #333;
        overflow: hidden;
    }
    
    .editor-right-toolbar .history-box {
        margin: 0;
        height: 185px;
        
        overflow: auto;
        max-height: 100%;
        margin-right: -100px;
        padding-right: 100px;
    }
    
    .editor-right-toolbar .history-box li {
        padding: 4px 8px;
        border-top: 1px solid #333;
    }
    
    .editor-right-toolbar .history-box li:hover {
        background-color: #666;
        cursor: pointer;
    }
    
    .editor-right-toolbar .history-box li.active {
        background-color: #444;
    }
    
    .editor-right-toolbar .history-box li.inactive {
        color: #999;
    }
    
    .editor-right-toolbar .history-box li.active:hover {
        background-color: #444;
    }
    
    .editor-right-toolbar .images-box li {
        height: 50px;
        padding: 5px;
        cursor: move;
    }
    
    .editor-right-toolbar .images-box li img{
        max-height: 40px;
        max-width: 40px;
        margin-right: 14px;
    }
    
    .editor-right-toolbar .images-box li .opacity {
        display: inline-block;
        width: 35px;
        color: #eee;
        height: 16px;
        margin: 5px 0;
        border: 0;
        background-color: #333;
        font-size: 14px;
        padding: 4px 8px;
    }
    
    .editor-right-toolbar .heading{
        font-size: 16px;
        margin-left: 8px;
        margin-bottom: 4px;
        display: block;
    }

    .editor-content {
        margin: 30px;
        height: calc(100% - 56px);
        width: 100%;
        overflow: auto;
        text-align: center;
    }

    #editor-canvas {
        background-color: #eee;
        margin: 0 15px 15px 0;
        /*width: calc(100% - 300px);*/
        /*height: 100%;*/
    }
CSS
);

?>
    <div id="editor">
        <div class="editor-top-toolbar">
            <ul class="tools">
                <li><a href="#" class="undo-btn disabled"><i class="material-icons">undo</i></a></li>
                <li><a href="#" class="redo-btn disabled"><i class="material-icons">redo</i></a></li>
                <li><span class="divider"></span></li>
            </ul>
            <ul class="tools">
                <li><a href="#" class="zoom-out-btn"><i class="material-icons">zoom_out</i></a></li>
                <li><input class="zoom" name="zoom" value="100%"></li>
                <li><a href="#" class="zoom-in-btn"><i class="material-icons">zoom_in</i></a></li>
                <li><span class="divider"></span></li>
            </ul>
            <ul class="tools active-tools">
                <li><i class="material-icons move-btn active">open_with</i></li>
                <li><i class="material-icons resize-btn">photo_size_select_large</i></li>
                <li><i class="material-icons rotate-btn">rotate_left</i></li>
                <li><i class="material-icons path-btn">format_shapes</i></li>
                <li><i class="material-icons download-image">file_download</i></li>
                <li><i class="material-icons save-image">save</i></li>
            </ul>
        </div>
        <div class="editor-main">
            <div class="editor-content">
                <div>
                    <canvas id="editor-canvas" width="1200" height="480"></canvas>
                </div>
            </div>

            <div class="editor-right-toolbar">
                <div class="history">
                    <span class="heading">History</span>
                    <ul class="history-box"></ul>
                </div>

                <div class="images">
                    <span class="heading">Images</span>
                    <ul class="images-box ui-sortable"></ul>
                </div>
            </div>
        </div>
    </div>
<script>
    var canvas;
</script>
<?php
$imagesJS = '';
foreach ($photos as $photo) {
    $imagesJS .= <<<JS

        var image_src = new CanvasImage('{$photo->url}');
        image_src.id = {$photo->id}
        canvas.addObject(image_src);

JS;
}


$this->registerJs(<<<JS

    $(".download-image").on('click', function(){
        exportCanvasAsPNG();
    });

    function exportCanvasAsPNG() {
        var canvasElement = document.getElementById("editor-canvas");
        var imgURL = canvasElement.toDataURL("image/png");
        var a = document.createElement('a');
        a.download = "new_file.png";
        a.href = imgURL;
    
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    $(document).ready(function() {
        canvas = new Canvas('editor');

        {$imagesJS}
    });
JS
);