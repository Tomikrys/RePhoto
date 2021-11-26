<?php
/* @var $this yii\web\View */
/* @var $photosJSON object */
/* @var $mapSettings array */
/* @var $photos array */
/* @var $categories array */
/* @var $placeFilter \frontend\models\search\PlaceSearch */

$this->title = 'Take Photo';
$this->h1 = $this->title;
$this->bodyClasses[] = 'takephoto';

?>
<!-- Load React. -->
<!-- Note: when deploying, replace "development.js" with "production.min.js". -->
<script src="https://unpkg.com/react@17/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js" crossorigin></script>


<?php
$YourID = 1;
$imgID = 1;
echo \Johnson\JayWebcam::widget(['id' => $YourID, 'imgID' => $imgID]); ?>
<input type="hidden" id="<?php echo $YourID ?>">
<img src="" id="<?php echo $imgID ?>" class="thumbnail">