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
});