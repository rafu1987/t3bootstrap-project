$(document).ready(function() {
    $("a.tooltip").tooltip({
        bodyHandler: function () {
            return $($(this).attr("href")).html();
        },
        showURL: false
    });
});
