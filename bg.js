const canvas = document.getElementById("bg");
const ctx = canvas.getContext("2d");
const container = document.getElementById("background-container");
const maxCodeBlocks = 0;
let elements = {
    codelines: [],
    circuits: []
};
maxAgeBlock = 60000;


function _ccw(ax, ay, bx, by, cx, cy) {
    return (cy - ay) * (bx - ax) > (by - ay) * (cx - ax);
}

function _segmentsIntersect(a, b) {
    // a: {x1,y1,x2,y2}, b: {x1,y1,x2,y2}
    return (
        _ccw(a.x1, a.y1, b.x1, b.y1, b.x2, b.y2) !== _ccw(a.x2, a.y2, b.x1, b.y1, b.x2, b.y2) &&
        _ccw(a.x1, a.y1, a.x2, a.y2, b.x1, b.y1) !== _ccw(a.x1, a.y1, a.x2, a.y2, b.x2, b.y2)
    );
}


class CodeBlock
{
    lines = []
    x = 0
    y = 0
    age = 0
    maxAge = 15000  // value in ms because ageing is frametime in ms
    fontSize = 15
    lineHeight = 1.1

    constructor(text, age = 0) {
        this.x = 0;
        this.y = 0;
        this.age = age;
        this.maxAge = maxAgeBlock;
        text.split("\n").forEach((t, i) => {
            this.lines.push(new CodeLine(this, t, i));
        })
    }

    setPos(x, y) {
        this.x = x;
        this.y = y;
    }

    getOlder(count) {
        this.age += count;
    }

    getDimension() {
        ctx.font = `${this.fontSize}px monospace`;
        const width = Math.max(...this.lines.map(l => l.getWidth()));
        const height = this.lines.length * this.fontSize * this.lineHeight;
        return { width, height };
    }

    getAnchor() {
        const {width, height} = this.getDimension()
        return {
            x: this.x,
            y: this.y,
            w: width,
            h: height
        };
    }

    collide(block) {
        const a = this.getAnchor();
        const b = block.getAnchor();
        return (
            a.x < b.x + b.w &&
            a.x + a.w > b.x &&
            a.y < b.y + b.h &&
            a.y + a.h > b.y
        );
    }

    isDead()
    {
        return this.age >= this.maxAge;
    }

    update(w, h) {
        const agePrecent = 100 / this.maxAge * this.age;
        ctx.font = `${this.fontSize}px monospace`;
        this.lines.forEach((l) => l.update(w, h, agePrecent));

    }
}


class CodeLine {

    i = 0
    block = null
    text = ''
    color = '130,145,185'
    ramp = 25


    constructor(block, text, i) {
        this.block = block
        this.i = i
        this.text = text

    }

    _count() {
        return this.block.lines.length
    }

    _agePrint(offset = 0) {
        return  ((this.i + offset) / this._count()) * this.ramp;
    }

    _ageDelete(offset = 0) {
        return  (100 - this.ramp) + ((this.i + offset) / this._count()) * this.ramp;
    }

    getWidth() {
        return ctx.measureText(this.text).width;
    }

    update(w, h, age) {
        let opacity = 1;
        if (age > this._agePrint() && age < this._ageDelete()) {
            opacity = 1;
            if (age < this._agePrint(1)) {
                const min = this._agePrint()
                const max = this._agePrint(1)
                opacity = 1 / (max - min) * (age - min);
            }
            if (age > this._ageDelete(-1)) {
                const min = this._ageDelete(-1)
                const max = this._ageDelete()
                opacity = (1 / (max - min) * (age - min)) * -1 + 1;
            }
        } else {
            opacity = 0;
        }
        if (opacity > 1) opacity = 1;
        if (opacity < 0) opacity = 0;
        const x = this.block.x;
        const y = this.block.lineHeight * this.block.fontSize + this.block.y + this.i * this.block.lineHeight * this.block.fontSize;
        ctx.fillStyle = `rgba(${this.color},${opacity})`;
        ctx.fillText(this.text, x, y);
    }
}

class Circuit {

    age = 0;
    maxAge = 0;
    startAlign = null;
    elements = [];
    color = '120,150,205'


