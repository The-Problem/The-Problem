LimePHP.register("page.admin", function() {
    var specialHandlers = {
        "overview-visibility": {
            visibility: {
                post: function(value, $parent) {
                    $parent.find(".column.registration input").attr('disabled', value === "private");
                }
            }
        }
    };

    var $inputs = $("section input");

    $inputs.on('change', function() {
        var $this = $(this);
        var $section = $this.parents("section");

        var type = $section.data("type");
        var name = $this.attr("name");

        var handlers = {};
        if (specialHandlers[type] && specialHandlers[type][name]) handlers = specialHandlers[type][name];
        if (handlers.pre) handlers.pre($this.val(), $section);

        var value = $this.val();

        var $disabled = $section.find('input');

        $disabled.attr('disabled', true);

        var hooks = LimePHP.request("post", LimePHP.path("ajax/admin/update"), {
            type: type,
            name: name,
            value: value
        }, "json");

        hooks.success = function(data) {
            $this.val(data.value);
        };

        hooks.error = function(err, data) {
            if (data.login) window.location.href = LimePHP.path("login");
            else if (data.home) window.location.href = LimePHP.path();
            else if (data.sudo) window.location.href = LimePHP.path("sudo");
        };

        hooks.complete = function() {
            $disabled.attr('disabled', false);
            if (handlers.post) handlers.post($this.val(), $section);
        };
    });


    var $rows = $(".section-list .table-row");

    $rows.children(".overview").on('click', function() {
        var $overview = $(this);
        var $row = $overview.parent(".table-row");
        var $options = $row.children(".options");

        $options.show();
        $overview.hide();

        if (!$row.data("loaded")) {
            LimePHP.library("modules").get("adminSection", $options, {
                id: $row.data("id")
            }, false, false, function() {
                $options.find(".close").on('click', function() {
                    $options.hide();
                    $overview.show();
                });
            });
            $row.data("loaded", true);
        }
    });

    var usernameSearch, $selector = $(".username-selector");

    $(document).on("keydown", ".developer-list input", function(e) {
        var $input = $(this);

        setTimeout(function() {
            var $row = $input.parents(".table-row");

            var term = $input.val();
            showResultsFor(term, $input, $row.data('id'));
        }, 0);
    });
    $(document).on("blur", ".developer-list input", function() {
        $selector.hide();
    });
    $(document).on("focus", ".developer-list input", function() {
        var $this = $(this);
        $this.keydown();
    });

    function showResultsFor(term, $input, rowId) {
        if (!$input.data("usernameCache")) $input.data("usernameCache", {});
        var usernameCache = $input.data("usernameCache");

        if (term in usernameCache) showUserSelector(usernameCache[term], $input);
        else {
            if (usernameSearch) usernameSearch.cancel();
            usernameSearch = LimePHP.request("get", LimePHP.path("ajax/admin/devSearch"), {
                query: term,
                section: rowId
            }, "json");

            usernameSearch.success = function(data) {
                usernameCache[term] = data;
                showUserSelector(usernameCache[term], $input);
                $input.data("lastList", data);
            };
        }
    }

    function showUserSelector(items, $input) {
        var offset = $input.offset();
        var height = $input.height();

        var top = offset.top + height + "px";
        var left = offset.left + "px";
        $selector.css({
            top: top,
            left: left
        });

        $selector.html(items.join(""));
        $selector.show();
    }
});