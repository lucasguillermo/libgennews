function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}

function down(downbutton) {
    $.ajax({
        url: 'down.php',
        data: {
            dest: $(downbutton).parent().find('input').val(),
            md5: $(downbutton).attr('md5')
        },
        context: this,
        success: function(data, statusText, xhr) {
            setTimeout (getStatus, getRandomInt(2000,4000), downbutton);
        }
    });
}

function getStatus(downbutton) {
    $.ajax({
        url: 'getstatus.php',
        data: {
            md5: $(downbutton).attr('md5')
        },
        context: this,
        success: function(data, statusText, xhr) {
            if (data == 'Saved!') {
                $(downbutton).text(data).css({'color':'black', 'background-color':'red', 'text-decoration':'none', 'cursor':'text'});
            } else if (data.slice(0,6) == 'ERROR ') {
                downbutton.pause = downbutton.pause + getRandomInt(30, 120);
                $(downbutton).text(data + ' (retrying in ' + downbutton.pause + 's)');
                setTimeout (down, downbutton.pause * 1000, downbutton);
            } else {
                if (data == '') { data = '[Empty]'; }
                $(downbutton).text(data).append('<span class="downer">⬇︎</span>').css({'text-decoration':'none', 'cursor':'text'});
                downbutton.pause = 0;
                setTimeout (getStatus, getRandomInt(2000,4000), downbutton);
            }
        },
    });
}

$(function() {
    $('td.2getstatus').each(function() {
        var downbutton = $(this).find('div.down');
        setTimeout(getStatus, getRandomInt(1000,3000), downbutton, 0);
    });
    $('div.lookup').click(function() {
        $(this).css('text-decoration', 'underline');
        $.ajax({
            url: 'lookup.php',
            data: {
                isbn: $(this).attr('isbn')
            },
            context: this,
            success: function(data, statusText, xhr) {
                $(this).css({'color':'black', 'background-color':'#075', 'text-decoration':'none'});
                $(this).parent().find('input').val(data + '.' + $(this).attr('extension'));
            },
            error: function() {
                $(this).css({'text-decoration':'line-through'});
            }
        });
    });
    $('div.fuzzfind').click(function() {
        $(this).css('text-decoration', 'underline');
        $.ajax({
            url: 'fuzzfind.php',
            data: {
                term: $(this).parent().find('input').val()
            },
            context: this,
            success: function(data, statusText, xhr) {
                $(this).css({'color':'black', 'background-color':'#660', 'text-decoration':'none'});
                $(this).parent().append($('<pre>' + data + '</pre>'));
            }
        });
    });
    $('div.down').click(function() {
        $(this).css('text-decoration', 'underline').unbind();
        downbutton = $(this);
        downbutton.pause = 0;
        down(downbutton);
    });
});