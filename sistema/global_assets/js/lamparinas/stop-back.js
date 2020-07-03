history.pushState({
    page: 1
}, "Categoria", "#");
window.addEventListener("popstate", function(event) {
    history.pushState({
        page: 1
    }, "Categoria", "#");
});