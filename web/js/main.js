const ajax = new Ajax();

window.onload = function() {
    ajaxHandler('/api/v1/tree/0', 'GET', null, (message) => {
        let view = new ViewTree(message);
        ge('tree').innerHTML = view.createRoot();
    });
}