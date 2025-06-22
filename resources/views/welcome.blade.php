<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ArenaLatih - Asah Kemampuan, Raih Kemajuan</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- GSAP (GreenSock Animation Platform) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <style>
     #liquid-canvas,.gradient-overlay{width:100%;height:100%;position:fixed}.neon-cursor,.scribble-canvas{top:0;left:0;pointer-events:none}a,body,button,html{cursor:none}#liquid-canvas{top:0;left:0;z-index:3;filter:blur(20px) contrast(25)}.gradient-overlay{top:0;left:0;z-index:2;background:#3b82f6}.container-left:hover,.cursor-glow,.cursor-main,.heroButton:hover{background-color:#ec6517}.neon-cursor{position:fixed;z-index:9999;border-radius:50%}.cursor-main{width:20px;height:20px;box-shadow:0 0 10px #ec6517,0 0 20px #ec6517}.cursor-trail{width:40px;height:40px;border:2px solid #ec6517}.cursor-glow{width:60px;height:60px;opacity:.4;filter:blur(15px)}.container-left,.heroButton{background-color:#fff;border-radius:15px;transition:.5s ease-in-out}.scribble-canvas{position:absolute;width:100%;height:100%;z-index:10}.content-container{position:relative;overflow:hidden;z-index:3;height:100vh;display:flex;justify-content:center;align-items:center;padding:1rem;box-sizing:border-box}.main-content{opacity:0;filter:blur(15px)}.heroHeading,.herosubHeading,.right-anim-item{opacity:0}.herosubHeading-word{display:inline-block;margin-right:.25em}.heroContainer{width:100%;height:100%;max-height:900px;max-width:1440px;display:flex;gap:30px;z-index:5}.container-left{position:relative;padding:40px;width:61.8%;display:flex;flex-direction:column;justify-content:space-between}.container-right,.top{flex-direction:column}.container-left:hover .heroHeading,.container-left:hover .herosubHeading{color:#fff}.heroHeading{font-family:Verdana,Geneva,Tahoma,sans-serif;font-size:4.5rem;font-weight:900;transition:.5s ease-in-out;line-height:.9;color:#1e293b}.herosubHeading{font-size:1.25rem;font-weight:400;line-height:1.5;transition:.5s ease-in-out;color:#475569;max-width:500px;font-family:monospace}.heroButton,.top{font-weight:800;display:flex}.container-right{width:38.2%;display:flex;gap:0;justify-content:space-between}.top{text-align:right;padding:0;line-height:1;color:#fff;font-size:1.7rem;gap:5px}.top a{transform:scale(1);transition:.5s ease-in-out}.top a:hover{transform:scale(1.05);transition:.5s ease-in-out}.socialLink:hover{color:#ec6517}.bot{display:flex;flex-direction:column;align-items:flex-start;gap:10px}.heroObject{flex-grow:1;display:flex;align-items:center;justify-content:space-between;gap:40px}.heroButton{position:relative;width:100%;height:150px;font-size:1.75rem;padding:20px;flex-direction:column;justify-content:flex-end;text-decoration:none;color:#1e293b}.heroButton:hover{color:#fff}.shape{width:150px;height:150px}.circle{border:8px solid #fff;border-radius:50%}.triangle svg{width:100%;height:100%}@media (max-width:700px){.container-left,.heroButton{position:relative;background-color:#fff;border-radius:15px}.heroButton,.heroHeading{color:#1e293b;transition:.5s ease-in-out}.container-left,.heroButton,.heroHeading,.herosubHeading{transition:.5s ease-in-out}body{width:100vw}.content-container{justify-content:center;align-items:flex-start;width:100%;height:100dvh}.heroContainer{height:100%;display:flex;flex-direction:column;max-height:100dvh}.container-left,.container-right{width:100%;flex-direction:column;display:flex}.container-left{height:61.8%;padding:40px;justify-content:space-between}.container-right{height:38.2%;gap:0;justify-content:space-between}.heroButton{width:90%;height:50%;font-size:1.75rem;font-weight:800;padding:20px;display:flex;flex-direction:column;justify-content:center;align-items:center!important;text-decoration:none}.heroHeading{font-family:Verdana,Geneva,Tahoma,sans-serif;font-size:3rem;font-weight:900;line-height:.9}.herosubHeading{font-size:.9rem;font-weight:400;line-height:1.5;color:#475569;max-width:500px;font-family:monospace}.top{order:2;font-size:105%;gap:20px;flex-direction:row;align-items:center;justify-content:center;height:20%}.bot{height:80%;justify-content:center;align-items:center}}@media (min-width:700px) and (max-width:1024px){.container-left,.heroButton{position:relative;background-color:#fff;border-radius:15px}.heroButton,.heroHeading{color:#1e293b;transition:.5s ease-in-out}.container-left,.heroButton,.heroHeading,.herosubHeading{transition:.5s ease-in-out}body{width:100vw}.content-container{justify-content:center;align-items:flex-start;width:100%;height:100dvh}.heroContainer{height:100%;display:flex;flex-direction:column;max-height:100dvh}.container-left,.container-right{width:100%;flex-direction:column;display:flex}.container-left{height:61.8%;padding:40px;justify-content:space-between}.container-right{height:38.2%;gap:0;justify-content:space-between}.heroButton{width:90%;height:50%;font-size:1.75rem;font-weight:800;padding:20px;display:flex;flex-direction:column;justify-content:center;align-items:center!important;text-decoration:none}.heroHeading{font-family:Verdana,Geneva,Tahoma,sans-serif;font-size:5rem;font-weight:900;line-height:.9}.herosubHeading{font-size:1.5rem;font-weight:400;line-height:1.5;color:#475569;max-width:800px;font-family:monospace}.top{order:2;font-size:200%;gap:20px;flex-direction:row;align-items:center;justify-content:center;height:20%}.bot{height:80%;justify-content:center;align-items:center}}
    </style>
