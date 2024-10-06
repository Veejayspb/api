
(function () {
    const items = document.getElementsByTagName('pre');
    for (let i = 0; items[i] !== undefined; i++) {
        items[i].innerHTML = JSON.stringify(
            JSON.parse(items[i].innerHTML),
            undefined,
            3
        );
    }
})();
