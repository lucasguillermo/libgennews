$(function() {
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
        $(this).css('text-decoration', 'underline');
        $.ajax({
            url: 'down.php',
            data: {
                dest: $(this).parent().find('input').val(),
                md5: $(this).attr('md5')
            },
            context: this,
            success: function(data, statusText, xhr) {
                $(this).css({'color':'black', 'background-color':'red', 'text-decoration':'none'});
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(this).css({'text-decoration':'line-through'});
            }
        });
    });
});