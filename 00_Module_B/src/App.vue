<script setup>

</script>

<template>
  <header>
    <button>新增專案</button>
    <button @click="previousClick">復原</button>
    <button @click="nextClick">重做</button>
    <button>儲存圖片</button>
  </header>
  <main>
    <div class="functions">
      <div>
        <button class="select" v-on:click="changeFunction('select')">選取</button>
        <button class="pen" v-on:click="changeFunction('pen')">筆刷</button>
        <button class="bucket" v-on:click="changeFunction('bucket')">油漆桶</button>
        <button class="ink" v-on:click="changeFunction('ink')">樣張</button>
      </div>
      <div>
        <p>function: {{ nowFunc }}</p>
        <p>color: {{ color }}</p>
      </div>
      <div>
        <p>功能區</p>
        <label v-if="nowFunc == 'bucket' || nowFunc == 'pen'" for="bucket">
          調色盤
        </label>
        <input v-if="nowFunc == 'bucket' || nowFunc == 'pen'" type="color" id="bucket" v-model="color">
        <label v-if="nowFunc == 'pen'" for="degree">
          筆畫大小
        </label>
        <input v-if="nowFunc == 'pen'" type="number" id="degree" v-model="degree">
        <div class="inkPicture" v-if="nowFunc == 'ink'">
          <label for="uploadInk">
            上傳樣張
          </label>
          <input type="file" id="uploadInk" accept="image/*" @change="uploadInk">
          <img @click="changeNowInkPicture(index)" v-for="(inkPicture, index) in inkPictures" :class="{select: index == nowInkPicture}" :key="inkPicture.id" :src="inkPicture.url">
        </div>
      </div>
    </div>
    <div class="canvas">
      <div class="layer" v-for="(layer, index) in layers" :key="layer.id" 
      v-bind:style="{backgroundColor: layer.bgcolor,position: 'absolute', top: 0, left: 0, width: (canvas.width + 'px'), height: (canvas.height + 'px'), zIndex : (index == nowLayer ?  1000 : index)}"
      v-on:mousedown="mouseDown($event)" v-on:mousemove="mouseMove" v-on:mouseup="mouseUp" v-on:mouseleave="mouseUp">
        <div class="object" :class="{select: (nowObject.layIndex == index && nowObject.objIndex == objIndex)}" v-for="(object, objIndex) in layer.object" :key="object.id" 
        @click="selectObject(index, objIndex)" v-on:mousedown.stop="selectStart"
        v-bind:style="{top: (object.y + 'px'), left: (object.x + 'px'), transform: ('rotate('+object.rotate+'deg)')}">
          <img v-if="object.name == 'canvas'" v-bind:src="object.url" :style="{width: (object.width + 'px'), height: (object.height + 'px')}">
          <img class="ink" v-if="object.name == 'ink'" v-bind:src="object.url" :style="{width: (object.width + 'px'), height: (object.height + 'px')}">
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot a"
          v-on:mousedown.stop="scaleStart('a', $event)"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot b"
          v-on:mousedown.stop="scaleStart('b', $event)"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot c"
          v-on:mousedown.stop="scaleStart('c', $event)"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot d"
          v-on:mousedown.stop="scaleStart('d', $event)"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dash"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot e"
          v-on:mousedown.stop="rotateStart"></div>
        </div>
      </div>
    </div>
    <div class="layers">
      <div>
        <button @click="addLayer">新增圖層</button>
        <button>向上</button>
        <button>向下</button>
        <button @click="nowSelectLayer">取消選取</button>
      </div>
      <div class="lists">
        <div :class="{nowlayer : nowLayer == index}"
        v-for="(layer, index) in layers" :key="index"
        @click="changeNowLayer(index)">
          {{ layer.name }}
        </div>
      </div>
    </div>
  </main>
          <!-- v-on:mousedown.capture.stop="mouseDown" v-on:mousemove.capture.stop="mouseMove" v-on:mouseup.capture.stop="mouseUp" v-on:mouseleave="mouseUp" -->
  <!-- <div class="black"></div>
  <div class="dialog">
    <div class="createCanvas">
      <label for="width">
        寬度
      </label>
      <input type="text" id="width" v-model="canvas.width">
      <label for="height">
        長度
      </label>
      <input type="text" id="height" v-model="canvas.height">
      <label for="bgcolor">
        背景顏色
      </label>
      <input type="color" id="bgcolor" v-model="canvas.bgcolor">
    </div>
  </div> -->