</head>
<body class="antialiased"><canvas id="liquid-canvas"></canvas><div class="gradient-overlay"></div><div class="neon-cursor cursor-main"></div><div class="neon-cursor cursor-trail"></div><div class="neon-cursor cursor-glow"></div><div class="content-container"><div class="heroContainer main-content"><div class="container-left scribble-target"><h1 class="heroHeading">Arena<br>Latih</h1><h3 id="subheading" class="herosubHeading">Tempat terbaik untuk mengasah kemampuan,berlatih soal,dan meraih kemajuan. Siap untuk tantangan di berbagai bidang?</h3></div><div class="container-right"><div class="top"><a class="socialLink right-anim-item" href="https://saweria.co/dewanggaszz">Tribute</a><a class="socialLink right-anim-item" href="https://www.tiktok.com/@gazeofdew?_t=ZS-8xQ4JQmKlIO&_r=1">Tiktok</a><a class="socialLink right-anim-item" href="https://www.instagram.com/gazeofdew?igsh=cjBoYTdhYWRhaWNs ">Instagram</a></div><div class="bot">@auth <a href="{{ url('/dashboard') }}" class="startButton heroButton right-anim-item scribble-target"><p>Lanjutkan Latihan</p></a>@else <a href="{{ route('login') }}" class="startButton heroButton right-anim-item scribble-target"><p>Mulai Latihan</p></a>@endauth </div></div></div></div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- BAGIAN 1: ANIMASI PEMBUKA (GSAP) ---
            document.body.style.overflow = 'hidden';
            const mainContent = document.querySelector('.main-content');
            const heroHeading = document.querySelector('.heroHeading');
            const subheading = document.getElementById('subheading');
            const rightSideItems = document.querySelectorAll('.right-anim-item');

            const subheadingText = subheading.textContent;
            const words = subheadingText.split(' ');
            subheading.innerHTML = words.map(word => `<span class="herosubHeading-word opacity-0">${word}</span>`).join(' ');

            const tl = gsap.timeline({
                onComplete: () => { document.body.style.overflow = 'auto'; }
            });

            tl
                .to(mainContent, { duration: 1.2, opacity: 1, filter: 'blur(0px)', ease: 'power2.out' })
                .fromTo(heroHeading, { opacity: 0, filter: 'blur(10px)' }, { duration: 1, opacity: 1, filter: 'blur(0px)', ease: 'power2.out' }, "-=0.8")
                .to(subheading, { opacity: 1, duration: 0 }, "-=0.8")
                .fromTo(document.querySelectorAll('.herosubHeading-word'), { opacity: 0, filter: 'blur(5px)', y: 10 }, { duration: 1, opacity: 1, filter: 'blur(0px)', y: 0, stagger: 0.08, ease: 'power2.out' }, "-=0.6")
                .fromTo(rightSideItems, { opacity: 0, filter: 'blur(10px)' }, { duration: 1, opacity: 1, filter: 'blur(0px)', stagger: 0.2, ease: 'power2.out' }, "-=0.5");
            
            // --- BAGIAN 2: EFEK KURSOR NEON & SENTUHAN ---
            // Tugas: Mengontrol kursor kustom, termasuk gerakannya, saat hover,
            // klik, dan saat disentuh di layar mobile.
            // =================================================================
            const cursorMain = document.querySelector('.cursor-main');
            const cursorTrail = document.querySelector('.cursor-trail');
            const cursorGlow = document.querySelector('.cursor-glow');
            const cursors = [cursorMain, cursorTrail, cursorGlow];
            let isClicking = false;
            let isHovering = false;

            gsap.set(cursors, { xPercent: -50, yPercent: -50 });

            function moveCursor(x, y) {
                gsap.to(cursorMain, { duration: 0.3, x, y, ease: 'power2.out' });
                gsap.to(cursorTrail, { duration: 0.6, x, y, ease: 'power2.out' });
                gsap.to(cursorGlow, { duration: 1.0, x, y, ease: 'power2.out' });
            }

            function updateCursorState() {
                if (isClicking) {
                    gsap.to(cursorMain, { duration: 0.2, scale: 0.8 });
                } else if (isHovering) {
                    gsap.to(cursorMain, { duration: 0.2, scale: 1.2 });
                    gsap.to(cursorTrail, { duration: 0.3, scale: 1.5, borderColor: '#ff9632', borderWidth: '3px' });
                    gsap.to(cursorGlow, { duration: 0.3, scale: 2, opacity: 0.8 });
                } else {
                    gsap.to(cursorMain, { duration: 0.2, scale: 1 });
                    gsap.to(cursorTrail, { duration: 0.3, scale: 1, borderColor: '#ec6517', borderWidth: '2px' });
                    gsap.to(cursorGlow, { duration: 0.3, scale: 1, opacity: 0.4 });
                }
            }
            // --- BAGIAN 3: EFEK LATAR BELAKANG CAIRAN (METABALLS) ---
            const liquidCanvas = document.getElementById('liquid-canvas');
            const liquidCtx = liquidCanvas.getContext('2d');
            let metaballs = [];
            const mouse = { x: null, y: null, radius: 150 };

            class Metaball {
                constructor(width, height) { this.width = width; this.height = height; this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 5; this.vy = (Math.random() - 0.5) * 5; this.r = Math.random() * 45 + 45; }
                update() { this.x += this.vx; this.y += this.vy; if (this.x > this.width + this.r || this.x < -this.r) this.vx = -this.vx; if (this.y > this.height + this.r || this.y < -this.r) this.vy = -this.vy; }
                draw() { liquidCtx.beginPath(); liquidCtx.arc(this.x, this.y, this.r, 0, 2 * Math.PI); liquidCtx.fillStyle = 'white'; liquidCtx.fill(); }
            }
            
            function initLiquid() {
                liquidCanvas.width = window.innerWidth; liquidCanvas.height = window.innerHeight; metaballs = [];
                for (let i = 0; i < 15; i++) { metaballs.push(new Metaball(liquidCanvas.width, liquidCanvas.height)); }
            }

            function animateLiquid() {
                liquidCtx.clearRect(0, 0, liquidCanvas.width, liquidCanvas.height);
                metaballs.forEach(ball => {
                    if (mouse.x) {
                        let dxMouse = ball.x - mouse.x; let dyMouse = ball.y - mouse.y;
                        let distance = Math.sqrt(dxMouse * dxMouse + dyMouse * dyMouse);
                        if (distance < mouse.radius + ball.r) {
                            if (mouse.x < ball.x && ball.x < liquidCanvas.width - ball.r) ball.x += 2;
                            if (mouse.x > ball.x && ball.x > ball.r) ball.x -= 2;
                            if (mouse.y < ball.y && ball.y < liquidCanvas.height - ball.r) ball.y += 2;
                            if (mouse.y > ball.y && ball.y > ball.r) ball.y -= 2;
                        }
                    }
                    ball.update(); ball.draw(); 
                });
                requestAnimationFrame(animateLiquid);
            }
            window.addEventListener('resize', initLiquid);
            window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });
            window.addEventListener('mouseout', () => { mouse.x = null; mouse.y = null; });
            initLiquid();
            animateLiquid();

            // --- BAGIAN 4: EFEK CORETAN (SCRIBBLE) ---
            const scribbleTargets = document.querySelectorAll('.scribble-target');
            scribbleTargets.forEach(target => {
                let scribbleCanvas, scribbleCtx, lastPoint, isDrawing = false;
                target.addEventListener('mouseenter', () => {
                    scribbleCanvas = document.createElement('canvas'); target.appendChild(scribbleCanvas);
                    scribbleCanvas.classList.add('scribble-canvas');
                    scribbleCanvas.width = target.offsetWidth; scribbleCanvas.height = target.offsetHeight;
                    scribbleCtx = scribbleCanvas.getContext('2d'); isDrawing = true;
                });
                target.addEventListener('mousemove', e => {
                    if (!isDrawing) return;
                    const rect = target.getBoundingClientRect(); const x = e.clientX - rect.left; const y = e.clientY - rect.top;
                    if (lastPoint) {
                        scribbleCtx.beginPath(); scribbleCtx.moveTo(lastPoint.x, lastPoint.y); scribbleCtx.lineTo(x, y);
                        scribbleCtx.strokeStyle = 'rgba(20, 20, 20, 0.7)'; scribbleCtx.lineWidth = 2; scribbleCtx.lineCap = 'round'; scribbleCtx.stroke();
                    }
                    lastPoint = { x, y };
                });
                target.addEventListener('mouseleave', () => {
                    isDrawing = false; lastPoint = null;
                    if (scribbleCanvas) {
                        gsap.to(scribbleCanvas, { opacity: 0, duration: 0.5, onComplete: () => scribbleCanvas.remove() });
                    }
                });
            });
            // =================================================================
            // --- BAGIAN 5: PENGELOLA EVENT TERPUSAT (MOUSE & SENTUHAN) ---
            // =================================================================
            
            // =================================================================
            // --- BAGIAN 5: PENGELOLA EVENT TERPUSAT (MOUSE & SENTUHAN) ---
            // Ini adalah bagian yang telah dirombak total untuk menyatukan semua logika.
            // =================================================================
            
            let currentScribbleTarget = null;
            
            function onPointerMove(e) {
                // Ambil koordinat dari mouse atau sentuhan pertama
                const x = e.touches ? e.touches[0].clientX : e.clientX;
                const y = e.touches ? e.touches[0].clientY : e.clientY;
                
                // Selalu gerakkan kursor dan update posisi mouse untuk liquid
                moveCursor(x, y);
                mouse.x = x;
                mouse.y = y;

                // Cek elemen apa yang ada di bawah pointer/jari
                const targetElement = document.elementFromPoint(x, y);
                const scribbleTarget = targetElement ? targetElement.closest('.scribble-target') : null;

                // Logika untuk memulai atau menghentikan coretan
                if (scribbleTarget) {
                    if (currentScribbleTarget !== scribbleTarget) {
                        stopScribble(); // Hentikan coretan lama jika pindah target
                        startScribble(scribbleTarget, x, y);
                        currentScribbleTarget = scribbleTarget;
                    }
                    drawScribble(x, y);
                } else {
                    if (currentScribbleTarget) {
                        stopScribble();
                        currentScribbleTarget = null;
                    }
                }
            }
            
            function onPointerDown(e) {
                isClicking = true;
                isHovering = e.target.closest('a, button');
                updateCursorState();
            }
            
            function onPointerUp() {
                isClicking = false;
                isHovering = false;
                updateCursorState();
            }
            
            // --- Event Listeners yang Disederhanakan ---
            
            // Mouse Events
            window.addEventListener('mousemove', onPointerMove);
            window.addEventListener('mousedown', onPointerDown);
            window.addEventListener('mouseup', onPointerUp);
            window.addEventListener('mouseleave', () => { // Hentikan coretan jika mouse keluar window
                if (currentScribbleTarget) {
                    stopScribble();
                    currentScribbleTarget = null;
                }
            });

            // Touch Events
            window.addEventListener('touchmove', onPointerMove, { passive: true });
            window.addEventListener('touchstart', e => {
                gsap.to(cursors, { duration: 0.3, opacity: 1, scale: 1 });
                onPointerDown(e);
                // Memulai coretan saat sentuhan pertama, sama seperti mouse down
                const touch = e.touches[0];
                const scribbleTarget = document.elementFromPoint(touch.clientX, touch.clientY)?.closest('.scribble-target');
                startScribble(scribbleTarget, touch.clientX, touch.clientY);
                currentScribbleTarget = scribbleTarget;
            }, { passive: true });
            window.addEventListener('touchend', () => {
                gsap.to(cursors, { duration: 0.3, opacity: 0, scale: 0 });
                onPointerUp();
                // Menghentikan coretan saat sentuhan diangkat
                stopScribble();
                currentScribbleTarget = null;
            });
        });
    </script>
    @include('components.cookie-banner')
</body>
</html>
