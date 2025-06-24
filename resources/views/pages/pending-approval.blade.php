<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Sukses - ArenaLatih</title>
  <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

  <style>
    :root {
      --bg: #000000;
      --text-dark: #ffffff;
      --text-muted: #a0a9c0;
      --accent: #0ea5e9;
      --white: rgba(255, 255, 255, 0.95);
      --card-bg: rgba(0, 0, 0, 0.7);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg);
      color: var(--text-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      min-height: 100vh;
      overflow-x: hidden;
      overflow-y: auto;
      cursor: none;
    }

    /* Water Animation Background */
    .water-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 1;
      pointer-events: none;
    }

    #waterCanvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #000;
    }

    /* 3D Triangles Background */
    .triangles-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 5;
      pointer-events: none;
      overflow: hidden;
    }

    .triangle-3d {
      position: absolute;
      width: 0;
      height: 0;
      opacity: 0.1;
      filter: blur(1px);
      transform-style: preserve-3d;
      animation: float3d 15s ease-in-out infinite;
    }

    .triangle-3d::before {
      content: '';
      position: absolute;
      width: 0;
      height: 0;
      border-style: solid;
      filter: drop-shadow(0 0 20px rgba(0, 150, 255, 0.3));
    }

    .triangle-3d.large {
      border-left: 60px solid transparent;
      border-right: 60px solid transparent;
      border-bottom: 100px solid rgba(0, 150, 255, 0.15);
      animation-duration: 20s;
    }

    .triangle-3d.medium {
      border-left: 40px solid transparent;
      border-right: 40px solid transparent;
      border-bottom: 70px solid rgba(0, 200, 255, 0.12);
      animation-duration: 18s;
    }

    .triangle-3d.small {
      border-left: 25px solid transparent;
      border-right: 25px solid transparent;
      border-bottom: 45px solid rgba(100, 200, 255, 0.1);
      animation-duration: 16s;
    }

    .triangle-3d.outline {
      border-left: 50px solid transparent;
      border-right: 50px solid transparent;
      border-bottom: 2px solid rgba(0, 150, 255, 0.2);
      border-top: 85px solid transparent;
      animation-duration: 22s;
    }

    @keyframes float3d {
      0%, 100% { 
        transform: translateY(0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg);
        opacity: 0.05;
      }
      25% { 
        transform: translateY(-30px) rotateX(15deg) rotateY(45deg) rotateZ(10deg);
        opacity: 0.15;
      }
      50% { 
        transform: translateY(-20px) rotateX(-10deg) rotateY(90deg) rotateZ(-5deg);
        opacity: 0.1;
      }
      75% { 
        transform: translateY(-40px) rotateX(20deg) rotateY(135deg) rotateZ(15deg);
        opacity: 0.12;
      }
    }

    .triangle-wireframe {
      position: absolute;
      width: 80px;
      height: 80px;
      opacity: 0.08;
      transform-style: preserve-3d;
      animation: wireframeRotate 25s linear infinite;
    }

    .triangle-wireframe::before,
    .triangle-wireframe::after {
      content: '';
      position: absolute;
      width: 0;
      height: 0;
      border: 1px solid rgba(0, 150, 255, 0.3);
    }

    .triangle-wireframe::before {
      border-left: 40px solid transparent;
      border-right: 40px solid transparent;
      border-bottom: 70px solid transparent;
      border-bottom-color: rgba(0, 150, 255, 0.2);
    }

    .triangle-wireframe::after {
      border-left: 30px solid transparent;
      border-right: 30px solid transparent;
      border-bottom: 50px solid transparent;
      border-bottom-color: rgba(100, 200, 255, 0.15);
      top: 10px;
      left: 10px;
    }

    @keyframes wireframeRotate {
      0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
      33% { transform: rotateX(120deg) rotateY(120deg) rotateZ(120deg); }
      66% { transform: rotateX(240deg) rotateY(240deg) rotateZ(240deg); }
      100% { transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
    }

    .light-cursor {
      position: absolute;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(0, 150, 255, 0.8) 0%, rgba(0, 100, 200, 0.4) 30%, rgba(0, 50, 150, 0.2) 60%, transparent 100%);
      border-radius: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%);
      z-index: 50;
      filter: blur(2px);
      mix-blend-mode: screen;
    }

    .light-spot {
      position: absolute;
      border-radius: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%);
      background: radial-gradient(circle, rgba(0, 200, 255, 0.6) 0%, rgba(0, 150, 255, 0.3) 50%, transparent 100%);
      mix-blend-mode: screen;
      opacity: 0;
      z-index: 50;
    }

    .water-surface {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(ellipse at 20% 30%, rgba(0, 100, 200, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 70%, rgba(0, 150, 255, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(0, 80, 160, 0.05) 0%, transparent 70%);
      opacity: 0.3;
      z-index: 10;
    }

    .ripple {
      position: absolute;
      border: 1px solid rgba(0, 150, 255, 0.6);
      border-radius: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%);
      box-shadow: 
        0 0 10px rgba(0, 150, 255, 0.4),
        inset 0 0 10px rgba(0, 150, 255, 0.2);
      z-index: 50;
    }

    /* Main Content */
    .main-content {
      position: relative;
      z-index: 100;
      width: 100%;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: auto;
      cursor: auto;
      padding: 2rem 0;
    }

    .card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 3rem;
      border-radius: 1.5rem;
      max-width: 720px;
      width: 100%;
      margin: 2rem auto;
      box-shadow: 
        0 20px 50px rgba(0, 0, 0, 0.3),
        0 0 100px rgba(0, 150, 255, 0.1);
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(0, 150, 255, 0.1) 0%, transparent 50%, rgba(0, 100, 200, 0.05) 100%);
      pointer-events: none;
      z-index: -1;
    }

    .heading {
      font-size: 2.75rem;
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: -0.03em;
      color: var(--text-dark);
      text-shadow: 0 0 30px rgba(0, 150, 255, 0.5);
    }

    .subtitle {
      font-size: 1.125rem;
      color: var(--text-muted);
      text-shadow: 0 0 20px rgba(0, 150, 255, 0.3);
    }

    .step {
      border-left: 4px solid var(--accent);
      padding: 1rem;
      font-size: 1rem;
      line-height: 1.7;
      color: var(--text-muted);
      box-shadow: -4px 0 20px rgba(0, 150, 255, 0.3);
    }

    .contacts {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .contacts a {
      color: var(--text-dark);
      text-decoration: none;
      font-weight: 600;
      position: relative;
      padding: 0.5rem 0;
      transition: all 0.3s ease;
      text-shadow: 0 0 20px rgba(0, 150, 255, 0.3);
    }

    .contacts a::after {
      content: '';
      position: absolute;
      width: 0%;
      height: 2px;
      background: linear-gradient(90deg, var(--accent), rgba(0, 200, 255, 0.8));
      bottom: 0;
      left: 0;
      transition: width 0.4s ease;
      box-shadow: 0 0 10px rgba(0, 150, 255, 0.5);
    }

    .contacts a:hover {
      color: rgba(0, 200, 255, 1);
      text-shadow: 0 0 30px rgba(0, 150, 255, 0.8);
      transform: translateX(10px);
    }

    .contacts a:hover::after {
      width: 100%;
    }

    .logout {
      text-align: center;
      margin-top: 2rem;
    }

    .logout button {
      background: none;
      border: none;
      font-size: 0.875rem;
      color: var(--text-muted);
      text-decoration: underline;
      cursor: pointer;
      transition: all 0.3s ease;
      text-shadow: 0 0 15px rgba(0, 150, 255, 0.2);
    }

    .logout button:hover {
      color: rgba(0, 200, 255, 0.8);
      text-shadow: 0 0 25px rgba(0, 150, 255, 0.6);
    }

    @media (min-width: 768px) {
      .heading {
        font-size: 3.5rem;
      }
    }

    @media (max-width: 768px) {
      .card {
        padding: 2rem;
      }
      .heading {
        font-size: 2rem;
      }
      .light-cursor {
        width: 150px;
        height: 150px;
      }
      .triangle-3d.large {
        border-left-width: 40px;
        border-right-width: 40px;
        border-bottom-width: 70px;
      }
      .triangle-3d.medium {
        border-left-width: 30px;
        border-right-width: 30px;
        border-bottom-width: 50px;
      }
    }

    .fade-in {
      opacity: 0;
      transform: translateY(40px);
    }

    @media (max-width: 480px) {
      body {
        padding: 1rem;
      }
      
      .main-content {
        padding: 1.5rem 0;
      }
      
      .card {
        padding: 2rem 1.5rem;
        margin: 1rem auto;
        border-radius: 1rem;
        gap: 1.25rem;
      }
      
      .heading {
        font-size: 1.75rem;
      }
      
      .subtitle {
        font-size: 1rem;
      }
      
      .step {
        font-size: 0.9rem;
      }
    }

    @media (min-width: 481px) and (max-width: 768px) {
      .card {
        padding: 2.5rem;
        margin: 1.5rem auto;
      }
      
      .heading {
        font-size: 2.25rem;
      }
    }

    @media (min-width: 1024px) {
      .card {
        padding: 4rem;
        margin: 3rem auto;
      }
    }

    @media (max-height: 600px) {
      .main-content {
        padding: 1rem 0;
      }
      
      .card {
        margin: 1rem auto;
        padding: 2rem;
      }
      
      .heading {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <!-- Water Animation Background -->
  <div class="water-container">
    <canvas id="waterCanvas"></canvas>
    <div class="water-surface"></div>
    
    <!-- 3D Triangles Background -->
    <div class="triangles-container" id="trianglesContainer">
      <!-- Triangles will be generated by JavaScript -->
    </div>
    
    <div class="light-cursor" id="lightCursor"></div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="card">
      <h1 class="heading fade-in">Pendaftaran Sukses</h1>
      <p class="subtitle fade-in">Hai <span style="color: white;"> {{ Auth::user()->name }}</span>, satu langkah lagi menuju Arenamu!</p>
      <p class="step fade-in">
        Silakan selesaikan pembayaran dan konfirmasi kepada kami melalui salah satu platform di bawah ini. Kami akan segera mengaktifkan akunmu setelahnya.
      </p>
      <div class="contacts fade-in">
        <a href="https://www.instagram.com/gazeofdew?igsh=cjBoYTdhYWRhaWNs" target="_blank">Instagram</a>
        <a href="https://www.tiktok.com/@gazeofdew?_t=ZS-8xQ4JQmKlIO&_r=1" target="_blank">TikTok</a>
      </div>
      <div class="logout fade-in">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit">Atau keluar dari sesi ini</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Canvas setup
    const canvas = document.getElementById('waterCanvas');
    const ctx = canvas.getContext('2d');
    const lightCursor = document.getElementById('lightCursor');
    const waterContainer = document.querySelector('.water-container');
    const trianglesContainer = document.getElementById('trianglesContainer');

    // Resize canvas
    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Mouse position
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let targetMouseX = mouseX;
    let targetMouseY = mouseY;

    // Water simulation variables
    const waves = [];
    const particles = [];
    const maxWaves = 8;
    const maxParticles = 30;

    // 3D Triangles generation
    function createTriangles() {
      const triangleTypes = ['large', 'medium', 'small', 'outline'];
      const triangleCount = 15;

      for (let i = 0; i < triangleCount; i++) {
        const triangle = document.createElement('div');
        const type = triangleTypes[Math.floor(Math.random() * triangleTypes.length)];
        
        triangle.className = `triangle-3d ${type}`;
        triangle.style.left = Math.random() * 100 + '%';
        triangle.style.top = Math.random() * 100 + '%';
        triangle.style.animationDelay = Math.random() * 10 + 's';
        triangle.style.transform = `
          rotateX(${Math.random() * 360}deg) 
          rotateY(${Math.random() * 360}deg) 
          rotateZ(${Math.random() * 360}deg)
        `;
        
        trianglesContainer.appendChild(triangle);
      }

      // Create wireframe triangles
      for (let i = 0; i < 8; i++) {
        const wireframe = document.createElement('div');
        wireframe.className = 'triangle-wireframe';
        wireframe.style.left = Math.random() * 100 + '%';
        wireframe.style.top = Math.random() * 100 + '%';
        wireframe.style.animationDelay = Math.random() * 15 + 's';
        
        trianglesContainer.appendChild(wireframe);
      }
    }

    // Triangle interaction with mouse
    function updateTrianglesWithMouse() {
      const triangles = document.querySelectorAll('.triangle-3d, .triangle-wireframe');
      
      triangles.forEach((triangle, index) => {
        const rect = triangle.getBoundingClientRect();
        const triangleX = rect.left + rect.width / 2;
        const triangleY = rect.top + rect.height / 2;
        
        const distance = Math.sqrt(
          Math.pow(mouseX - triangleX, 2) + Math.pow(mouseY - triangleY, 2)
        );
        
        if (distance < 200) {
          const intensity = (200 - distance) / 200;
          const newOpacity = 0.05 + (intensity * 0.15);
          
          gsap.to(triangle, {
            opacity: newOpacity,
            scale: 1 + (intensity * 0.2),
            duration: 0.3,
            ease: "power2.out"
          });
        } else {
          gsap.to(triangle, {
            opacity: 0.08,
            scale: 1,
            duration: 0.5,
            ease: "power2.out"
          });
        }
      });
    }

    // Wave class
    class Wave {
      constructor(x, y, radius = 0, intensity = 1) {
        this.x = x;
        this.y = y;
        this.radius = radius;
        this.maxRadius = 150 + Math.random() * 100;
        this.intensity = intensity;
        this.alpha = 1;
        this.speed = 2 + Math.random() * 2;
      }

      update() {
        this.radius += this.speed;
        this.alpha = Math.max(0, 1 - (this.radius / this.maxRadius));
        this.intensity *= 0.98;
      }

      draw() {
        if (this.alpha <= 0) return;

        ctx.save();
        ctx.globalAlpha = this.alpha * 0.6;
        
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(0, ${100 + this.intensity * 100}, ${200 + this.intensity * 55}, ${this.alpha})`;
        ctx.lineWidth = 2;
        ctx.shadowBlur = 10;
        ctx.shadowColor = `rgba(0, 150, 255, ${this.alpha * 0.5})`;
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius * 0.7, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(100, 200, 255, ${this.alpha * 0.3})`;
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.restore();
      }

      isDead() {
        return this.alpha <= 0 || this.radius > this.maxRadius;
      }
    }

    // Particle class
    class WaterParticle {
      constructor(x, y) {
        this.x = x;
        this.y = y;
        this.vx = (Math.random() - 0.5) * 2;
        this.vy = (Math.random() - 0.5) * 2;
        this.life = 1;
        this.decay = 0.01 + Math.random() * 0.02;
        this.size = 1 + Math.random() * 3;
      }

      update() {
        this.x += this.vx;
        this.y += this.vy;
        this.vx *= 0.98;
        this.vy *= 0.98;
        this.life -= this.decay;
        
        const dx = mouseX - this.x;
        const dy = mouseY - this.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        
        if (distance < 200) {
          const force = (200 - distance) / 200 * 0.1;
          this.vx += (dx / distance) * force;
          this.vy += (dy / distance) * force;
        }
      }

      draw() {
        if (this.life <= 0) return;

        ctx.save();
        ctx.globalAlpha = this.life;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(0, ${150 + this.life * 100}, 255, ${this.life})`;
        ctx.shadowBlur = 8;
        ctx.shadowColor = `rgba(0, 150, 255, ${this.life * 0.8})`;
        ctx.fill();
        ctx.restore();
      }

      isDead() {
        return this.life <= 0;
      }
    }

    // Create initial particles
    function createParticles() {
      for (let i = 0; i < 20; i++) {
        particles.push(new WaterParticle(
          Math.random() * canvas.width,
          Math.random() * canvas.height
        ));
      }
    }

    // Mouse move handler
    function handleMouseMove(e) {
      targetMouseX = e.clientX;
      targetMouseY = e.clientY;
    }

    // Mouse click handler
    function handleMouseClick(e) {
      createWave(e.clientX, e.clientY, 2);
      createLightSpot(e.clientX, e.clientY);
      createRipple(e.clientX, e.clientY);
      
      for (let i = 0; i < 8; i++) {
        particles.push(new WaterParticle(
          e.clientX + (Math.random() - 0.5) * 50,
          e.clientY + (Math.random() - 0.5) * 50
        ));
      }
    }

    // Create wave
    function createWave(x, y, intensity = 1) {
      if (waves.length < maxWaves) {
        waves.push(new Wave(x, y, 0, intensity));
      }
    }

    // Create light spot
    function createLightSpot(x, y) {
      const lightSpot = document.createElement('div');
      lightSpot.className = 'light-spot';
      lightSpot.style.left = x + 'px';
      lightSpot.style.top = y + 'px';
      lightSpot.style.width = '100px';
      lightSpot.style.height = '100px';
      waterContainer.appendChild(lightSpot);

      gsap.fromTo(lightSpot, 
        { opacity: 0, scale: 0 },
        { 
          opacity: 1, 
          scale: 2, 
          duration: 0.5, 
          ease: "power2.out",
          onComplete: () => {
            gsap.to(lightSpot, {
              opacity: 0,
              scale: 3,
              duration: 2,
              ease: "power2.out",
              onComplete: () => lightSpot.remove()
            });
          }
        }
      );
    }

    // Create ripple
    function createRipple(x, y) {
      const ripple = document.createElement('div');
      ripple.className = 'ripple';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      waterContainer.appendChild(ripple);

      gsap.fromTo(ripple, 
        { width: 0, height: 0, opacity: 1 },
        { 
          width: 300, 
          height: 300, 
          opacity: 0, 
          duration: 2, 
          ease: "power2.out",
          onComplete: () => ripple.remove()
        }
      );
    }

    // Animation loop
    function animate() {
      mouseX += (targetMouseX - mouseX) * 0.1;
      mouseY += (targetMouseY - mouseY) * 0.1;

      gsap.set(lightCursor, { x: mouseX, y: mouseY });

      // Update triangles based on mouse position
      updateTrianglesWithMouse();

      ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      for (let i = waves.length - 1; i >= 0; i--) {
        waves[i].update();
        waves[i].draw();
        if (waves[i].isDead()) {
          waves.splice(i, 1);
        }
      }

      for (let i = particles.length - 1; i >= 0; i--) {
        particles[i].update();
        particles[i].draw();
        if (particles[i].isDead()) {
          particles.splice(i, 1);
        }
      }

      if (particles.length < maxParticles && Math.random() < 0.08) {
        particles.push(new WaterParticle(
          Math.random() * canvas.width,
          Math.random() * canvas.height
        ));
      }

      if (Math.random() < 0.015) {
        createWave(
          Math.random() * canvas.width,
          Math.random() * canvas.height,
          0.3 + Math.random() * 0.5
        );
      }

      requestAnimationFrame(animate);
    }

    // Event listeners
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('click', handleMouseClick);

    // Touch support
    document.addEventListener('touchmove', (e) => {
      e.preventDefault();
      const touch = e.touches[0];
      handleMouseMove(touch);
    });

    document.addEventListener('touchstart', (e) => {
      const touch = e.touches[0];
      handleMouseClick(touch);
    });

    // Initialize
    createTriangles();
    createParticles();
    animate();

    // Content animations
    gsap.to('.fade-in', {
      opacity: 1,
      y: 0,
      duration: 1.2,
      ease: 'power3.out',
      stagger: 0.2,
      delay: 0.5
    });

    // Ambient lighting animation
    gsap.to(lightCursor, {
      scale: 1.2,
      duration: 3,
      ease: "power2.inOut",
      yoyo: true,
      repeat: -1
    });
  </script>
</body>
</html>