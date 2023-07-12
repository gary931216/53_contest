let links = document.querySelectorAll("a");
let main = document.querySelector("#main")

links.forEach((link, index) => {
    link.addEventListener('click', (ev) => {
        ev.preventDefault();
        if(index == 0) {
            main.innerHTML = "<p>我是A</p>"
        }
        if(index == 1) {
            main.innerHTML = "<p>我是B</p>"
        }
        if(index == 2) {
            main.innerHTML = "<p>我是C</p>"
        }
        history.replaceState({}, link.href, link.href)
    })
})