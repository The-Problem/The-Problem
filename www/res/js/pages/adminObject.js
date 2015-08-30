LimePHP.register("pages.adminObject", function() {
    var $permissionSelects = $("section select"),
        $selector = $(".username-selector");

    $permissionSelects.on('focus', function() {
        var $this = $(this);
        $this.data('previous', $this.val());
    });

    $permissionSelects.on('change', function() {
        var $this = $(this);
        var $section = $this.parents("section");

        $this.attr('disabled', true);

        var permissionName = $section.data("permission");
        var objectId = $section.data("object");
        var newRank = $this.val();

        var r = LimePHP.request("get", LimePHP.path("ajax/admin/permissionRank"), {
            permission: permissionName,
            object: objectId,
            rank: newRank
        }, "json");

        r.error = function() {
            $this.val($this.data('previous'));
        };
        r.complete = function() {
            $this.attr('disabled', false);
            $this.data('previous', $this.val());
        };
    });


    var currentSelected = false, usernameSearch;
    var canHide = true;

    var $searchField = $(".add-user");

    $searchField.on('keydown', function(e) {
        var $input = $(this);

        if (e.keyCode === 13) {
            $(".username-selector tr:first-child").click();
            return;
        }

        setTimeout(function() {
            var $section = $input.parents("section");
            var permissionName = $section.data("permission");
            var objectId = $section.data("object");
            var term = $input.val();
            showResultsFor(term, $input, permissionName, objectId, $section);
        }, 0);
    });
    $searchField.on('blur', function() {
        if (canHide) $selector.hide();
    });
    $searchField.on('focus', function() {
        var $this = $(this);
        $this.keydown();
    });

    $(document).on("mousedown", ".username-selector", function() {
        canHide = false;
    });
    $(document).on("mouseup", ".username-selector", function() {
        canHide = true;
    });

    $(document).on("click", ".username-selector tr", function() {
        if (currentSelected === false) return;

        canHide = true;
        $selector.hide();

        var $this = $(this);

        var $table = currentSelected.find(".user-list");
        var $input = currentSelected.find(".add-user");
        $input.attr('disabled', true);

        var userImage = $this.children('.user-image').css('background-image'),
            userName = $this.children('.user-name').text();

        var dataUsername = $this.data('username');

        var permissionName = currentSelected.data("permission");
        var objectId = currentSelected.data("object");

        var r = LimePHP.request("post", LimePHP.path("ajax/admin/permissionUser"), {
            permission: permissionName,
            object: objectId,
            username: dataUsername,
            remove: false
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
        };

        r.complete = function() {
            $input.val("");
            $input.attr('disabled', false);
            $input.focus();
        };
    });

    $(document).on("click", ".user-remove", function() {
        var $remove = $(this);
        var $row = $remove.parents("tr");
        var $section = $remove.parents("section");
        if ($row.data('submitting')) return;
        $row.data('submitting', true);

        var permissionName = $section.data("permission");
        var objectId = $section.data("object");
        var username = $row.data("username");

        var r = LimePHP.request("post", LimePHP.path("ajax/admin/permissionUser"), {
            permission: permissionName,
            object: objectId,
            username: username,
            remove: true
        }, "json");

        r.success = function() {
            $row.remove();
        };

        r.complete = function() {
            $row.data('submitting', false);
        };
    });

    function showResultsFor(term, $input, permission, object, $section) {
        if (!$input.data("usernameCache")) $input.data("usernameCache", {});
        var usernameCache = $input.data("usernameCache");

        currentSelected = $section;

        if (term in usernameCache) showUserSelector(usernameCache[term], $input);
        else {
            if (usernameSearch) usernameSearch.cancel();
            usernameSearch = LimePHP.request("get", LimePHP.path("ajax/admin/permissionSearch"), {
                query: term,
                permission: permission,
                object: object
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