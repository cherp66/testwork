
function ge(id) {
    return document.getElementById(id);
}

function qs(selector){
    return document.querySelector(selector);
}

qs('#list').onclick = (event) => {
    let target = event.target;
    if (target.tagName != 'A') {
        return false;
    }
    let id = target.getAttribute('data');
    showBranch(id);
    return false;
};

function showBranch(id) {
    ajaxHandler('/api/v1/tree/' + id, 'GET', null, (message) => {
        let view = new ViewTree(message);
        var tree = view.createBranch();
        var parent = Object.keys(tree);
        tree.forEach((item) => {
            let branchs = Object.entries(item);
            ge('show_' + parent[0]).innerHTML = '';
            branchs.forEach((branch) => {
                ge('show_' + parent[0]).innerHTML += branch[1];
            });
        });
    });
}

function view(id) {
    ge('show').style.display = 'block';
    ajaxHandler('/api/v1/object/' + id, 'GET', null, (message) => {
        let view = new ViewShow(message);
        ge('show_content').innerHTML = view.createShow();
    });
    return false;
}

function ajaxHandler(uri, method, params, callback) {
    ajax.process = function () {
        ge('error').innerHTML = '';
    }
    ajax.success = function (response) {
        let answer = JSON.parse(response);
        if (answer.status == 'success') {
            callback(answer.message);
        } else if (answer.status == 'error') {
            ge('error').innerHTML = answer.message;
        }
    }
    ajax.error = function (status) {
        ge('error').innerHTML = 'Сбой системы '+ status;
    }

    ajax.json(params);
    ajax.send(uri, method);
}

class ViewShow {
    constructor(message) {
        this.message = message;
    }

    createShow() {
        return '<h4>Просмотр объекта id='+ this.message.id +'</h4>'+
            '<h4>' + this.message.name +'</h4>' + '<pre class="description">'+ this.message.description +'</pre>';
    }
}

class ViewTree {
    constructor(message) {
        this.message = message;
        this.tree = new Array();
    }
    createRoot() {
        var result = '';
        var root = 'root_'
        this.message.forEach((item) => {
            result += this.branchTemplate(item, root);
        });
        return result;
    }
    createBranch() {
        var data = {};
        var branch = {};
        var parent = null;
        this.message.forEach((item) => {
            data[item.id] = item;
            branch[item.id] = this.branchTemplate(item, null);
            parent = item.parent_id;
        });
        this.tree[parent] = branch;
        return this.tree;
    }

    branchTemplate(item, root) {
        let result = '';
        let parent = item.id;
        if (item.parent_id == null) {
            parent = root + item.id;
        }
        result +=
            '<div class="root" id="top_'+ parent +'">'+
            '<span id="name_'+ item.id +'">'+ item.name +'</span>';
        if (item.root > 0) {
            result += ' <a href="#" id="next_'+ item.id +'" class="more fa fa-tree" data="'+ item.id +'"></a>';
        }
        result +='<i class="modify descript fa fa-eye" aria-hidden="true" onclick="view('+ item.id +')"></i>';

        if (typeof admin != "undefined") {
            result +=
                '<i class="modify edit fa fa-pencil" aria-hidden="true" ' +
                'onclick="return edit('+ item.id +')"></i>';
            result +=
                '<i class="modify jump fa fa-arrows-v" aria-hidden="true" ' +
                'onclick="return jump('+ item.id +')"></i>'

            result +=
            '<i class="modify add fa fa-plus-square" aria-hidden="true" ' +
                'onclick="return add('+ item.id + ')"></i>' +
            '<i class="modify delete fa fa-times-circle" aria-hidden="true" ' +
                'onclick="return del('+ item.id + ')"></i>';
        }
        result +=
            '<div class="subitem" id="show_'+ item.id +'"></div>' +
            '</div>';
        return result;
    }
}

class ViewForm {
    addTemplate(id, name) {
        let head = '';
        let result = '';
        if (id == 'root') {
            head ='<h4>Добавить объект в корень</h4>'
        } else {
            head = '<h4>Добавить объект в ветку "'+ name +'"</h4>'
        }
        result = head + 'Имя<br/><input type="text" class="edit-name" id="add_name"/>' +
            '<br/>Описание<br><textarea class="edit-text" id="add_text"></textarea>';
        if(id == 'root') {
            result += '<br/><input type="submit" onclick="sendAddRoot(\''+ id +'\')" value="Сохранить" />';
        } else {
            result += '<br/><input type="submit" onclick="sendAdd(\''+ id +'\')" value="Сохранить" />';
        }
        return result;
    }

    editTemplate(message) {
        return '<h4>Редактировать</h4>'+
            'Имя<br/><input type="text" class="edit-name" id="edit_name" value="'+ message.name +'"/>' +
            '<br/>Описание<br><textarea class="edit-text" id="edit_text">'+ message.description +'</textarea>' +
            '<br/><input type="submit" onclick="sendEdit('+ message.id +')" value="Сохранить" />';
    }

    jumpTemplate(id) {
        return '<h4>Перемещение</h4>' +
            '<input type="submit" onclick="sendJump('+ id +', false)" value="Переместить в id=" />' +
            '<input type="text" class="edit-name" size="5" id="jump"/>' +
            '<br />или в <input type="submit" onclick="sendJump('+ id +', true)" value="корень" />';
    }
}