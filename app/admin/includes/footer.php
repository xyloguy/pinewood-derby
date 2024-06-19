</main>

<script src="/js/jquery-3.7.1.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    jQuery(function($){
        var update_heat_count = function() {
            var c = $("#racers :selected").length;
            if (c < 2) {
                c = 0;
            }
            var r = $('#rounds').val();
            var l = Math.min($('#lanes').val(), c);
            $("#heat-info").text((c * r) + " heats will be generated. Each car will race " + (l * r) + " times.");
        };

        $(".alert:not(.autoclose)").alert();

        setTimeout(function(){
            $(".alert.autoclose").alert('close');
        }, 2500);

        $('button[data-group]').on("click", function(){
            var group = $(this).data('group');
            var active = $(this).is('.active');
            if (group === 'all') {
                if (active) {
                    $('button[data-group]:not([data-group="all"])').filter('.active').click();
                    $(this).text('Select All');

                } else {
                    $('button[data-group]:not([data-group="all"])').filter(':not(.active)').click();
                    $(this).text('Deselect All');
                }
                return;
            }

            $('#racers optgroup').each(function(){
                if($(this).attr('label') === group) {
                    $(this).children('option').each(function(){
                        $(this).prop('selected', !active);
                    });
                }
                $("#racers").trigger("change");
            });
        });

        $("#racers").on("change", function(){
            var c = $("#racers :selected").length;
            var s = 's';
            if (c === 1) {
                s = '';
            }
            $("#racers-help").text(c + " racer" + s + " selected.");
            update_heat_count();
        });

        $("#lanes, #rounds").on("change", function() {
            update_heat_count();
        });

        $("table#heats td[data-racer]").on("click", function(){
            $("table#heats td[data-racer]").removeClass('table-info');
            var c = $(this).data('racer');
            $("table#heats td[data-racer='" + c + "']").addClass('table-info');
        });

        $("table#results td label").on("click", function() {
            var f = $(this).attr('for');
            var id = '#' + f;
            var tr = $(id).parents('tr');
            var total_selects = tr.find('select').length;
            var total_selected = 0;
            tr.find('select :selected').each(function(){
                if ($(this).text().length) {
                    total_selected++;
                }
            });
            var next = total_selects - total_selected;
            if (next === 0) {
                return;
            }
            $(id + " :not(:selected)").filter("[value='" + next + "']").prop('selected', true);
        });
    });


</script>
</body>
</html>