EstadoPesquisa = {

    prefix: 'pesquisa_',

    getPesquisa: function(page) {
        var name = EstadoPesquisa.prefix + page + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    },

    setPesquisa: function(page, pesquisa) {

        var date = new Date();
        var minuto = 60 * 1000;
        date.setTime(date.getTime() + (10 * minuto));
        var name = EstadoPesquisa.prefix + page;
        var expires = date.toUTCString();
        document.cookie = name + "=" + pesquisa + "; expires=" + expires + ";path=/";
    },



    deletePesqusa: function(page) {
        var date = new Date();
        var minuto = 60 * 1000;
        date.setTime(date.getTime() - minuto);
        var name = EstadoPesquisa.prefix + page;
        document.cookie = name + "=" + "; expires=" + date.toUTCString()
    }





};