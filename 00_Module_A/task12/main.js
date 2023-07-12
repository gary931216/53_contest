let images = document.querySelectorAll('img');
let div = document.createElement('div');
let img = document.createElement('img');

div.style = "display: none"
div.appendChild(img);
document.body.appendChild(div);

images.forEach(image => {
    image.addEventListener('click', () => {
        img.src = image.src;
        div.style = "position: fixed;top: 0;left: 0;width: 100%; height: 100vh; background-color: rgba(0, 0, 0, 0.7);display: flex;justify-content: center;align-items: center;"
    })
})

div.addEventListener('click', () => {
    div.style = "display: none"
})