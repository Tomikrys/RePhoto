<?php
/* @var $this yii\web\View */
/* @var $places array */
/* @var $lastFetch boolean */
/* @var $page integer */
?>

<?php if (empty($places)): ?>
    <? if ($page == 1): ?>
        <div class="col m12 no-result">
            <i class="material-icons">place</i>
            <p>V této oblasti se nenacházejí žádné fotografie.</p>
            <p>Přidejte nové nebo vyhledejte jíné místo na mapě.</p>
        </div>
    <? endif; ?>

<?php else: ?>
    <?php foreach ($places as $place): ?>

        <div class="card col"
             data-lat="<?= $place['latitude'] ?>"
             data-lng="<?= $place['longitude'] ?>"
        >
            <div class="card-image">
                <img class="oldest partial" src="<?= $place['oldest_photo']['thumbnail_url'] ?>">
                <img class="newest" src="<?= $place['newest_photo']['thumbnail_url'] ?>">
                <span data-badge-caption=""
                      class="new badge oldest"><?= Yii::$app->formatter->asDate($place['oldest_photo']['captured_at'], 'Y') ?></span>
                <span data-badge-caption=""
                      class="new badge newest"><?= Yii::$app->formatter->asDate($place['newest_photo']['captured_at'], 'Y') ?></span>
            </div>
            <div class="card-content">
                <p><?= $place['name'] ?></p>
            </div>
            <div class="card-action">
                <a href="<?= \yii\helpers\Url::to(['/place/view', 'id' => $place['id']]) ?>" class="teal-text"
                   data-pjax="0">detail</a>
            </div>
        </div>

    <?php endforeach; ?>
<?php endif; ?>

<?php if (!$lastFetch): ?>
    <!-- TODO nefunguje -->
    <div class="actions text-center">
        <?= \yii\helpers\Html::button('load more', [
            'id' => 'btn-next-preview',
            'class' => 'btn waves-effect waves-light',
            'data' => [
                'page' => $page + 1,
            ],
        ]) ?>
    </div>
<?php endif; ?>

<? $this->registerJs(<<<JS
    // TODO refactor events to places
   
    $('.photo.preview').on('click', function(e){
         if($(e.target).hasClass('wishlist-btn') || $(e.target).hasClass('add-to-editor-btn')){
            //e.preventDefault();
            return;
        }
        
        showPhotoDetail(this.dataset.id); 
    });

    $('.photo .wishlist-btn').on('click', function(e){
        wishListToggle($(this).parents('.photo'));
    });
    
    $('.photo .add-to-editor-btn').on('click', function(e){
        var photo = $(this).parents('.photo');
        editorListToggle(photo);
        
    });
    
    $(document).on('mouseenter', '.card', function(){
        showActiveMarker(this.dataset.lat, this.dataset.lng);
    });
    
    $(document).on('mouseleave', '.card', function(){
        hideActiveMarker();
    });
    
    $("#btn-next-preview").on('click', function(){
        refreshMapAndSideNav(this.dataset.page); 
    });
JS
); ?>