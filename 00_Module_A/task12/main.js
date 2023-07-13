let images = document.querySelectorAll('img');
let div = document.createElement('div');
let img = document.createElement('img');
let close = document.createElement('button');
let pre = document.createElement('button');
let next = document.createElement('button');
close.innerHTML = '關閉';
pre.innerHTML = '<'
next.innerHTML = '>'
// close.style = 

div.style = "display: none"
div.appendChild(img);
div.appendChild(close);
div.appendChild(pre);
div.appendChild(next);
document.body.appendChild(div);

let nowIndex;
images.forEach((image, index) => {
    image.addEventListener('click', () => {
        nowIndex = index
        img.src = image.src;
        div.style = "position: fixed;top: 0;left: 0;width: 100%; height: 100vh; background-color: rgba(0, 0, 0, 0.7);display: flex;justify-content: center;align-items: center;"
    })
})

pre.addEventListener('click', (ev) => {
    ev.preventDefault()
    nowIndex -= 1;
    if(nowIndex < 0) {
        nowIndex = images.length - 1
    }
    img.src = images[nowIndex].src
})
next.addEventListener('click', (ev) => {
    ev.preventDefault()
    nowIndex += 1;
    if(nowIndex > images.length - 1) {
        nowIndex = 0
    }
    img.src = images[nowIndex].src
})

div.addEventListener('click', () => {
    // div.style = "display: none"
})