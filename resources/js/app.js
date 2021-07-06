require('./bootstrap');
window.$ = window.jQuery = require('jquery');

function ajaxSync(url, type = "POST", data = null) {
    $('button').prop('disabled', true);
    $('.alert-syncing').css('display', 'flex');
    $('body').css('cursor', 'wait');

    $.ajax({
        type: type,
        url: url,
        data: data,
        datatype: "json",
        success: function(data) {
            $('.alert-syncing').css('display', 'none');
            $('button').prop('disabled', false);
            $('body').css('cursor', 'inherit');

            output = jQuery.parseJSON(data);

            if($('.directory-row').length != output.directories.length) {
                alert("Number of directories has changed" + "\n\nClick 'OK' to refresh the page");
                location.reload();
            }

            $('#directories-count').text(output.directories.length);
            $('#directories-synced-count').text(output.directories.filter (dir => dir.status === 'synced').length);
            $('#directories-ignored-count').text(output.directories.filter (dir => dir.status === 'ignored').length);
            $('#picture-count').text(output.picture_count);

            output.directories.forEach(function(dir) {
                let target = $('#directory-' + dir.id);
                if(dir.status == 'ignored') {
                    target.addClass('disabled');
                    target.find('.ignored-hidden').hide();
                    target.find('.ignored-show').show();
                } else {
                    let target = $('#directory-' + dir.id);
                    target.removeClass('disabled');
                    target.find('.ignored-hidden').show();
                    target.find('.ignored-show').hide();
                }
                target.find('.directory-status').text(dir.status);
                target.find('.directory-picture-count').text(dir.picture_count);
                target.find('.directory-total-picture-count').text(dir.total_picture_count);

            });

        },
        error: function(data) {
            $('.alert-syncing').css('display', 'none');
            $('button').prop('disabled', false);
            $('body').css('cursor', 'inherit');
            alert("Something went wrong");
        },
    });
}


function runSync(url) {
    ajaxSync(url)
}

function ignoreDirectory(url) {

    if(confirm('Are you sure you?\n\nChild directories will be ignored and synced pictures will be removed.')) {
        ajaxSync(url, "PATCH", {"ignore": true})
    }
}


$(document).ready(function(){
    $('.accordion-control').on('click', function(e){
        e.preventDefault();
        let open = !$(this).next('.accordion-panel').hasClass('open');

        console.log($(this).closest('.accordion'));

        $(this).closest('.accordion')
            .find('.open')
            .removeClass('open')
            .slideToggle();
        if(open) {
            $(this)
                .next('.accordion-panel')
                .toggleClass('open')
                .slideToggle();
        }
    })
});

