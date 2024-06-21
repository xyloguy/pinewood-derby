<script src="/js/jquery-3.7.1.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
jQuery(function($){
    var current_data = {};
    var previous_data = {};
    var worker = function() {
        $.ajax({
            url: '/includes/ping.php',
            dataType: 'json',
            success: function(data) {
                if (JSON.stringify(current_data) !== JSON.stringify(data)) {
                    if(!String(window.location).includes('heats.php')) {
                        window.location.reload();
                    } else {
                        previous_data = current_data;
                        current_data = data;
                        $('tr').removeClass('bg-info').removeClass('text-dark').removeClass('table-active');
                        if(current_data.current_heat) {
                            if($("#heat" + current_data.current_heat).length === 0) {
                                window.location.reload();
                                return;
                            }
                            $("#heat" + current_data.current_heat).addClass('bg-info').addClass('text-dark')[0].scrollIntoView({
                                behavior: "smooth",
                                block: "start"
                            });
                        }
                        $(".current_heat").text(current_data.current_heat?("Running Heat " +current_data.current_heat):"Finished");
                        if (current_data.current_heat === null && current_data.total_heats !== "0") {
                            $('header')[0].scrollIntoView({
                                behavior: "instant"
                            });
                            window.location.reload();
                        }
                        if (current_data.total_heats !== previous_data.total_heats) {
                            window.location.reload();
                        }
                    }
                }
            },
            complete: function() {
                var time = 2000;
                if(String(window.location).includes('heats.php')) {
                    time = 7000;
                }
                setTimeout(worker, time);
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

    $("table#heats td[data-racer]").on("click", function(){
        $("table#heats td[data-racer]").removeClass('table-info');
        var data = $(this).data('racer');
        var c = 'data-racer="' + data + '"';
        window.location.hash = data;
        $("table#heats td[" + c + "]").addClass('table-info');
    });

    if(window.location.hash.includes("racer-")) {
        $('table#heats td[data-racer="' + window.location.hash.replace('#', '') + '"]').trigger("click");
    }
});
</script>
</body>
</html>