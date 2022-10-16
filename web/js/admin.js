const admin = true;
const ajax = new Ajax();


function enter() {
    let params = {
        "login": ge('login').value,
        "password" : ge('password').value
    };
    ajaxHandler('/api/v1/login', 'PUT', params, (message) => {
        ge('enter').style.display = 'none';
        ge('exit').style.display = 'inline-block';
        ajax.setToken(message.token);
        ajaxHandler('/api/v1/tree/0', 'GET', null, (message) => {
            let view = new ViewTree(message);
            ge('add-root').style.display = 'inline-block';
            ge('tree').innerHTML = view.createRoot();
        });
    });
    return false;
}

function exit() {
    let params = {
        "logout": true
    };
    ajaxHandler('/api/v1/login', 'PUT', params, (message) => {
        ge('list').innerHTML = '';
        ge('login').value = '';
        ge('password').value = '';
        ge('exit').style.display = 'none';
        ge('enter').style.display = 'flex';
    });
    return false;
}

function addRoot() {
    let id = 'root';
    let name = '';
    ge('show').style.display = 'block';
    let form = new ViewForm();
    ge('show_content').innerHTML = form.addTemplate(id, name);
    return false;
}

function sendAddRoot(id) {
    var name = ge('add_name').value;
    let params = {
        "id_parent": null,
        "name": name,
        "description": ge('add_text').value,
        "state": 3
    };
    ajaxHandler('/api/v1/add/0', 'POST', params, (message) => {
        ge('show_content').innerHTML = 'В корень добавлен объект <br />"'+ name +'"<br />';
        ajaxHandler('/api/v1/tree/0', 'GET', null, (message) => {
            let view = new ViewTree(message);
            ge('tree').innerHTML = view.createRoot();
        });
    });
    return false;
}

function add(id) {
    ge('show').style.display = 'block';
    let form = new ViewForm();
    let name = ge('name_'+ id).innerHTML;
    ge('show_content').innerHTML = form.addTemplate(id, name);
    return false;
}

function sendAdd(id) {
    var name = ge('add_name').value;
    let params = {
        "id": id,
        "name": name,
        "description": ge('add_text').value,
        "state": 3
    };
    ajaxHandler('/api/v1/add/'+ id, 'POST', params, (message) => {
        let parent = ge('name_'+ id).innerHTML;
        ge('show_content').innerHTML = 'Объект <br />"'+ name +
            '"<br />добавлен в ветку id='+ id +' "'+ parent +'"';
        showBranch(id);
    });
    return false;
}

function edit(id) {
    ajaxHandler('/api/v1/object/' + id, 'GET', null, (message) => {
        ge('show').style.display = 'block';
        let form = new ViewForm();
        ge('show_content').innerHTML = form.editTemplate(message);
    });
    return false;
}

function sendEdit(id) {
    let params = {
        "id": id,
        "name": ge('edit_name').value,
        "description": ge('edit_text').value,
        "state": 4
    };
    ajaxHandler('/api/v1/edit/'+ id, 'PUT', params, (message) => {
        ge('name_'+ message.id).innerHTML = message.name;
        ge('show_content').innerHTML = 'Объект id='+ message.id +'<br />' +
            '"'+ message.name +'"<br />отредактирован';
    });
    return false;
}

function jump(id) {
    ge('show').style.display = 'block';
    let form = new ViewForm();
    ge('show_content').innerHTML = form.jumpTemplate(id);
    return false;
}

function sendJump(id, root) {
    var parentId = root ? 0 : ge('jump').value;
    let params = {
        "parent_id": parentId,
        "state": 5
    };
    ajaxHandler('/api/v1/jump/'+ id, 'PUT', params, (message) => {
        if (message.status == 'error') {
            ge('show').style.display = 'none';
        } else {
            let name = message[id];
            let parentName = message[parentId];
            parentName = (typeof message[parentId] == 'undefined') ? 'корень' : parentName;
                ge('show_content').innerHTML = 'Ветка <br />"'+ name +
                '"<br />перемещена в <br />"'+ parentName +'"';
        }
        ajaxHandler('/api/v1/tree/0', 'GET', null, (message) => {
            let view = new ViewTree(message);
            ge('tree').innerHTML = view.createRoot();
        });
    });
    return false;
}

function del(id) {
    if (confirm("Удалить?")) {
        ajaxHandler('/api/v1/'+ id, 'DELETE', null, (message) => {
            if (ge('show_'+ message)) {
                ge('show_'+ message).parentNode.remove();
            }
        });
    }
    return false;
}