</template>

<script>
export default {
  data() {
    return {
      nowDot: null,
      nowLayer: 0,
      nowFunc: '',
      nowObject: {
        layIndex: null,
        objIndex: null
      },
      nowInkPicture: null,
      inkPictures: [],
      ink: {
        onInk: 0,
      },
      color: '#000000',
      degree: 10,
      test: 'aaa',
      canvas: {
        width: 1280,
        height: 720,
        bgcolor: '#FFFFFF'
      },
      pen: {
        ondraw: 0,
        startX: '',
        startY: '',
        nowX: '',
        nowY: '',
        endX: '',
        endY: '',
      },
      select: {
        onSelect: 0,
        startX: '',
        startY: '',
        nowX: '',
        nowY: '',
        endX: '',
        endY: '',
      },
      scale: {
        onScale: 0,
        startX: '',
        startY: '',
        nowX: '',
        nowY: '',
        endX: '',
        endY: '',
        left: null,
        right: null,
        top: null,
        bottom: null,
      },
      rotate: {
        onRotate: 0,
        oX: '',
        oY: '',
        startX: '',
        startY: '',
        nowX: '',
        nowY: '',
      },
      layers: [
        {
          id: 1,
          name: '空白畫板',
          bgcolor: '#FFFFFF',
          opacity: 1,
          object: []
        },
      ],
      previous:[],
      next:[],
    }
  },
  methods: {
    changeFunction(func) {
      if(func != 'select') {
        this.nowObject = {
          layIndex: null,
          objIndex: null
        }
      }
      this.nowFunc = func
    },
    mouseDown(event) {
      let currentTarget = event.currentTarget.getBoundingClientRect();
      
      let x = event.clientX - currentTarget.x;
      let y = event.clientY - currentTarget.y;
      if(this.nowFunc == "pen") {
        this.pen.startX = x;
        this.pen.startY = y;
        this.pen.nowX = x;
        this.pen.nowY = y;
        
        this.pen.left = x
        this.pen.right = x
        this.pen.top = y
        this.pen.bottom = y
        this.penStart();  
      }
      if(this.nowFunc == "ink") {
        if(this.nowInkPicture == null) {
          alert("請選擇樣張")
        }else {
          console.log(event);
          this.inkStart(x, y);
        }
      }
      if(this.nowFunc == "bucket") {
        this.bucket();
      }
    },
    mouseMove(event) {
      let currentTarget = event.currentTarget.getBoundingClientRect();
      let x = event.clientX - currentTarget.x;
      let y = event.clientY - currentTarget.y;
      if(this.nowFunc == "pen" && this.pen.ondraw == 1) {
        this.penMove(x, y);  
      }  
      if(this.nowFunc == "ink" && this.ink.onInk == 1) {
        this.inkMove(x, y);  
      }  
      if(this.nowFunc == "select" && this.select.onSelect == 1) {
        this.selectMove(event.movementX, event.movementY)  
      }
      if(this.nowFunc == "select" && this.scale.onScale == 1) {
        this.scaleMove(event.movementX, event.movementY);  
      }
      if(this.nowFunc == "select" && this.rotate.onRotate == 1) {
        this.rotateMove(event.clientX, event.clientY);  
      }
    },
    mouseUp(event) {
      let currentTarget = event.currentTarget.getBoundingClientRect();
      let x = event.clientX - currentTarget.x;
      let y = event.clientY - currentTarget.y;
      if(this.nowFunc == "pen" && this.pen.ondraw == 1) {
        this.pen.endX = x;
        this.pen.endY = y
        this.penEnd()
      }  
      if(this.nowFunc == "ink" && this.ink.onInk == 1) {
        this.inkEnd()
      }  
      if(this.nowFunc == "select" && this.select.onSelect == 1) {
        this.selectEnd()
      } 
      if(this.nowFunc == "select" && this.scale.onScale == 1) {
        this.scaleEnd()
      }
      if(this.nowFunc == "select" && this.rotate.onRotate == 1) {
        this.rotateEnd();  
      } 
    },
    // 筆刷
    penStart() {
      this.pen.ondraw = 1;
      let canvas = document.createElement('canvas')
      canvas.height = this.canvas.height
      canvas.width = this.canvas.width
      let object = this.layers[this.nowLayer].object;
      let url = this.canvasProcess(canvas)
      let id = 1
      if(this.layers[this.nowLayer].object.length) {
        id = Math.max(...this.layers[this.nowLayer].object.map(p => p.id)) + 1
      }
      this.layers[this.nowLayer].object.push({
        id: id,
        name: 'canvas',
        canvas: canvas,
        url: url,
        rotate: 0,
        x: 0,
        y: 0
      })
      // 紀錄步驟
      this.previous.push({
        name: 'createObj',
        layIndex: this.nowLayer,
        objIndex: this.layers[this.nowLayer].object.length - 1,
      })
    },
    penMove(x, y) {
      this.pen.left = (this.pen.left == null || x < this.pen.left) ? x : this.pen.left
      this.pen.right = (this.pen.right == null || x > this.pen.right) ? x : this.pen.right
      this.pen.top = (this.pen.top == null || y < this.pen.top) ? y : this.pen.top
      this.pen.bottom = (this.pen.bottom == null || y > this.pen.bottom) ? y : this.pen.bottom
      let object = this.layers[this.nowLayer].object;
      let canvas = object[object.length - 1].canvas;
      let ctx = canvas.getContext('2d'); 
      ctx.fillStyle = 'rgba(255, 255, 255, 0)'
      // ctx.lineJoin = 'round';
      // ctx.lineCap = 'round';
      ctx.lineWidth = this.degree;  
      ctx.strokeStyle = this.color;
      ctx.beginPath();
      // start from
      ctx.moveTo(this.pen.nowX, this.pen.nowY);
      // go to
      ctx.lineTo(x, y);
      ctx.stroke();
      this.pen.nowX = x;
      this.pen.nowY = y;
      let url = this.canvasProcess(canvas)
      this.layers[this.nowLayer].object[object.length - 1].canvas = canvas;
      this.layers[this.nowLayer].object[object.length - 1].url = url;
    },
    penEnd() {
      this.pen.ondraw = 0;
      console.log(this.pen.top, this.pen.bottom)
      console.log(this.pen.left, this.pen.right)
      let object = this.layers[this.nowLayer].object;
      let img = document.createElement('img');
      img.src = object[object.length - 1].url;
      let width = this.pen.right - this.pen.left + this.degree
      let height = this.pen.bottom - this.pen.top + this.degree 
      console.log(width, height)
      let canvas = document.createElement('canvas')
      canvas.width = width;
      canvas.height = height;
      let ctx = canvas.getContext('2d')
      ctx.drawImage(img, this.pen.left - (this.degree / 2), this.pen.top - (this.degree / 2), width, height, 0, 0, width, height);
      this.layers[this.nowLayer].object[object.length - 1].canvas = canvas;
      this.layers[this.nowLayer].object[object.length - 1].url = this.canvasProcess(canvas);
      this.layers[this.nowLayer].object[object.length - 1].width = width
      this.layers[this.nowLayer].object[object.length - 1].height = height
      this.layers[this.nowLayer].object[object.length - 1].x = this.pen.left - (this.degree / 2)
      this.layers[this.nowLayer].object[object.length - 1].y = this.pen.top - (this.degree / 2)
      this.clearNext()
    },
    canvasProcess(canvas) {
      let url = canvas.toDataURL("image/png", 1.0);
      return url
    },
    // 圖層
    addLayer() {
      let id = Math.max(...this.layers.map(p => p.id)) + 1
      this.layers.push({
        id: id,
        name: '空白畫板' + id,
        bgcolor: 'rgba(256, 256, 256, 1)',
        opacity: 1,
        object: []
      });
      // 紀錄步驟
      this.previous.push({
        name: 'createLayer',
        layIndex: this.layers.length - 1,
      })
      this.clearNext()
    },
    changeNowLayer(index) {
      this.nowLayer = index;
    },
    nowSelectLayer() {
      this.nowLayer = null
    },
    // 選取
    selectObject(layIndex, objIndex) {
      if(this.nowFunc == 'select') {
        this.nowObject.layIndex = layIndex
        this.nowObject.objIndex = objIndex
      }
    },
    selectStart(event) {
      let currentTarget = event.currentTarget.getBoundingClientRect();
      let x = event.clientX - currentTarget.x;
      let y = event.clientY - currentTarget.y;
      if(this.nowFunc == "select" && this.nowObject.layIndex != null && this.nowObject.objIndex != null) {
        this.select.onSelect = 1;
        this.select.startX = x;
        this.select.startY = y;
        this.select.nowX = x;
        this.select.nowY = y;
      }
    },
    selectMove(x, y) {
      let layIndex = this.nowObject.layIndex
      let objIndex = this.nowObject.objIndex
      this.layers[layIndex].object[objIndex].x += x
      this.layers[layIndex].object[objIndex].y += y
      this.select.nowX = x
      this.select.nowY = y
    },
    selectEnd() {
      this.clearNext()
      this.select.onSelect = 0
    },
    // 樣張
    uploadInk(event) {
      let id = 1
      if(this.inkPictures.length) {
        id = Math.max(...this.inkPictures.map(p => p.id)) + 1
      }
      let file = event.target.files[0]
      let reader = new FileReader();
      let _this = this;
      reader.readAsDataURL(file);
      reader.onload = function () {
        _this.inkPictures.push({
          id: id,
          url: reader.result
        })
      };
    },
    changeNowInkPicture(index) {
      this.nowInkPicture = index
    },
    inkStart(x, y) {
      console.log("inkStart")
      this.ink.onInk = 1
      let id = 1
      if(this.layers[this.nowLayer].object.length) {
        id = Math.max(...this.layers[this.nowLayer].object.map(p => p.id)) + 1
      }
      let url = this.inkPictures[this.nowInkPicture].url
      let img = document.createElement('img')
      img.src = url;
      let width = img.width;
      let height = img.height;
      this.layers[this.nowLayer].object.push({
        id: id,
        name: 'ink',
        url: url,
        x: x,
        y: y,
        rotate: 0,
        width: width,
        height: height,
      })
      // 紀錄步驟
      this.previous.push({
        name: 'createObj',
        layIndex: this.nowLayer,
        objIndex: this.layers[this.nowLayer].object.length - 1,
      })
    },
    inkMove(x, y) {
      let id = 1
      if(this.layers[this.nowLayer].object.length) {
        id = Math.max(...this.layers[this.nowLayer].object.map(p => p.id)) + 1
      }
      let url = this.inkPictures[this.nowInkPicture].url
      let img = document.createElement('img')
      img.src = url;
      let width = img.width;
      let height = img.height;
      this.layers[this.nowLayer].object.push({
        id: id,
        name: 'ink',
        url: url,
        x: x,
        y: y,
        rotate: 0,
        width: width,
        height: height,
      })
      // 紀錄步驟
      this.previous.push({
        name: 'createObj',
        layIndex: this.nowLayer,
        objIndex: this.layers[this.nowLayer].object.length - 1,
      })
    },
    inkEnd() {
      this.clearNext()
      this.ink.onInk = 0
    },
    // 油漆桶
    bucket() {
      if(this.nowLayer == null) {
        alert('請選取塗層')
      }else {
        this.layers[this.nowLayer].bgcolor = this.color;
      }
    },
    // 放大縮小
    scaleStart(dot,event) {
      if(this.nowFunc == "select") {
        this.scale.onScale = 1
        this.nowDot = dot
        let x = event.clientX;
        let y = event.clientY;
        // 紀錄步驟
        let layIndex = this.nowObject.layIndex
        let objIndex = this.nowObject.objIndex
        this.previous.push({
          name: 'changeObj',
          layIndex: layIndex,
          objIndex: objIndex,
          object: {...this.layers[layIndex].object[objIndex]}
        })
      }
    },
    scaleMove(x, y) {
      let layIndex = this.nowObject.layIndex
      let objIndex = this.nowObject.objIndex
      if(this.nowDot == 'a') {
        this.layers[layIndex].object[objIndex].x += x
        this.layers[layIndex].object[objIndex].y += y
        this.layers[layIndex].object[objIndex].width -= x
        this.layers[layIndex].object[objIndex].height -= y
      }
      if(this.nowDot == 'b') {
        this.layers[layIndex].object[objIndex].y += y
        this.layers[layIndex].object[objIndex].width += x
        this.layers[layIndex].object[objIndex].height -= y
      }
      if(this.nowDot == 'c') {
        this.layers[layIndex].object[objIndex].width += x
        this.layers[layIndex].object[objIndex].height += y
      }
      if(this.nowDot == 'd') {
        this.layers[layIndex].object[objIndex].x += x
        this.layers[layIndex].object[objIndex].width -= x
        this.layers[layIndex].object[objIndex].height += y
      }
    },
    scaleEnd() {
      this.clearNext()
      this.scale.onScale = 0
    },
    // 旋轉
    rotateStart(event) {
      if(this.nowFunc == "select") {
        let layIndex = this.nowObject.layIndex
        let objIndex = this.nowObject.objIndex
        // 紀錄步驟
        this.previous.push({
          name: 'changeObj',
          layIndex: layIndex,
          objIndex: objIndex,
          object: {...this.layers[layIndex].object[objIndex]}
        })
        let object = this.layers[layIndex].object[objIndex]
        this.rotate.onRotate = 1
        // 先抓object 再找起始點x, y 再找控制點x, y 也可以直接將向量0設為(0, 100)
        let parenttarget = event.target.parentElement.getBoundingClientRect();
        let oX = parenttarget.x + parenttarget.width / 2;
        let oY = parenttarget.y + parenttarget.height / 2;
        this.rotate.oX = oX;
        this.rotate.oY = oY;
        this.rotate.startX = oX;
        this.rotate.startY = parenttarget.y - 100;
      }
    },
    rotateMove(x, y) {
      let layIndex = this.nowObject.layIndex
      let objIndex = this.nowObject.objIndex
      let x1 = this.rotate.startX - this.rotate.oX;
      let y1 = this.rotate.startY - this.rotate.oY;
      // console.log(x1, y1)
      let x2 = x - this.rotate.oX;
      let y2 = y - this.rotate.oY;
      const dot = x1 * x2 + y1 * y2
      const det = x1 * y2 - y1 * x2
      const angle = Math.atan2(det, dot) / Math.PI * 180
      let deg = (angle + 360) % 360
      this.layers[layIndex].object[objIndex].rotate = deg
    },
    rotateEnd() {
      this.clearNext()
      this.rotate.onRotate = 0
    },
    previousClick() {
      if(this.previous.length) {
        let method = this.previous.pop();
        console.log(method)
        console.log(this.previous)
        if(method.name == 'createLayer') {
          let layer = this.layers.splice(method.layIndex, 1);
          method.layer = layer[0];
        }
        if(method.name == 'createObj') {
          let object = this.layers[method.layIndex].object.splice(method.objIndex, 1);
          method.object = object[0];
        }
        if(method.name == 'changeObj') {
          let backup = this.layers[method.layIndex].object[method.objIndex]
          this.layers[method.layIndex].object[method.objIndex] = method.object
          method.object = backup
        }
        this.next.push(method);
      }
    },
    nextClick() {
      if(this.next.length) {
        let method = this.next.pop();
        if(method.name == 'createLayer') {
          this.layers.splice(method.layIndex, 0, method.layer);
        }
        if(method.name == 'createObj') {
          this.layers[method.layIndex].object.splice(method.layIndex, 0, method.object);
        }
        if(method.name == 'changeObj') {
          let backup = this.layers[method.layIndex].object[method.objIndex]
          this.layers[method.layIndex].object[method.objIndex] = method.object
          method.object = backup
        }
        this.previous.push(method);
      }
    },
    clearNext() {
      this.next = []
    },
    downloadImage() {
      let canvas = element.createElement('canvas');
      canvas.width = this.canvas.width
      canvas.height = this.canvas.height
      let ctx = canvas.getContext('2d')
      this.layers.forEach(layer => {
        ctx.rect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = layer.bgcolor;
        ctx.fill();
        layer.object.forEach(object => {
          
        })
      })
    }
  }
}
</script>

