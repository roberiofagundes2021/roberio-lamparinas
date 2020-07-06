history.pushState({
    page: 1
}, "Categoria", "#");
window.addEventListener("popstate", function(event) {
    history.pushState({
        page: 1
    }, "Categoria", "#");
});

var popped = ('state' in window.history && window.history.state !== null), initialURL = location.href;