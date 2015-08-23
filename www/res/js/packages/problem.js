LimePHP.register("problem", function() {
    if ($.prototype.timeago) $("span.timeago").timeago();
    if (autosize) autosize($('textarea'));
});