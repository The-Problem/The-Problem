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


    var $tableRows = $(".table-row");
    var $tableSearch = $(".table-search input");
    $tableSearch.on('keydown', function(e) {
        if (e.keyCode == 13) {
            $tableRows.filter(':visible').first().children('.overview').click();
            return;
        }

        setTimeout(function() {
            var term = $.trim($tableSearch.val().toLowerCase());

            $tableRows.each(function() {
                var $this = $(this);
                if ($this.data('search').toLowerCase().indexOf(term) === -1 || $this.hasClass('new-section')) $this.hide();
                else $this.show();
            });
        }, 0);
    });

    var $rows = $(".section-list .table-row");

    $(document).on('click', ".section-list .table-row .overview",  function() {
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

        function removeRow() {
            $row.remove();

            var $devCount = $section.find(".overview .developers");
            var newAmount = parseInt($devCount.text()) - 1;
            $devCount.text(newAmount);

            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");

            $input.data("usernameCache", {});
        }

        if (sectionId) {
            var r = LimePHP.request("get", LimePHP.path("ajax/admin/removeDev"), {
                section: sectionId,
                username: username
            }, "json");

            r.success = removeRow;

            r.complete = function () {
                $row.data('submitting', false);
            };
        } else removeRow();
    });
    $(document).on("click", ".username-selector tr", function() {
        if (currentSelected === false) return;

        canHide = true;
        $selector.hide();

        var $this = $(this);
        var $row;
        if (currentSelected) $row = $(".table-row[data-id=" + currentSelected + "]");
        else $row = $(".table-row.new-section");
        var $table = $row.find(".developer-list table");
        var $input = $row.find(".developer-list input");
        var $devCount = $row.find(".overview .developers");
        $input.attr('disabled', true);

        var userImage = $this.children('.user-image').css('background-image'),
            userName = $this.children('.user-name').text();

        var dataUsername = $this.data('username');

        var newAmount = parseInt($devCount.text()) + 1;

        function addRow() {
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

            $devCount.text(newAmount);

            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");
        }

        if (currentSelected) {
            var r = LimePHP.request("get", LimePHP.path("ajax/admin/addDev"), {
                section: currentSelected,
                username: dataUsername
            }, "json");

            r.success = addRow;

            r.complete = function () {
                $input.val("");
                $input.attr('disabled', false);
                $input.focus();
            };
        } else {
            addRow();
            $input.val("");
            $input.attr('disabled', false);
            $input.focus();
        }
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

    var $addSection = $(".add-section");
    $addSection.on('click', function() {
        var $addSection = $(this);
        $addSection.hide();

        var color = Math.floor(Math.random() * 15) + 1;

        var $newRow = $('<div class="table-row new-section">' +
            '<div class="overview" style="display:none">' +
                '<p class="name"></p>' +
                '<p class="description"></p>' +
                '<p class="developers">0</p>' +
                '<p class="bugs"><em class="none">No bugs</em></p>' +
            '</div><div class="options" style="display:block">' +
                '<div class="section-header">' +
                    '<div class="section-tile color-' + color + '"></div>' +
                    '<div class="right-column">' +
                        '<div class="section info">' +
                            '<h2><input type="text" class="new-section-name" placeholder="Section Name" /></h2>' +
                            '<textarea class="new-section-description" placeholder="Section Description"></textarea>' +
                        '</div>' +
                        '<div class="section developer-list">' +
                            '<h3>Developers</h3>' +
                            '<p class="total">Developers are the people who work on your section, and have some ' +
                            'elevated privelidges.</p>' +
                            '<table></table>' +
                            '<input type="text" placeholder="Add a developer..." />' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="section buttons">' +
                    '<button class="cancel-add-section">Cancel</button>' +
                    '<button class="green finish-add-section" data-color="' + color + '">Apply</button>' +
                '</div>' +
            '</div>' +
        '</div>');
        $(".section-list").append($newRow);
        autosize($newRow.find('textarea'));
        $newRow.find("input.new-section-name").focus();
    });
    $(document).on('click', '.cancel-add-section', function() {
        $(".table-row.new-section").remove();
        $addSection.show();
    });
    $(document).on('keydown', '.new-section-name', function() {
        var $this = $(this);
        setTimeout(function() {
            if ($.trim($this.val()).length) $this.removeClass("error");
        }, 0);
    });
    $(document).on('click', '.finish-add-section', function() {
        var $this = $(this);

        var $name = $(".new-section-name"), name = $name.val();
        if (!$.trim(name).length) {
            $name.addClass("error");
            return;
        }

        var $description = $(".new-section-description"), description = $description.val();
        var $devRows = $(".new-section .developer-list table tr");

        var devs = $devRows.map(function() {
            return $(this).data('username');
        }).toArray();

        var $buttons = $(".new-section .buttons");

        var $inputs = $([$name, $description, ".new-section .developer-list input"]);
        $inputs.attr('disabled', true);
        $buttons.hide();

        var r = LimePHP.request("post", LimePHP.path("ajax/admin/addSection"), {
            name: name,
            description: description,
            devs: devs,
            color: $this.data("color")
        }, "json");

        r.success = function(data) {
            var $newRow = $("<div class='table-row'></div>");
            $newRow.data("id", data.id);
            $newRow.data("search", name.toLowerCase());

            var $name = $("<p class='name'></p>").text(name);
            var $description = $("<p class='description'></p>").text(description);
            var $developers = $("<p class='developers'></p>").text(devs.length);
            if (!devs.length) {
                $developers.addClass("highlight");
                $newRow.addClass("highlight");
            }

            var $bugs = $("<p class='bugs'>No bugs</p>");
            var $overview = $("<div class='overview'></div>");
            $overview.append($name).append($description).append($developers).append($bugs);

            var $options = $("<div class='options' style='display:none'></div>");
            $newRow.append($overview).append($options);

            $(".section-list").append($newRow);

            $(".table-row.new-section").remove();
            $addSection.show();
        };
    });

    var $userListSelect = $(".user-list select");

    $userListSelect.on('focus', function() {
        var $this = $(this);
        $this.data('previous', $this.val());
    });
    $userListSelect.on('change', function() {
        var $this = $(this);
        var $row = $this.parents(".table-row");
        $this.attr("class", $this.children(":selected").text().toLowerCase());

        $this.attr('disabled', true);

        var r = LimePHP.request("get", LimePHP.path("ajax/admin/changeRank"), {
            username: $row.data('username'),
            rank: $this.val()
        }, "json");

        var previous = $this.data('previous');

        r.error = function(err) {
            $this.val(previous);
            $this.attr("class", $this.children(":selected").text().toLowerCase());
        };
        r.complete = function() {
            $this.attr('disabled', false);
            $this.data('previous', $this.val());
        };
    });
});