<style scoped>
header {
  height: 10%;
}
main {
  display: flex;
  justify-content: space-around;
  align-items: center;
  height: 90%;
}

.functions,
.layers {
  width: 15%;
  height: 95%;
  background-color: antiquewhite;
}
.functions {
  display: flex;
  flex-direction: column;
}
.functions > div {
  border: 3px solid black;
  margin: 10px 10px;
  border-radius: 5px;
}
.functions > div > .inkPicture > img {
  width: 50%;
  aspect-ratio: 1/1;
  object-fit: cover;
  object-position: center;
}
.functions > div > .inkPicture > img.select {
  border: 3px solid black;
}
.canvas {
  position: relative;
  width: 60%;
  height: 95%;
  background-color: rgb(255, 221, 176);
  overflow: scroll;
}
.canvas > .layer > .object.select {
  border: 1px dashed rgb(119, 119, 119);
}
.canvas > .layer > .object > .dot{
  position: absolute;
  width: 15px;
  height: 15px;
  background-color: rgb(83, 83, 83);
  border-radius: 50%;
}
.canvas > .layer > .object > .dot.a {
  top: 0;
  left: 0;
  transform: translate(-50%, -50%);
  cursor: nw-resize;
}
.canvas > .layer > .object > .dot.b {
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
  cursor: ne-resize;
}
.canvas > .layer > .object > .dot.c {
  bottom: 0;
  right: 0;
  transform: translate(50%, 50%);
  cursor: nw-resize;
}
.canvas > .layer > .object > .dot.d {
  bottom: 0;
  left: 0;
  transform: translate(-50%, 50%);
  cursor: ne-resize;
}
.canvas > .layer > .object > .dash {
  top: -100px;
  left: 50%;
  transform: translateX(-50%);
  position: absolute;
  border: 0.5px dashed rgb(119, 119, 119);
  height: 100px;
}
.canvas > .layer > .object > .dot.e {
  width: 15px;
  height: 15px;
  top: -100px;
  left: 50%;
  transform: translateX(-50%);
  background-color: rgb(43, 76, 226);
}

.black {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1001;
  width: 100%;
  height: 100%;
  background-color: black;
  opacity: 0.7;
}
.dialog {
  position: fixed;
  top: 200px;
  left: 50%;
  transform: translateX(-50%);
  padding: 10px;
  border-radius: 5px;
  z-index: 1002;
  background-color: white;
}
.object {
  position: absolute;
  top: 0;
  left: 0;
  -webkit-user-drag: none;
  user-select:none;
}
.object > img {
  -webkit-user-drag: none;
  user-select:none;
}
.layers > .lists > div {
  margin: 5px 10px;
  width: calc(100% - 20px);
  height: 60px;
  background-color: rgb(247, 247, 247);
}
.layers > .lists > div.nowlayer {
  margin: 5px 10px;
  width: calc(100% - 20px);
  height: 60px;
  background-color: rgb(182, 182, 182);
}
</style>
