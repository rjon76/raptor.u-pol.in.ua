var ajax = {};

ajax =
{
    /*
     Отправляем асинхронный "GET" запрос
    */
    get: function(url, succFunc) {
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response){
                eval(succFunc['func'] + '(response' + (succFunc['args'] ? ', succFunc[\'args\']' : '') + ');')
            }
        });
    },

    post: function(url, data, succFunc) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(response){
                eval(succFunc['func'] + '(response' + (succFunc['args'] ? ', succFunc[\'args\']' : '') + ');')
            }
        });
    }
}