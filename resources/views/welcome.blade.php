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
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow: hidden;
            background-color: #4f46e5;
            cursor: none; /* Sembunyikan kursor default */
        }
        html, body, a, button {
            cursor: none;
        }

        /* Latar Belakang Liquid */
        #liquid-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 3; 
            filter: blur(20px) contrast(25);
        }
        .gradient-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 2;
            background: #3b82f6;
            /* mix-blend-mode: screen;  */
        }

        /* Kursor Neon */
        .neon-cursor {
            position: fixed;
            top: 0; left: 0;
            pointer-events: none;
            z-index: 9999;
            border-radius: 50%;
        }
        .cursor-main {
            width: 20px;
            height: 20px;
            background-color: #ec6517;
            box-shadow: 0 0 10px #ec6517, 0 0 20px #ec6517;
        }
        .cursor-trail {
            width: 40px;
            height: 40px;
            border: 2px solid #ec6517;
        }
        .cursor-glow {
            width: 60px;
            height: 60px;
            background-color: #ec6517;
            opacity: 0.4;
            filter: blur(15px);
        }

        /* Efek Coretan */
        .scribble-canvas {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        /* Konten Utama */
        .content-container {
            position: relative; 
            overflow: hidden;
            z-index: 3;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            box-sizing: border-box;
        }
        .main-content {
            opacity: 0;
            filter: blur(15px);
        }
        .heroHeading, .herosubHeading, .right-anim-item {
            opacity: 0;
        }
        .herosubHeading-word {
            display: inline-block;
            margin-right: 0.25em;
        }
        .heroContainer {
            width: 100%;
            height: 100%;
            max-height: 900px;
            max-width: 1440px;
            display: flex;
            gap: 30px;
            z-index: 5;
        }
        .container-left {
            position: relative;
            background-color: #ffffff; 
            border-radius: 15px;
            padding: 40px;
            width: 61.8%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: .5s ease-in-out;
        }

        .container-left:hover {
            background-color: #ec6517;
        }
        .container-left:hover .heroHeading {
            color: #ffffff;
        }
        .container-left:hover .herosubHeading {
            color: #ffffff;
        }
        .heroHeading{
            font-family:Verdana, Geneva, Tahoma, sans-serif;
            font-size: 4.5rem;
            font-weight: 900;
            transition: .5s ease-in-out;
            line-height: .9;
            color: #1e293b;
        }
        .herosubHeading{
            font-size: 1.25rem;
            font-weight: 400;
            line-height: 1.5;
            transition: .5s ease-in-out;
            color: #475569;
            max-width: 500px;
            font-family: monospace;
        }
        .container-right {
            width: 38.2%;
            display: flex;
            flex-direction: column;
            gap: 0px;
            justify-content: space-between;
        }
        .top {
            text-align: right;
            padding: 0;
            line-height: 1;
            color: white;
            font-weight: 800;
            font-size: 1.7rem;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .top a {
            transform: scale(1);
            transition: .5s ease-in-out;
        }

        .top a:hover {
            transform: scale(1.05);
            transition: .5s ease-in-out;
        }

        .socialLink:hover {
            color:#ec6517;
        }

        .bot {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .heroObject{
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }
        .heroButton {
            position: relative;
            width: 100%;
            /* PERUBAHAN DI SINI: Tinggi diperbesar */
            height: 150px;
            background-color: #fff;
            font-size: 1.75rem;
            font-weight: 800;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            text-decoration: none;
            color: #1e293b;
           
            transition: 0.5s ease-in-out;
        }
        .heroButton:hover {
            background-color: #ec6517;
            color: white;
        }
        /* PERUBAHAN DI SINI: Objek diperkecil */
        .shape {
            width: 150px;
            height: 150px;
        }
        .circle {
            border: 8px solid white;
            border-radius: 50%;
        }
        .triangle svg {
            width: 100%;
            height: 100%;
        }

        @media (max-width: 700px) {
            body {
                width: 100vw;
            }

            .content-container {
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            height: 100dvh;
        
            }

            .heroContainer {
                height: 100%;
                display: flex;
                
                flex-direction: column;
                max-height: 100dvh;
            }

            .container-left {
                position: relative;
                height: 61.8%;
                background-color: #ffffff; 
                border-radius: 15px;
                padding: 40px;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                transition: .5s ease-in-out;
            }

            .container-right {
                width: 100%;
                height: 38.2%;
                display: flex;
                flex-direction: column;
                gap: 0px;
                justify-content: space-between;
            }

            .heroButton {
                position: relative;
                width: 90%;
                /* PERUBAHAN DI SINI: Tinggi diperbesar */
                height: 50%;
                background-color: #fff;
                font-size: 1.75rem;
                font-weight: 800;
                padding: 20px;
                border-radius: 15px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center !important;
                text-decoration: none;
                color: #1e293b;
                transition: 0.5s ease-in-out;
            }

            .heroHeading{
                font-family:Verdana, Geneva, Tahoma, sans-serif;
                font-size: 3rem;
                font-weight: 900;
                transition: .5s ease-in-out;
                line-height: .9;
                color: #1e293b;
            }
            .herosubHeading{
                font-size: .9rem;
                font-weight: 400;
                line-height: 1.5;
                transition: .5s ease-in-out;
                color: #475569;
                max-width: 500px;
                font-family: monospace;
            }

            .top {
                order: 2;
                font-size: 105%;
                gap: 20px;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                height: 20%;
            }

            .bot {
                height: 80%;
                justify-content: center;
                align-items: center;
            }
        }
        
        @media (min-width: 700px) and (max-width: 1024px) {
            
            body {
                width: 100vw;
            }

            .content-container {
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            height: 100dvh;
        
            }

            .heroContainer {
                height: 100%;
                display: flex;
               
                flex-direction: column;
                max-height: 100dvh;
            }

            .container-left {
                position: relative;
                height: 61.8%;
                background-color: #ffffff; 
                border-radius: 15px;
                padding: 40px;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                transition: .5s ease-in-out;
            }

            .container-right {
                width: 100%;
                height: 38.2%;
                display: flex;
                flex-direction: column;
                gap: 0px;
                justify-content: space-between;
            }

            .heroButton {
                position: relative;
                width: 90%;
                /* PERUBAHAN DI SINI: Tinggi diperbesar */
                height: 50%;
                background-color: #fff;
                font-size: 1.75rem;
                font-weight: 800;
                padding: 20px;
                border-radius: 15px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center !important;
                text-decoration: none;
                color: #1e293b;
                transition: 0.5s ease-in-out;
            }

            .heroHeading{
                font-family:Verdana, Geneva, Tahoma, sans-serif;
                font-size: 5rem;
                font-weight: 900;
                transition: .5s ease-in-out;
                line-height: .9;
                color: #1e293b;
            }
            .herosubHeading{
                font-size: 1.5rem;
                font-weight: 400;
                line-height: 1.5;
                transition: .5s ease-in-out;
                color: #475569;
                max-width: 800px;
                font-family: monospace;
            }

            .top {
                order: 2;
                font-size: 200%;
                gap: 20px;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                height: 20%;
            }

            .bot {
                height: 80%;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
</head>
<body class="antialiased">
    <canvas id="liquid-canvas"></canvas>
    <div class="gradient-overlay"></div>
    <div class="neon-cursor cursor-main"></div>
    <div class="neon-cursor cursor-trail"></div>
    <div class="neon-cursor cursor-glow"></div>

    <div class="content-container">
        <div class="heroContainer main-content">
            <div class="container-left scribble-target">
                <h1 class="heroHeading">Arena<br>Latih</h1>
                <h3 id="subheading" class="herosubHeading">Tempat terbaik untuk mengasah kemampuan, berlatih soal, dan meraih kemajuan. Siap untuk tantangan di berbagai bidang?</h3>
            </div>
            <div class="container-right">
                <div class="top">
                    
                        <a class="socialLink right-anim-item" href="#">Tribute</a>
                        <a class="socialLink right-anim-item" href="#">Tiktok</a>
                        <a class="socialLink right-anim-item" href="#">Instagram</a>
                    
                </div>
                <div class="bot">
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="startButton heroButton right-anim-item scribble-target">
                            <p>Lanjutkan Latihan</p>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="startButton heroButton right-anim-item scribble-target">
                            <p>Mulai Latihan</p>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

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
            
            // --- BAGIAN 2: EFEK KURSOR NEON ---
            const cursorMain = document.querySelector('.cursor-main');
            const cursorTrail = document.querySelector('.cursor-trail');
            const cursorGlow = document.querySelector('.cursor-glow');
            let isClicking = false;
            let isHovering = false;
            gsap.set([cursorMain, cursorTrail, cursorGlow], { xPercent: -50, yPercent: -50, opacity: 0, scale: 0 });
            gsap.to([cursorMain, cursorTrail, cursorGlow], { duration: 0.5, opacity: 1, scale: 1, delay: 0.5 });
            window.addEventListener('mousemove', e => {
                gsap.to(cursorMain, { duration: 0.3, x: e.clientX, y: e.clientY, ease: 'power2.out' });
                gsap.to(cursorTrail, { duration: 0.6, x: e.clientX, y: e.clientY, ease: 'power2.out' });
                gsap.to(cursorGlow, { duration: 1.0, x: e.clientX, y: e.clientY, ease: 'power2.out' });
            });
            window.addEventListener('mousedown', () => { isClicking = true; updateCursorState(); });
            window.addEventListener('mouseup', () => { isClicking = false; updateCursorState(); });
            document.addEventListener('mouseover', e => {
                if (e.target.matches('a, button, .scribble-target')) {
                    isHovering = true;
                    updateCursorState();
                }
            });
            document.addEventListener('mouseout', e => {
                if (e.target.matches('a, button, .scribble-target')) {
                    isHovering = false;
                    updateCursorState();
                }
            });
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
                constructor(width, height) { this.width = width; this.height = height; this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 0.5; this.vy = (Math.random() - 0.5) * 0.5; this.r = Math.random() * 40 + 40; }
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
                            if (mouse.x < ball.x && ball.x < liquidCanvas.width - ball.r) ball.x += 3;
                            if (mouse.x > ball.x && ball.x > ball.r) ball.x -= 3;
                            if (mouse.y < ball.y && ball.y < liquidCanvas.height - ball.r) ball.y += 3;
                            if (mouse.y > ball.y && ball.y > ball.r) ball.y -= 3;
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
        });
    </script>
    @include('components.cookie-banner')
</body>
</html>
