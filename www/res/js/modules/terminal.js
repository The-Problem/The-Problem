LimePHP.register("module.terminal", function() {
    var /*prompt = "$ ",*/ submitting = false, memory = [];

    $(document).on("click", ".terminal", function() {
        var $input = $(this).children("input");
        $input.focus();
        var tmpStr = $input.val();
        $input.val("");
        $input.val(tmpStr);
    });

    $(document).on("keydown", ".terminal input", function(e) {
        var $this = $(this);

        if (submitting) {
            $this.val("");
            return;
        }

        var $terminal = $this.parents(".terminal");
        var $prompt = $terminal.find(".prompt");

        var $out = $terminal.find(".out");
        var $in = $terminal.find(".in");

        if (e.keyCode === 13) {
            memory.push($this.val());
            $this.val("");

            submitting = true;

            $out.html($out.html() + "<span style='color:#0F0'>" + $prompt.text() + "</span>" + $in.text());
            $terminal.scrollTop($terminal[0].scrollHeight);

            $in.text("");
            $prompt.text("");

            var r = LimePHP.request("post", LimePHP.path("ajax/terminal/run"), { code: memory.join("\n") }, "json");
            r.error = function() {
                $out.html($out.html() + "\n" + r.ajax.responseText + "\n");
                $prompt.text("$ ");
                submitting = false;
            };
            r.success = function(response) {
                if (response.success === false) {
                    $out.html($out.html() + "\n");
                    $prompt.text("... ");
                } else {
                    $prompt.text("$ ");
                    memory = [];
                    $out.html($out.html() + "\n" + response.output + "\n");
                    $terminal.scrollTop($terminal[0].scrollHeight);
                }
                submitting = false;
            };
        } else {
            setTimeout(function() { $in.text($this.val()); }, 0);
        }
    });
});