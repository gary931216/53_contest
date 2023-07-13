let links = document.querySelectorAll("a");
let main = document.querySelector("#main")

links.forEach((link, index) => {
    link.addEventListener('click', (ev) => {
        ev.preventDefault();
        fetch(link.href)
        .then(res => res.text())
        .then(response => {
            let dom = new DOMParser();
            let doc = dom.parseFromString(response, 'text/html');
            let newMain = doc.querySelector('#main').innerHTML;
            main.innerHTML = newMain
        })
        history.replaceState(null, null, link.href)
    })
})