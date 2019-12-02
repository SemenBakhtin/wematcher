var WeMatcherWidget = function(id) {
    var div = document.getElementById(id);
    if(!div){
        return;
    }

    $('#'+id).html('<object id="wematcher_frame" data="https://wematcher.com/WeMatcher/public" style="width:100%;"></object>');

    window.onmessage = function(e) {
        if(e.data.type && e.data.type == 'height') {
            $('#wematcher_frame').height(e.data.value);
        }
    }
}

