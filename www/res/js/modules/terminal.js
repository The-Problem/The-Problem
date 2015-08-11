LimePHP.register("module.terminal", function() {
    var /*prompt = "$ ",*/ submitting = false, memory = [];

    var $document = $(document), $window = $(window), $body = $("body");


    $document.on("focus", ".terminal input", function() {
        var $terminal = $(this).parents(".terminal");
        var $cursor = $terminal.find(".cursor");
        $cursor.css("display", "inline");
    });

    $document.on("blur", ".terminal input", function() {
        var $terminal = $(this).parents(".terminal");
        var $cursor = $terminal.find(".cursor");
        $cursor.css("display", "none");
    });

    $document.on("click", ".terminal", function() {
        var $input = $(this).children("input");
        $input.focus();
        var tmpStr = $input.val();
        $input.val("");
        $input.val(tmpStr);
    });

    var draggingElt = false;

    $document.on("mousedown", ".terminal .slider", function() {
        if (draggingElt) return;
        $body.addClass("dragging");
        draggingElt = $(this).parents(".terminal");
    });

    $document.on("mousemove", function(e) {
        if (!draggingElt) return;

        var yPos = e.clientY;
        if (e.clientY < 0) yPos = 0;

        var bottomY = $window.innerHeight() - yPos;
        draggingElt.height(bottomY);

        var $output = $(".terminal .output");
        $output.scrollTop($output[0].scrollHeight);
    });

    $document.on("mouseup", function() {
        $body.removeClass("dragging");
        draggingElt = false;
    });

    $document.on("keydown", ".terminal input", function(e) {
        var $this = $(this);

        if (submitting) {
            $this.val("");
            return;
        }

        var $terminal = $this.parents(".terminal");
        var $prompt = $terminal.find(".prompt");

        var $output = $terminal.find(".output");
        var $out = $terminal.find(".out");
        var $in = $terminal.find(".in");

        if (e.keyCode === 13) {
            memory.push($this.val());
            $this.val("");

            submitting = true;

            $out.html($out.html() + "<span style='color:#0F0'>" + $prompt.text() + "</span>" + $in.html() + "\n");
            $output.scrollTop($output[0].scrollHeight);

            $in.text("");
            $prompt.text("");

            var r = LimePHP.request("post", LimePHP.path("ajax/terminal/run"), { code: memory.join("\n") }, "json");
            r.error = function() {
                memory = [];
                $out.html($out.html() + r.ajax.responseText + "\n");
                $output.scrollTop($output[0].scrollHeight);
                $prompt.text("$ ");
                submitting = false;
            };
            r.success = function(response) {
                if (response.success === false) {
                    //$out.html($out.html() + "\n");
                    $prompt.text("... ");
                } else {
                    $prompt.text("$ ");
                    memory = [];
                    $out.html($out.html() + response.output + "\n");
                    $output.scrollTop($output[0].scrollHeight);
                }
                submitting = false;
            };
        } else {
            setTimeout(function() {
                $in.html(Prism.highlight($this.val(), Prism.languages.php));
                $output.scrollTop($output[0].scrollHeight);
            }, 0);
        }
    });
});