    constructor(age = 0) {
        this.age = age;
        const winW = window.innerWidth;
        const winH = window.innerHeight;
        let lastAlign = 0;

        let last = this._generateStartLine();
        let notAllowedAligns = [
            (last.align - 2 + 8) % 8,
            (last.align - 3 + 8) % 8,
            (last.align - 4 + 8) % 8,
            (last.align - 5 + 8) % 8,
            (last.align - 6 + 8) % 8
        ]
        this.elements.push(last);
        lastAlign = last.align;
        do {
            let newType = 'line';
            if (last.type === 'line') {
                if (Math.random() >= 0.9) {
                    newType = 'hole';
                } else {
                    newType = 'line';
                }
            }
            const el = new CircuitElement(this, newType, last);
            el.x = last.getEnd().x;
            el.y = last.getEnd().y;
            if (newType === 'line') {
                let tries = 0
                for (tries = 0; tries < 50; tries++) {
                    let newAlign = lastAlign + Math.floor(Math.random() * 3) - 1;
                    newAlign = (newAlign + 8) % 8;
                    if (notAllowedAligns.includes(newAlign)) {
                        continue;
                    }
                    el.align = newAlign;
                    lastAlign = newAlign;
                    el.length = Math.random() * (winW / 16) + 50;

                    let hit = false;
                    for (const c of elements.circuits) {
                        if (c.collide(el)) {
                            hit = true;
                            break;
                        }
                    }
                    if (hit) {
                        //break;
                        continue;
                    }
                    break;
                }
                if (tries >= 50) {
                    console.log(tries)
                    let hole = new CircuitElement(this, 'hole', last);
                    hole.x = last.getEnd().x;
                    hole.y = last.getEnd().y;
                    this.elements.push(hole);
                    return;
                }
            }
            const {x, y} = el.getEnd();

            this.elements.push(el);
            if (x < 0 || x > 0 + winW || y < 0 || y > 0 + winH)
                break;
            last = el;


        } while (true);


        console.log(this);
    }

    _generateStartLine() {
        const winW = window.innerWidth;
        const winH = window.innerHeight;
        this.startAlign = Math.floor(Math.random() * 8);
        let startSeg = this.startAlign - 4;
        if (startSeg < 0) startSeg += 8;

        let x = 0;
        let y = 0;

        switch (startSeg) {
            case 0:
                x = winW + 5;
                y = Math.random() * (winH - (winH * 0.2)) + (winH * 0.1);
                break;
            case 1:
                // random the corner side
                if (Math.random() >= 0.5) {
                    x = winW + 5;
                    y = Math.random() * (winH / 2) + (winH / 2);
                } else {
                    x = Math.random() * (winW / 2) + (winW / 2);
                    y = winH + 5;
                }
                break;
            case 2:
                x = Math.random() * (winW - (winW * 0.2)) + (winW * 0.1);
                y = winH + 5;
                break;
            case 3:
                // random the corner side
                if (Math.random() >= 0.5) {
                    x = -5
                    y = Math.random() * (winH / 2) + (winH / 2);
                } else {
                    x = Math.random() * (winW / 2)
                    y = winH + 5;
                }
                break;
            case 4:
                x = -5;
                y = Math.random() * (winH - (winH * 0.2)) + (winH * 0.1);
                break;
            case 5:
                // random the corner side
                if (Math.random() >= 0.5) {
                    x = -5;
                    y = Math.random() * (winH / 2)
                } else {
                    x = Math.random() * (winW / 2)
                    y = -5;
                }
                break
            case 6:
                x = Math.random() * (winW - (winW * 0.2)) + (winW * 0.1);
                y = -5;
                break
            case 7:
                // random the corner side
                if (Math.random() >= 0.5) {
                    x = Math.random() * (winW / 2) + (winW / 2);
                    y = -5;

                } else {
                    x = winW + 5;
                    y = Math.random() * (winH / 2)
                }
                break;
        }
        const el = new CircuitElement(this, 'line', null);
        el.align = this.startAlign;
        el.length = Math.random() * (winW / 16) + 50;
        el.x = x;
        el.y = y;
        return el;
    }



    collide(testEl) {
        if (!testEl || testEl.type !== "line") return false;

        const segA = testEl.toSegment();
        if (!segA) return false;

        // gegen alle existierenden Liniensegmente in diesem Circuit prüfen
        for (const el of this.elements) {
            if (el.type !== "line") continue;

            // wichtig: Segment gegen sich selbst / direkten Vorgänger nicht prüfen
            // weil Startpunkt identisch ist
            if (testEl.prevElement === el) continue;

            const segB = el.toSegment();
            if (!segB) continue;
            if (_segmentsIntersect(segA, segB)) {
                return true;
            }
        }

        return false;
    }

    getOlder(count) {
        this.age += count;
    }

    isDead()
    {
        return false;
        //return this.age >= this.maxAge;
    }

