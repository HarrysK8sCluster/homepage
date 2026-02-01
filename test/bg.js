const canvas = document.getElementById("bg");
const ctx = canvas.getContext("2d");
const container = document.getElementById("background-container");
const maxCodeBlocks = 0;
let elements = {
    codelines: [],
    circuits: []
};
maxAgeBlock = 60000;
let board = null;



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

function randomBetween(min, max) {
    return min + Math.random() * (max - min);
}

function pointInRect(x, y, r) {
    return x >= r.x && x <= r.x + r.w && y >= r.y && y <= r.y + r.h;
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

class Board {

    segments = [];

    constructor() {
        this.generate();
    }

    generate() {
        const w = window.innerWidth;
        const h = window.innerHeight;

        // 1) Random Layout Type
        // 0: horizontal stripes, 1: vertical stripes, 2: diagonal \ stripes, 3: diagonal / stripes
        const mode = Math.floor(Math.random() * 4);

        const count = Math.floor(Math.random() * 4) + 4; // 4..7 stripes
        const padding = 40;

        this.segments = [];

        if (mode === 0) {
            // horizontal stripes
            const stripeH = (h - padding * 2) / count;
            for (let i = 0; i < count; i++) {
                this.segments.push({
                    type: "h",
                    x: 0,
                    y: padding + i * stripeH,
                    w: w,
                    h: stripeH
                });
            }
        } else if (mode === 1) {
            // vertical stripes
            const stripeW = (w - padding * 2) / count;
            for (let i = 0; i < count; i++) {
                this.segments.push({
                    type: "v",
                    x: padding + i * stripeW,
                    y: 0,
                    w: stripeW,
                    h: h
                });
            }
        } else if (mode === 2) {
            // diagonal \ stripes as bounding boxes
            const stripeW = (w - padding * 2) / count;
            for (let i = 0; i < count; i++) {
                this.segments.push({
                    type: "d1",
                    x: padding + i * stripeW,
                    y: 0,
                    w: stripeW,
                    h: h
                });
            }
        } else if (mode === 3) {
            // diagonal / stripes as bounding boxes
            const stripeW = (w - padding * 2) / count;
            for (let i = 0; i < count; i++) {
                this.segments.push({
                    type: "d2",
                    x: padding + i * stripeW,
                    y: 0,
                    w: stripeW,
                    h: h
                });
            }
        }
    }

    getRandomSegment() {
        return this.segments[Math.floor(Math.random() * this.segments.length)];
    }

    update() {
        // optional: draw debug overlay
        // this.drawDebug();
    }

    drawDebug() {
        ctx.save();
        ctx.strokeStyle = "rgba(0,0,0,0.05)";
        ctx.lineWidth = 1;
        for (const s of this.segments) {
            ctx.strokeRect(s.x, s.y, s.w, s.h);
        }
        ctx.restore();
    }
}

class Circuit {

    age = 0;
    maxAge = 0;

    elements = [];
    color = '120,150,205';

    segments = [];       // allowed segments
    maxSegments = 2;
    maxSteps = 150;

    constructor(age = 0) {
        this.age = age;

        // segment plan
        this._chooseSegments();

        // start element
        const start = this._generateStartLine();
        this.elements.push(start);

        let last = start;
        let lastAlign = start.align;

        for (let step = 0; step < this.maxSteps; step++) {

            // random stop -> VIA
            if (Math.random() < 0.05 && step > 15) {
                const via = new CircuitElement(this, 'via', last);
                via.x = last.getEnd().x;
                via.y = last.getEnd().y;
                this.elements.push(via);
                break;
            }

            // random via (hole)
            let newType = "line";
            if (Math.random() >= 0.86) newType = "via";

            const el = new CircuitElement(this, newType, last);
            el.x = last.getEnd().x;
            el.y = last.getEnd().y;

            if (!this._pointAllowed(el.x, el.y)) {
                break;
            }

            if (newType === "via") {
                this.elements.push(el);
                last = el;
                continue;
            }

            // line
            let ok = false;

            for (let tries = 0; tries < 35; tries++) {

                // align based on segment type
                const seg = this._getSegmentForPoint(el.x, el.y);
                if (!seg) break;

                el.align = this._randomAlignForSegment(seg, lastAlign);
                el.length = randomBetween(40, window.innerWidth / 12);

                const end = el.getEnd();

                // keep inside allowed segments
                if (!this._pointAllowed(end.x, end.y)) continue;

                ok = true;
                lastAlign = el.align;
                break;
            }

            if (!ok) {
                // dead end => VIA end
                const via = new CircuitElement(this, 'via', last);
                via.x = last.getEnd().x;
                via.y = last.getEnd().y;
                this.elements.push(via);
                break;
            }

            this.elements.push(el);
            last = el;

            // out of bounds stop
            const end = el.getEnd();
            if (end.x < -10 || end.x > window.innerWidth + 10 || end.y < -10 || end.y > window.innerHeight + 10) {
                break;
            }
        }
    }

    _chooseSegments() {
        this.segments = [];
        const a = board.getRandomSegment();
        this.segments.push(a);

        if (Math.random() < 0.7) {
            // second segment allowed
            let b = board.getRandomSegment();
            if (b === a) b = board.getRandomSegment();
            this.segments.push(b);
        }
    }

    _getSegmentForPoint(x, y) {
        for (const s of this.segments) {
            if (pointInRect(x, y, s)) return s;
        }
        return null;
    }

    _pointAllowed(x, y) {
        return this._getSegmentForPoint(x, y) !== null;
    }

    _randomAlignForSegment(seg, lastAlign) {
        let aligns = [];

        if (seg.type === "h") aligns = [0, 4];
        if (seg.type === "v") aligns = [2, 6];
        if (seg.type === "d1") aligns = [1, 5];
        if (seg.type === "d2") aligns = [3, 7];

        // choose similar direction sometimes
        if (aligns.includes(lastAlign) && Math.random() < 0.7) return lastAlign;

        return aligns[Math.floor(Math.random() * aligns.length)];
    }

    _generateStartLine() {
        // pick one of allowed segments
        const seg = this.segments[0];

        const el = new CircuitElement(this, 'line', null);

        // choose start on segment border (2 sides only)
        if (seg.type === "h") {
            el.y = randomBetween(seg.y + 10, seg.y + seg.h - 10);
            if (Math.random() < 0.5) {
                el.x = seg.x - 5;
                el.align = 0;
            } else {
                el.x = seg.x + seg.w + 5;
                el.align = 4;
            }
        }

        if (seg.type === "v") {
            el.x = randomBetween(seg.x + 10, seg.x + seg.w - 10);
            if (Math.random() < 0.5) {
                el.y = seg.y - 5;
                el.align = 2;
            } else {
                el.y = seg.y + seg.h + 5;
                el.align = 6;
            }
        }

        if (seg.type === "d1") {
            // diagonal \ -> start from left or right side of bounding rect
            el.y = randomBetween(seg.y + 10, seg.y + seg.h - 10);
            if (Math.random() < 0.5) {
                el.x = seg.x - 5;
                el.align = 1; // ↘
            } else {
                el.x = seg.x + seg.w + 5;
                el.align = 5; // ↖
            }
        }

        if (seg.type === "d2") {
            // diagonal / -> start from left or right
            el.y = randomBetween(seg.y + 10, seg.y + seg.h - 10);
            if (Math.random() < 0.5) {
                el.x = seg.x - 5;
                el.align = 7; // ↗
            } else {
                el.x = seg.x + seg.w + 5;
                el.align = 3; // ↙
            }
        }

        el.length = randomBetween(60, window.innerWidth / 10);
        return el;
    }

    getOlder(count) {
        this.age += count;
    }

    isDead() {
        return false;
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

            if (el.type === 'via' && path) {
                ctx.strokeStyle = `rgba(${this.color},1)`;
                ctx.stroke();
                path = false;
            }

            el.update();
            lastEl = el;
        }

        if (path && lastEl) {
            ctx.strokeStyle = `rgba(${this.color},1)`;
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
        if (this.type === 'via') {
            return this._drawVia();
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

    _drawVia() {
        ctx.save();
        ctx.fillStyle = `rgba(0,0,0,0.7)`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, 4, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
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
    board.update();
    elements.circuits.forEach(line => line.update(w, h));
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

board = new Board();
elements.circuits.push(new Circuit(0));
elements.circuits.push(new Circuit(0));





animate();
