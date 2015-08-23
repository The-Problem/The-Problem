LimePHP.register("module.comments", function() {
    var $newComment = $(".comment.new");
    var $commentText = $newComment.find("textarea");
    var $onEnter = $newComment.find("input[type=checkbox]");

    $commentText.on('keydown', function(e) {
        console.log("HI");
        if ($onEnter.is(':checked') && e.keyCode === 13) submit();
    });

    function submit() {
        $commentText.prop('disabled', true);
        var text = $commentText.val();

        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/addComment"), {
            bug: $newComment.data('bugId'),
            value: text
        }, "json");

        r.success = function(data) {
            $(data.html).hide().insertBefore($newComment).show().find("span.timeago").timeago();
        };

        r.complete = function() {
            $commentText.val("");
            $commentText.prop('disabled', false);
        };
    }
});