    update() {
        let lastEl = null;
        let path = false;

        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        for (const el of this.elements) {

            if (el.type === 'line' && !path) {
                path = true;
                ctx.beginPath();
                ctx.moveTo(el.x, el.y);
            }

            if (el.type === 'line' && lastEl !== null && path && el.opacity() !== lastEl.opacity()) {
                const opacity = lastEl.opacity();
                ctx.strokeStyle = `rgba(${this.color},${opacity})`;
                ctx.stroke();

                ctx.beginPath();
                ctx.moveTo(el.x, el.y);
            }

            if (el.type === 'hole' && path) {
                const opacity = lastEl.opacity();
                ctx.strokeStyle = `rgba(${this.color},${opacity})`;
                ctx.stroke();
                path = false;
            }

            el.update();
            lastEl = el;
        }

        if (path && lastEl) {
            ctx.strokeStyle = `rgba(${this.color},${lastEl.opacity()})`;
            ctx.stroke();
        }
    }

}



class CircuitElement {

    circuit = null
    prevElement = null
    type = null
    x = 0
    y = 0
    length = 0
    align = 0

    constructor(circuit, type, prev) {
        this.circuit = circuit;
        this.type = type;
        this.prevElement = prev;
    }

    opacity() {
        return 1;
    }

    update() {
        if (this.type === 'line') {
            return this._drawLine();
        }
        if (this.type === 'hole') {
            return this._drawHole();
        }
    }


    toSegment() {
        if (this.type !== "line") return null;
        const end = this.getEnd();
        return {
            x1: this.x,
            y1: this.y,
            x2: end.x,
            y2: end.y
        };
    }


    getEnd() {
        if (this.type === "line") {
            const a =  (2 * Math.PI) / 8 * this.align
            const x2 = this.x + Math.cos(a) * this.length
            const y2 = this.y + Math.sin(a) * this.length
            return {x: x2, y: y2}
        }
        return {x: this.x, y: this.y}
    }

    _drawLine() {
        const a =  (2 * Math.PI) / 8 * this.align
        const x2 = this.x + Math.cos(a) * this.length
        const y2 = this.y + Math.sin(a) * this.length
        ctx.lineTo(x2, y2);
        return {x: x2, y: y2}
    }

    _drawHole()
    {
        ctx.save();
        ctx.fillStyle = `rgba(120,150,205,${this.alpha})`;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(this.x, this.y, 5, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
        return {x: this.x, y: this.y}
    }

}

function update()
{
    const w = window.innerWidth;
    const h = window.innerHeight;
    if (canvas.width !== w || canvas.height !== h) {
        canvas.width = w;
        canvas.height = h;
        canvas.style.width = w + "px";
        canvas.style.height = h + "px";
        container.style.width = w + "px";
        container.style.height = h + "px";
    }
    ctx.clearRect(0, 0, w, h);
    elements.circuits.forEach(circuit => circuit.update(w, h));
    elements.codelines.forEach(line => line.update(w, h));
}

function createCodeBlock(age, maxTries = 50) {
    const winW = window.innerWidth;
    const winH = window.innerHeight;
    const text = C_CODE_POOL[Math.floor(Math.random() * C_CODE_POOL.length)];
    const block = new CodeBlock(text, age);
    const {width, height} = block.getDimension();
    if (width >= winW || height >= winH) return null;

    let collide = false;
    for (let tries = 0; tries < maxTries; tries++) {
        collide = false;
        const x = Math.floor(Math.random() * (winW - width));
        const y = Math.floor(Math.random() * (winH - height));
        block.setPos(x, y);
        for (const e of elements.codelines) {
            if (e.collide(block)) {
                collide = true;
                break;
            }
        }
        if (!collide) {
            elements.codelines.push(block);
            return block;
        }
    }
    return null;
}

let running = true;
let last = 0;
const fps = 30;
const frameTime = 1000 / fps;
function animate(t) {
    if (running && t - last >= frameTime) {
        last = t;

        // cleanup old elements and create new if needed
        elements.codelines = elements.codelines.filter(e => !e.isDead());
        elements.circuits  = elements.circuits.filter(e => !e.isDead());

        // fill up
        while (elements.codelines.length < maxCodeBlocks) {
            for (let tries = 0; tries < 50; tries++) {
                const res = createCodeBlock(0);
                if (res) {
                    break;
                }
            }
        }

        // set age
        elements.codelines.concat(elements.circuits).forEach((el) => el.getOlder(frameTime))

        update();
    }
    requestAnimationFrame(animate);
}
document.addEventListener("visibilitychange", () => {
    running = !document.hidden;
});

for (let i = 0; i < maxCodeBlocks; i++) {
    const ageStep = maxAgeBlock / maxCodeBlocks;
    for (let tries = 0; tries < 50; tries++) {
        const res = createCodeBlock(ageStep * (i + 1));
        if (res) {
            break;
        }
    }
}
elements.circuits.push(new Circuit(0));
elements.circuits.push(new Circuit(0));

elements.circuits.push(new Circuit(0));

elements.circuits.push(new Circuit(0));

elements.circuits.push(new Circuit(0));





animate();
