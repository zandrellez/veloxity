/**
 * Vanilla translation of Glyph Portal text analysis and zoom[cite: 1]
 */
(function() {
    const clamp = (n, a = 0, b = 1) => Math.min(b, Math.max(a, n));
    const smooth = (a, b, n) => { const t = clamp((n - a) / (b - a)); return t * t * (3 - 2 * t); };

    function interior(context, char, font) {
        context.font = font;
        const m = context.measureText(char);
        const pad = 8;
        const left = Math.ceil(m.actualBoundingBoxLeft || 0);
        const ascent = Math.ceil(m.actualBoundingBoxAscent || 0);
        const width = Math.max(1, Math.ceil((m.actualBoundingBoxLeft || 0) + (m.actualBoundingBoxRight || 0)) + pad * 2);
        const height = Math.max(1, Math.ceil((m.actualBoundingBoxAscent || 0) + (m.actualBoundingBoxDescent || 0)) + pad * 2);
        
        context.canvas.width = width;
        context.canvas.height = height;
        context.font = font;
        context.fontKerning = "none";
        context.fillText(char, pad + left, pad + ascent);
        
        const pixels = context.getImageData(0, 0, width, height).data;
        const rows = new Uint16Array(width + 1);
        let size = 0, bx = 0, by = 0;
        
        for (let y = 0; y < height; y++) {
            let diagonal = 0;
            for (let x = 0; x < width; x++) {
                const above = rows[x + 1];
                rows[x + 1] = pixels[(y * width + x) * 4 + 3] > 245 ? Math.min(above, rows[x], diagonal) + 1 : 0;
                diagonal = above;
                if (rows[x + 1] > size) { size = rows[x + 1]; bx = x; by = y; }
            }
        }
        if (size < 3) return null;
        return { x: (bx + 1 - size / 2 - pad - left) / 3, y: (by + 1 - size / 2 - pad - ascent) / 3, radius: (size / 2 - 1) / 3 };
    }

    const section = document.getElementById('gp-veloxity');
    if (!section) return;
    const nextSection = section.nextElementSibling;

    const text = "VELOXITY";
    const characters = Array.from(text).map((char, index) => ({ char, index }));
    const choicesContainer = document.getElementById('gp-choices-container');
    const selectContainer = document.getElementById('gp-select-container');

    characters.forEach(({char, index}, i) => {
        const btn = document.createElement('button');
        btn.type = 'button'; btn.role = 'radio'; btn.dataset.gpLetter = index;
        btn.setAttribute('aria-label', `${char}, letter ${i + 1}`);
        choicesContainer.appendChild(btn);

        const opt = document.createElement('option');
        opt.value = index; opt.textContent = `${i + 1} · ${char}`;
        selectContainer.appendChild(opt);
    });

    const pin = section.querySelector('[data-gp-pin]');
    const field = section.querySelector('[data-gp-field]');
    const clip = section.querySelector('#gp-veloxity-clip');
    const glyph = section.querySelector('[data-gp-glyph]');
    const marks = section.querySelector('[data-gp-marks]');
    const buttons = Array.from(choicesContainer.querySelectorAll('button'));
    const motion = window.matchMedia("(prefers-reduced-motion: reduce)");

    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d", { willReadFrequently: true });

    let W = 1, H = 1, travel = 1, startScale = 1, endScale = 1;
    let center = { x: 0, y: 0 }, target = null;
    let candidates = [], letters = [];
    let bounds = { x: 0, y: 0, width: 1, height: 1 };
    let raf = 0, active = true;

    function readInk() {
        const fontStr = "700 100px 'Syne', 'Arial Black', Arial, sans-serif";
        const scanFont = "700 300px 'Syne', 'Arial Black', Arial, sans-serif";
        context.font = fontStr;
        const metrics = context.measureText(text);
        
        bounds = { 
            x: -(metrics.actualBoundingBoxLeft || 0), 
            y: -(metrics.actualBoundingBoxAscent || 0),
            width: (metrics.actualBoundingBoxLeft || 0) + (metrics.actualBoundingBoxRight || text.length * 70),
            height: (metrics.actualBoundingBoxAscent || 100) + (metrics.actualBoundingBoxDescent || 20)
        };
        
        center = { x: bounds.x + bounds.width / 2, y: bounds.y + bounds.height / 2 };
        let offset = 0;
        candidates = []; letters = [];
        
        for (const char of text) {
            context.font = fontStr;
            const advances = context.measureText(text.slice(0, offset)).width;
            const m = context.measureText(char);
            letters.push({ index: offset, x: advances - (m.actualBoundingBoxLeft||0), y: -(m.actualBoundingBoxAscent||0), width: m.width, height: 100 });
            
            const found = interior(context, char, scanFont);
            if (found) candidates.push({ ...found, x: found.x + advances, index: offset });
            offset++;
        }
        
        target = candidates.find(c => text[c.index] === 'X') || candidates[0] || null;
    }

    function layout() {
        if (!section.clientWidth) return;
        W = pin.clientWidth;
        H = motion.matches ? Math.min(section.clientHeight * 0.75, 480) : section.clientHeight;
        section.style.setProperty("--gp-height", `${H}px`);
        travel = H * 2.4; 
        
        readInk();
        
        startScale = Math.min(W * 0.84 / bounds.width, (H * .38) / bounds.height);
        endScale = target ? Math.max(startScale, Math.hypot(W, H) / (target.radius * 1.35)) : startScale;

        buttons.forEach(btn => {
            const letter = letters.find(l => l.index === Number(btn.dataset.gpLetter));
            if(letter) {
                Object.assign(btn.style, {
                    left: `${W / 2 + (letter.x - center.x) * startScale}px`,
                    top: `${H * .46 + (letter.y - center.y) * startScale}px`,
                    width: `${letter.width * startScale}px`,
                    height: `${letter.height * startScale}px`,
                });
            }
        });
        
        section.classList.add('ready');
        section.classList.toggle('motion-on', !motion.matches);
    }

    function paint(progress) {
        if(!target) return;
        const p = motion.matches ? 0 : progress;
        const t = clamp(p / 0.78);
        const eased = t < 0.5 ? 4 * t ** 3 : 1 - (-2 * t + 2) ** 3 / 2;
        const scale = Math.exp(Math.log(startScale) + Math.log(endScale / startScale) * eased);
        const blend = endScale === startScale ? 0 : (1 / scale - 1 / startScale) / (1 / endScale - 1 / startScale);
        
        const cx = center.x + (target.x - center.x) * blend;
        const cy = center.y + (target.y - center.y) * blend;
        const roll = -4 * smooth(0.06, 0.5, t) * (1 - smooth(0.62, 0.92, t));
        
        const radians = roll * Math.PI / 180;
        const dx = W / 2 / scale, dy = (H * .46 + H * .04 * eased) / scale;
        
        clip.setAttribute("transform", `scale(${scale}) rotate(${roll})`);
        glyph.setAttribute("transform", `translate(${Math.cos(radians) * dx + Math.sin(radians) * dy - cx} ${-Math.sin(radians) * dx + Math.cos(radians) * dy - cy})`);
        
        section.classList.toggle('choosing', p < .04);
        field.style.clipPath = t >= 1 ? "none" : `url(#gp-veloxity-clip)`;
        section.style.setProperty("--gp-caption", String(1 - smooth(0.01, 0.16, p)));
        section.style.setProperty("--gp-reveal", String(smooth(0.78, 0.9, p)));
        section.classList.toggle('entered', p >= 0.9);
    }

    function frame() {
        // Calculate progress based on the container's internal scroll position
        const position = clamp(section.scrollTop / travel);
        paint(position);
    }

    window.addEventListener('resize', layout);
    
    // Tie the frame update directly to the scroll event
    section.addEventListener('scroll', () => {
        requestAnimationFrame(frame);
    });

    // Capture wheel input only while the hero is the active scroll stage.
    section.addEventListener('wheel', (e) => {
        const atHeroStart = section.scrollTop <= 0;
        const atHeroEnd = section.scrollTop >= section.scrollHeight - section.clientHeight - 1;
        const atPageTop = window.scrollY === 0;
        const heroComplete = section.classList.contains('entered') || atHeroEnd;
        const nextSectionVisible = window.scrollY > 0 && nextSection && (() => {
            const bounds = nextSection.getBoundingClientRect();
            return bounds.top < window.innerHeight && bounds.bottom > 0;
        })();

        if (nextSectionVisible) {
            e.preventDefault();
            window.scrollBy(0, e.deltaY);
            return;
        }

        if (heroComplete && !atPageTop) return;

        const canAdvance = e.deltaY > 0 && !atHeroEnd;
        const canReverse = e.deltaY < 0 && atPageTop && !atHeroStart;

        if (!canAdvance && !canReverse) return;

        section.scrollTop += e.deltaY;
        e.preventDefault();
    }, { passive: false });
    
    layout();
    frame();

    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => {
            layout();
            frame();
        });
    }
})(); // Keep the existing IIFE closing tag