$(document).ready(function() {
    $('.staff-avatar').on('click', function() {
        if ($(this).attr('data-active') == "false") { $(this).attr('data-active', 'true') } else { $(this).attr('data-active', 'false') }
        // refresh
        $('.staff-avatar').each(function() {
                if ($(this).hasClass("staff-active")) {
                    $(this).removeClass("staff-active")
                }
            })
            // active
        if ($(this).attr('data-active') == "true") {
            $(this).addClass("staff-active")
        }
    })
})