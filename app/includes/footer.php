<script src="/js/jquery-3.7.1.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
jQuery(function($){
    var current_data = {};
    var worker = function() {
        $.ajax({
            url: '/includes/ping.php',
            dataType: 'json',
            success: function(data) {
                if (JSON.stringify(current_data) !== JSON.stringify(data)) {
                    window.location.reload();
                }
            },
            complete: function() {
                setTimeout(worker, 1000);
            }
        });
    };

    $.ajax({
        url: '/includes/ping.php',
        dataType: 'json',
        success: function(data) {
            if (JSON.stringify(current_data) !== JSON.stringify(data)) {
                current_data = data;
            }
        },
        complete: worker
    });
});
</script>
</body>
</html>