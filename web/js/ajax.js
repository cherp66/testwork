
var request = getRequest();

function getRequest() {
    if(window.XMLHttpRequest) {
        return  new XMLHttpRequest();
    } else if(window.ActiveXObject) {
        return  new ActiveXObject("Microsoft.XMLHTTP");
    }
}

class Ajax {

    constructor() {
        this.process = null;
        this.error = null;
        this.success = null;
        this.params = null;
        this.token = null;
    }
    data(params) {
        this.params = params;
    }
    json(json) {
        this.params = JSON.stringify(json);
    }
    send(url, method) {
        let sender = new Sender();
        sender.send(url, method);
    }
    setToken(token) {
        this.token = token;
    }
    response() {
        let r_headers = request.getResponseHeader('Content-Type');
        if(r_headers.indexOf('text/html') != -1) {
            return request.responseText;
        }
        if(r_headers.indexOf('application/json') != -1) {
            return eval('(' + request.responseText + ')');
        }
    }

    _process() {
        if (this.process != null) {
            this.process();
        }
    }

    _success() {
        if (this.success == null) {
            alert("Error: success should be a function.");
        }
        this.success(this.response());
    }

    _error() {
        if (this.error != null) {
            this.error(request.status);
        }
    }
}

class Sender {

    send(url, method) {
        request.open(method, url, true);
        request.setRequestHeader('X-Requested-With', 'XmlHttpRequest');
        request.setRequestHeader('Content-Type', 'application/json; charset=utf-8');
        if (ajax.token != null) {
            request.setRequestHeader('X-Token', ajax.token);
        }
        request.onreadystatechange = function() {
            if(request.readyState == 2) {
                ajax._process();
            }
            if(request.readyState == 4) {
                if(request.status == 200) {
                    ajax._success();
                } else {
                    ajax._error();
                }
            }
        }
        request.send(this.params_prepares());
    }

    params_prepares() {
        let params = ajax.params;
        if (params == null) {
            return null;
        }
        ajax.params = null;
        return params;
    }
}
