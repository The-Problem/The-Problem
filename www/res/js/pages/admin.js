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

    var currentSelected = false;

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

        if (e.keyCode === 13) {
            $(".username-selector tr:first-child").click();
            return;
        }

        setTimeout(function() {
            var $row = $input.parents(".table-row");

            var term = $input.val();
            showResultsFor(term, $input, $row.data('id'));
        }, 0);
    });

    var canHide = true;
    $(document).on("blur", ".developer-list input", function() {
        if (canHide) $selector.hide();
    });
    $(document).on("focus", ".developer-list input", function() {
        var $this = $(this);
        $this.keydown();
    });

    $(document).on("mousedown", ".username-selector", function() {
        canHide = false;
    });
    $(document).on("click", ".user-remove", function() {
        var $remove = $(this);
        var $row = $remove.parents(".developer-list tr");
        if ($row.data('submitting')) return;
        $row.data('submitting', true);

        var $section = $row.parents(".table-row");
        var $input = $section.find(".developer-list input");

        var sectionId = $section.data("id");
        var username = $row.data("username");

        var r = LimePHP.request("get", LimePHP.path("ajax/admin/removeDev"), {
            section: sectionId,
            username: username
        }, "json");

        r.success = function() {
            $row.remove();

            var $devCount = $section.find(".overview .developers");
            var newAmount = parseInt($devCount.text()) - 1;
            $devCount.text(newAmount);

            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");

            $input.data("usernameCache", {});
        };

        r.complete = function() {
            $row.data('submitting', false);
        };
    });
    $(document).on("click", ".username-selector tr", function() {
        if (currentSelected === false) return;

        canHide = true;
        $selector.hide();

        var $this = $(this);
        var $row = $(".table-row[data-id=" + currentSelected + "]");
        var $table = $row.find(".developer-list table");
        var $input = $row.find(".developer-list input");
        $input.attr('disabled', true);

        var userImage = $this.children('.user-image').css('background-image'),
            userName = $this.children('.user-name').text();

        var dataUsername = $this.data('username');
        var r = LimePHP.request("get", LimePHP.path("ajax/admin/addDev"), {
            section: currentSelected,
            username: dataUsername
        }, "json");

        r.success = function() {
            var $image = $("<td class='user-image'></td>");
            $image.css("background-image", userImage);
            var $name = $("<td class='user-name'></td>");
            $name.text(userName);
            var $remove = $("<td class='user-remove'><a title='Remove developer' href='javascript:void(0)'><i class='fa fa-times'></i></a></td>");

            var $newRow = $("<tr></tr>");
            $newRow.data("username", dataUsername);
            $newRow.append($image).append($name).append($remove);
            $table.append($newRow);

            $input.data("usernameCache", {});

            var $devCount = $row.find(".overview .developers");
            var newAmount = parseInt($devCount.text()) + 1;
            $devCount.text(newAmount);

            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");
        };

        r.complete = function() {
            $input.val("");
            $input.attr('disabled', false);
            $input.focus();
        };
    });

    function showResultsFor(term, $input, rowId) {
        if (!$input.data("usernameCache")) $input.data("usernameCache", {});
        var usernameCache = $input.data("usernameCache");

        currentSelected = rowId;

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