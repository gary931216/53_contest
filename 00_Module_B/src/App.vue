<script setup>

</script>

<template>
  <header>
    <button>新增專案</button>
    <button>復原</button>
    <button>重做</button>
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
      v-on:mousedown.capture="mouseDown($event)" v-on:mousemove.capture="mouseMove" v-on:mouseup.capture="mouseUp" v-on:mouseleave="mouseUp">
        <div class="object" v-for="(object, objIndex) in layer.object" :key="object.id" 
        @click.capture="selectObject(index, objIndex)"

        v-bind:style="{top: (object.y + 'px'), left: (object.x + 'px')}">
          <img v-if="object.name == 'canvas'" v-bind:src="object.url">
          <img class="ink" v-if="object.name == 'ink'" v-bind:src="object.url">
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot a"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot b"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot c"></div>
          <div v-if="nowObject.layIndex == index && nowObject.objIndex == objIndex" class="dot d"></div>
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
      layers: [
        {
          id: 1,
          name: '空白畫板',
          bgcolor: '#FFFFFF',
          opacity: 1,
          object: []
        },
      ]
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
        console.log(x, y)
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
      if(this.nowFunc == "select") {
        this.select.onSelect = 1;
        this.select.startX = x;
        this.select.startY = y;
        this.select.nowX = x;
        this.select.nowY = y;
        // this.selectStart();
      }
    },
    mouseMove(event) {
      let currentTarget = event.currentTarget.getBoundingClientRect();
      let x = event.clientX - currentTarget.x;
      let y = event.clientY - currentTarget.y;
      if(this.nowFunc == "pen" && this.pen.ondraw == 1) {
        console.log(x, y)
        this.penMove(x, y);  
      }  
      if(this.nowFunc == "ink" && this.ink.onInk == 1) {
        this.inkMove(x, y);  
      }  
      if(this.nowFunc == "select" && this.select.onSelect == 1) {
        this.selectMove(x, y);  
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
        x: 0,
        y: 0
      })
    },
    penMove(x, y) {
      let object = this.layers[this.nowLayer].object;
      let canvas = object[object.length - 1].canvas;
      let ctx = canvas.getContext('2d'); 
      ctx.fillStyle = 'rgba(255, 255, 255, 0)'
      ctx.lineJoin = 'round';
      ctx.lineCap = 'round';
      ctx.lineWidth = 10;  
      ctx.strokeStyle = `#000000`;
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
      console.log(this.layers[this.nowLayer].object.url);
    },
    penEnd() {
      this.pen.ondraw = 0;
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
    selectMove(x, y) {
      let layIndex = this.nowObject.layIndex
      let objIndex = this.nowObject.objIndex
      this.layers[layIndex].object[objIndex].x +=  x - this.select.nowX
      this.layers[layIndex].object[objIndex].y +=  y - this.select.nowY
      console.log(this.select.nowX - x, this.select.nowY - y)
      this.select.nowX = x
      this.select.nowY = y
    },
    selectEnd() {
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
      this.layers[this.nowLayer].object.push({
        id: id,
        name: 'ink',
        url: this.inkPictures[this.nowInkPicture].url,
        x: x,
        y: y,
      })
    },
    inkMove(x, y) {
      let id = 1
      if(this.layers[this.nowLayer].object.length) {
        id = Math.max(...this.layers[this.nowLayer].object.map(p => p.id)) + 1
      }
      console.log(x, y)
      // this.layers[this.nowLayer].object.push({
      //   id: id,
      //   name: 'ink',
      //   url: this.inkPictures[this.nowInkPicture].url,
      //   x: x,
      //   y: y,
      // })
    },
    inkEnd() {
      this.ink.onInk = 0
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
.canvas > .layer > .object > .dot{
  position: absolute;
  width: 15px;
  height: 15px;
  background-color: rgb(119, 119, 119);
  border-radius: 50%;
}
.canvas > .layer > .object > .dot.a {
  top: 0;
  left: 0;
  transform: translate(-50%, -50%);
}
.canvas > .layer > .object > .dot.b {
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
}
.canvas > .layer > .object > .dot.c {
  bottom: 0;
  right: 0;
  transform: translate(50%, 50%);
}
.canvas > .layer > .object > .dot.d {
  bottom: 0;
  left: 0;
  transform: translate(-50%, 50%);
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
