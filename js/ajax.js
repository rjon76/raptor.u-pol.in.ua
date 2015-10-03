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
                switch(typeof(succFunc)) {

                    case 'object':
                        eval(succFunc['func'] + '(response' + (succFunc['args'] ? ', succFunc[\'args\']' : '') + ');')
                    break;

                    case 'function':
                        succFunc(response);
                    break;
                }
            }
        });
    },

    post: function(url, data, succFunc) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(response){

                switch(typeof(succFunc)) {

                    case 'object':
                        eval(succFunc['func'] + '(response' + (succFunc['args'] ? ', succFunc[\'args\']' : '') + ');')
                    break;

                    case 'function':
                        succFunc(response);
                    break;
                }
            }
        });
    }
}