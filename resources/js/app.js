require('./bootstrap');
window.$ = window.jQuery = require('jquery');

function ajaxSync(url, type = "POST", data = null) {
    disableButtons();

    $.ajax({
        type: type,
        url: url,
        data: data,
        datatype: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(data) {
            output = jQuery.parseJSON(data);
            updateDisplay(output);
            if(output.status && output.status == 'syncing') {
                syncStatus();
            } else {
                enableButtons();
            }

        },
        error: function(data) {
            enableButtons();
            alert("Something went wrong");
        },
    });
}

function syncStatus() {
    $.ajax({
        type: 'GET',
        url: '/sync/status',
        datatype: "json",
        success: function(data) {
            $('.alert-warning').css('display', 'none');
            output = jQuery.parseJSON(data);
            updateDisplay(output);
            if(output.status && output.status == 'syncing') {
                if(output.current) {
                    $('.alert-syncing .alert-message').text('Syncing ' + output.current);
                }
                disableButtons();
                setTimeout(function(){
                    syncStatus();
                }, 5000);
            } else {
                enableButtons();
            }
        },
        error: function(data) {
            enableButtons();
            $('.alert-syncing').css('display', 'none');
            $('.alert-warning').css('display', 'flex');
            $('.alert-warning').text('Could not get sync status');

        },
    });
}

function disableButtons() {
    $('button:not(.accordion-control)').prop('disabled', true);
    $('.alert-syncing').css('display', 'flex');
}

function enableButtons() {
    $('.alert-syncing').css('display', 'none');
    $('.alert-message').text('Syncing...please wait');
    $('button').prop('disabled', false);
}

function updateDisplay(output) {
    if (window.location.pathname.includes('/directories') && output.current != 'Broken Photos') {
        if($('.directory-row').length != output.directories.length) {
            $('.alert-syncing').css('display', 'none');
            alert("Number of directories has changed" + "\n\nClick 'OK' to refresh the page");
            location.reload();
        }

        $('#directories-count').text(output.directories.length);
        $('#directories-synced-count').text(output.directories.filter (dir => dir.status === 'synced').length);
        $('#directories-ignored-count').text(output.directories.filter (dir => dir.status === 'ignored').length);
        $('#photo-count').text(output.photo_count);

        output.directories.forEach(function(dir) {
            let target = $('#directory-' + dir.id);
            if(dir.status == 'ignored') {
                target.addClass('disabled');
                target.find('.button-sync').text('Sync');
                target.find('.ignored-hidden').hide();
            } else {
                let target = $('#directory-' + dir.id);
                target.removeClass('disabled');
                target.find('.button-sync').text(dir.status == 'synced' || dir.status == 'syncing' ? 'Resync' : 'Sync');
                target.find('.ignored-hidden').show();
            }
            target.find('.directory-status').text(dir.status);
            target.find('.directory-photo-count').text(dir.photo_count);
            target.find('.directory-total-photo-count').text(dir.total_photo_count);
        });
    } else if(window.location.pathname.includes('/photos/broken') && output.broken_photo_ids) {
        $('#broken-count').text(output.broken_photo_ids.length);
        $('.broken-row').each(function() {
            if(!output.broken_photo_ids.includes(parseInt($(this).attr('id'), 10))) {
                $(this).remove();
            }
        });
    }
}

window.runSync = function(url) {
    ajaxSync(url)
}

window.ignoreDirectory = function(url) {

    if(confirm('Are you sure you?\n\nChild directories will be ignored and synced photos will be removed.')) {
        ajaxSync(url, "PATCH", {"status": true})
    }
}


$(document).ready(function(){
    syncStatus();
    $('.accordion-control').on('click', function(e){
        e.preventDefault();
        let open = !$(this).next('.accordion-panel').hasClass('open');

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

