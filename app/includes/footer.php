
<script src="/js/jquery-3.7.1.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
jQuery(function($){
    if (!document.URL.includes("/admin/")) {
        var current_heat = <?= intval(Heat::current_heat() ? Heat::current_heat() : 0) ?>;
        (function worker() {
            $.ajax(
                {
                url: '/ping.php',
                dataType: 'json',
                success: function(data) {
                    if (current_heat !== data.current_heat) {
                        current_heat = data.current_heat;
                        console.log("Current Heat changed to :", data.current_heat);
                        window.location.reload();
                    }
                },
                complete: function() {
                    setTimeout(worker, 2000);
                }
            });
        })();
    }

});
</script>
</body>
</html>