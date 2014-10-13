/**
 * Sctipts for work with additional actions
 */
$(function()
{
    var spinnerBlock = $('#spinnerBlock'),
        actionsBlock = $('#actionsBlock');

    /**
     * Start the parser
     */
    $(document).on('click', '#startParserAction', function()
    {
        if (confirm('Вы уверены?')) {
            ajaxRequest($('#startParserUrl').val());

            return true;
        }

        return false;
    });

    /**
     * Clearing the database
     */
    $(document).on('click', '#clearDatabaseAction', function()
    {
        if (confirm('Вы уверены?')) {
            ajaxRequest($('#clearDatabaseUrl').val());

            return true;
        }

        return false;
    });

    /**
     * Ajax request
     *
     * @param {string} actionUrl
     */
    function ajaxRequest(actionUrl)
    {
        $.ajax({
            url: actionUrl,
            type: 'POST',
            dataType: 'json',
            beforeSend: function()
            {
                actionsBlock.html('');
                spinnerBlock.spin('large');
            },
            success: function(data)
            {
                spinnerBlock.stop();
                window.location.assign($('#homepageUrl').val())
            },
            error: function()
            {
                spinnerBlock.stop();
                actionsBlock.html('Error.')
            }
        });
    }
});