LimePHP.register("problem", function() {
    if (typeof $.prototype.timeago === 'function') $("span.timeago").timeago();
    if (typeof autosize === 'function') autosize($('textarea'));
});