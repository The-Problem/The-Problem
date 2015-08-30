LimePHP.register("problem", function() {
    // timeago all spans with 'timeago' class
    $("span.timeago").timeago();
    // if autosize is imported, make all textareas autosize
    if (typeof autosize === 'function') autosize($('textarea'));
});