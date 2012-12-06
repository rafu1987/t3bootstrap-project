$(document).ready(function() {
    $("a.btn-submit").click(function(e) {
        var $this = $(this);
        $this.parents("form").submit();
    });
});