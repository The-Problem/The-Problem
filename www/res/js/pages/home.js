LimePHP.register("page.home", function() {
    var $search = $(".search-box"),
        $searchable = $(".searchable"),
        $sections = $searchable.children("section"),
        $none = $searchable.children(".none");

    function search(val) {
        if (val === "") {
            $sections.css("display", "");
            $none.css("display", "none");
        } else {
            val = val.toLowerCase();
            var amount = 0;
            $sections.each(function (index, elt) {
                var $elt = $(elt);

                var name = $elt.data("name");
                if (name.indexOf(val) === -1) $elt.css("display", "none");
                else {
                    $elt.css("display", "");
                    amount++;
                }
            });
            console.log(amount);

            if (amount) $none.css("display", "none");
            else $none.css("display", "");
        }
    }

    search($search.val());

    $search.on("keydown", function() {
        setTimeout(function() {
            search($search.val());
        }, 0);
    });
});