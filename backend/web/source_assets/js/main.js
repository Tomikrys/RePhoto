function url(route){
    if (APP_LANG !== 'en'){
        route = '/' + APP_LANG + route;
    }

    return route;
}

function renderPjaxLoader(selector) {
    $(selector).prepend("<div class=\"pjax-loading\" style=\"width: 100%;height: 100%;position: absolute;background: black;opacity: 0.54;text-align: center;\">\n" +
        " <div class=\"preloader-wrapper active\" style=\"top: 50%;margin-top: -50px;\">\n" +
        "    <div class=\"spinner-layer spinner-yellow-only\">\n" +
        "      <div class=\"circle-clipper left\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div><div class=\"gap-patch\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div><div class=\"circle-clipper right\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div>\n" +
        "    </div>\n" +
        "  </div>\n" +
        "</div>");
}

function hidePjaxLoader(selector) {
    $(selector).find('.pjax-loading').remove();
}

$(".pjax-wrapper")
    .on('pjax:start', function () {
        renderPjaxLoader($(this));
    })
    .on('pjax:end', function () {
        hidePjaxLoader($(this));
    });


var URL_GET_ALL_FLASH_MESSAGES = '/system/get-all-flash-messages';

function showFlashMessages(data) {
    eval(data);
}

function refreshFlashMessages(callback) {
    $.get(url(URL_GET_ALL_FLASH_MESSAGES), function (response) {
        if (response.trim() !== '') showFlashMessages(response);
        if (callback !== undefined) setTimeout(callback, 2000);
    });
}

function editorListToggle(photo, callback) {
    var progress = $('#main-progress');
    progress.show();

    var url1 = '';
    var added = false;

    if (photo.data('editor')) {
        url1 = '/photo/remove-from-editor-list';
        added = false;
    } else {
        url1 = '/photo/add-to-editor-list';
        added = true;
    }

    url1 = url(url1);

    $.post(url1, {id: photo.data('id')}, function () {
        var btn_add = photo.find('.add-btn');
        var btn_remove = photo.find('.remove-btn');
        var badge = $("#editor-images");
        var badge_number = parseInt(badge.text());

        if (added) {
            btn_add.hide();
            btn_remove.show();
            badge_number++;

        } else {
            btn_remove.hide();
            btn_add.show();
            badge_number--
        }

        badge.text(badge_number + "/2");

        photo.data('editor', added);

        refreshFlashMessages();
        progress.hide();

        !callback || callback();

    }).fail(function () {
        progress.hide();
    });
}

function wishListToggle(photo, callback) {
    var progress = $('#main-progress');
    progress.show();

    var url = '';
    var added = false;

    if (photo.data('wishlist')) {
        url = '/photo/remove-from-wish-list';
        added = false;
    } else {
        url = '/photo/add-to-wish-list';
        added = true;
    }

    url = url(url);

    $.post(url, {id: photo.data('id')}, function () {
        var btn = photo.find('.wishlist-btn');
        if (added) {
            btn.text('favorite');
            btn.addClass('remove');
            photo.data('wishlist', true);
        } else {
            btn.text('favorite_border');
            btn.addClass('add');
            photo.data('wishlist', false);
        }
        refreshFlashMessages();
        progress.hide();

        !callback || callback();

    }).fail(function () {
        progress.hide();
    });
}

// https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}