{{-- File: resources/views/errors/500.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Kesalahan Server</title>

    {{-- Kita akan menggunakan CDN Tailwind CSS untuk kemudahan di halaman error --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- CSS Kustom untuk Halaman Error --}}
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;500;800&display=swap");

        :root {
            --primary-color: #ef4444; /* Warna Merah untuk error server */
            --eye-pupil-color: #050505;
            --bg-color: #fff;
            --text-color: #000;
            --fs-heading: 36px;
            --fs-text: 26px;
            --fs-button: 18px;
            --fs-icon: 30px;
            --pupil-size: 30px;
            --eye-size: 80px;
            --button-padding: 15px 30px;
        }

        @media only screen and (max-width: 567px) {
            :root {
                --fs-heading: 30px;
                --fs-text: 22px;
                --fs-button: 16px;
                --fs-icon: 24px;
                --button-padding: 12px 24px;
            }
        }
        
        body {
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: "Fira Sans", sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            row-gap: 30px;
            height: 100vh;
            width: 100dvw;
            text-align: center;
            position: relative;
            margin: auto;
            padding: 2rem;
        }

        .error-page__heading-title {
            text-transform: capitalize;
            font-size: var(--fs-heading);
            font-weight: 800;
            color: var(--primary-color);
        }

        .error-page__heading-desciption {
            margin-top: 10px;
            font-size: var(--fs-text);
            font-weight: 200;
        }

        .error-page__button {
            color: inherit;
            text-decoration: none;
            border: 1px solid var(--primary-color);
            font-size: var(--fs-button);
            font-weight: 500;
            padding: var(--button-padding);
            border-radius: 15px;
            box-shadow: 0px 7px 0px -2px var(--primary-color);
            transition: all 0.3s ease-in-out;
            text-transform: capitalize;
        }

        .error-page__button:hover {
            box-shadow: none;
            transform: translateY(3px);
            background-color: var(--primary-color);
            color: #fff;
        }

        .eyes {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .eye {
            width: var(--eye-size);
            height: var(--eye-size);
            background-color: var(--primary-color);
            border-radius: 50%;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }

        .eye__pupil {
            width: var(--pupil-size);
            height: var(--pupil-size);
            background-color: var(--eye-pupil-color);
            border-radius: 50%;
            transform-origin: center center;
            /* Animasi baru: mata "sedih" atau "lelah" */
            animation: movePupilSad 5s infinite ease-in-out;
        }
        
        .eye__pupil--right {
            animation-direction: reverse;
        }

        /* Animasi baru untuk mata yang sedih/error */
        @keyframes movePupilSad {
            0%, 100% { transform: translateY(10px); }
            50% { transform: translateY(-10px); }
        }

        /* Animasi untuk "air mata" */
        .tear {
            position: absolute;
            width: 8px;
            height: 12px;
            background: #60a5fa; /* Warna biru muda */
            border-radius: 50%;
            top: 60%;
            left: 50%;
            transform: translateX(-50%);
            animation: fall 3s infinite linear;
            opacity: 0;
        }

        @keyframes fall {
            0% { transform: translate(-50%, -20px); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translate(-50%, 80px); opacity: 0; }
        }

        .color-switcher {
            position: absolute;
            top: 40px;
            right: 40px;
            background-color: transparent;
            font-size: var(--fs-icon);
            cursor: pointer;
            color: var(--primary-color);
            border: 0;
            z-index: 100;
        }
    </style>
</head>
<body>
    <main class="error-page">
        <div class="container">
            <div class="eyes">
                <div class="eye">
                    <div class="eye__pupil eye__pupil--left"></div>
                    <div class="tear" style="animation-delay: 0s;"></div>
                    <div class="tear" style="animation-delay: 1.5s;"></div>
                </div>
                <div class="eye">
                    <div class="eye__pupil eye__pupil--right"></div>
                    <div class="tear" style="animation-delay: 0.5s;"></div>
                    <div class="tear" style="animation-delay: 2s;"></div>
                </div>
            </div>

            <div class="error-page__heading">
                <h1 class="error-page__heading-title">Oops! Ada Masalah di Server</h1>
                <p class="error-page__heading-desciption">500 Internal Server Error</p>
            </div>

            <a class="error-page__button" href="{{ app('router')->has('dashboard') ? route('dashboard') : url('/') }}" aria-label="kembali ke beranda" title="kembali ke beranda">
                Kembali ke Beranda
            </a>
        </div>
        <button class="color-switcher" data-theme-color-switch>&#127769;</button>
    </main>


    <script>
        const colorSwitcher = document.querySelector("[data-theme-color-switch]");
        let currentTheme = "light";

        colorSwitcher.addEventListener("click", function () {
            const root = document.documentElement;

            if (currentTheme == "dark") {
                root.style.setProperty("--bg-color", "#fff");
                root.style.setProperty("--text-color", "#000");
                root.style.setProperty("--eye-pupil-color", "#050505");
                colorSwitcher.textContent = "\u{1F319}";
                currentTheme = "light";
            } else {
                root.style.setProperty("--bg-color", "#111827");
                root.style.setProperty("--text-color", "#fff");
                root.style.setProperty("--eye-pupil-color", "#fff");
                colorSwitcher.textContent = "\u{2600}";
                currentTheme = "dark";
            }
            colorSwitcher.setAttribute("data-theme", currentTheme);
        });
    </script>
</body>
</html>
