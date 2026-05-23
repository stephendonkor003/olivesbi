<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERMS PLUS – Change Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #fff;
        }

        .container {
            display: flex;
            flex: 1;
            width: 100%;
        }

        /* Left Form Section */
        .form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .form-box {
            max-width: 400px;
            width: 100%;
        }

        .form-box h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #0d1b2a;
            text-align: center;
        }

        .form-box p {
            color: #555;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
            text-align: center;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            /*margin-bottom: 15px;*/
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #017C76;
            box-shadow: 0 0 4px rgba(1, 124, 118, 0.3);
        }

        .forgot {
            display: block;
            text-align: right;
            font-size: 13px;
            color: #017C76;
            text-decoration: none;
            margin-top: -10px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #0d1b2a;
            color: #fff;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .divider {
            margin: 20px 0;
            border-top: 1px solid #ddd;
        }

        .signup {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
        }

        .signup a {
            color: #017C76;
            text-decoration: none;
            font-weight: 600;
        }

        /* Logos Row */
        .logo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            gap: 15px;
        }

        .logo-row img {
            height: 32px;
            max-width: 80px;
            object-fit: contain;
            filter: grayscale(100%);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .logo-row img:hover {
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        /* Popup */
        .popup {
            position: absolute;
            background: #0d1b2a;
            color: #fff;
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.5;
            max-width: 280px;
            display: none;
            z-index: 1000;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            white-space: pre-line;
        }

        /* Right Image Section */
        .image-section {
            flex: 1;
            position: relative;
            background: url("{{ asset('assets/images/afrk.jpg') }}") no-repeat center center/cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .inspire-text {
            font-size: 26px;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.55);
            padding: 15px 25px;
            border-radius: 12px;
            max-width: 80%;
            line-height: 1.4;
        }

        /* Mobile Image */
        .mobile-image {
            display: none;
            margin-bottom: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .mobile-image img {
            width: 100%;
            display: block;
        }

        /* Footer */
        footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 15px 0;
            border-top: 1px solid #eee;
        }

        input.is-invalid {
            border-color: #dc3545;
            /*padding-right: calc(1.5em + .75rem);*/
            /*background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e);*/
            /*background-repeat: no-repeat;*/
            /*background-position: right calc(.375em + .1875rem) center;*/
            /*background-size: calc(.75em + .375rem) calc(.75em + .375rem);*/
        }

        .text-danger {
            color: rgb(248, 66, 56) !important;
            opacity: 1;
        }

        .form-control {
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            body {
                background: #f8f8f8;
            }

            .container {
                flex-direction: column;
                max-width: 420px;
                margin: 20px auto;
            }

            .image-section {
                display: none;
            }

            .mobile-image {
                display: block;
            }

            .form-section {
                padding: 20px;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            }

            .logo-row {
                justify-content: center;
                gap: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Right Image -->
        <div class="image-section">
            <div class="inspire-text" id="inspire"></div>
        </div>

        <!-- Left Form -->
        <div class="form-section">
            <div class="form-box">
                <!-- Mobile Image -->
                <div class="mobile-image">
                    <img src="{{ asset('asset/images/mob.png') }}" alt="Mobile Design">
                </div>

                @if (session()->has('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong style="color:green">{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong style="color:red">{{ session('error') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Login Form -->
                <div id="login-form">
                    <h2>Password Reset 🔑</h2>
                    <p>Enter your OTP to change your password.</p>

                    <form action="{{ route('auth.changePasswordFrontWa') }}" method="POST">
                        @csrf

                        <div class="form-control">
                            <label>Enter OTP</label>
                            <input type="text" name="code"
                                class="@error('code') is-invalid @enderror" placeholder="Enter OTP"
                                required>

                            @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label>New Password</label>
                            <input type="password" class="@error('password') is-invalid @enderror"
                                name="password" placeholder="Enter new password" required>

                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                required>
                        </div>

                        <div class="form-control">
                        <a class="forgot" href="{{ route('auth.sendOTP') }}">Resend OTP?</a>
                        </div>

                        <button type="submit" class="btn">Reset Password</button>
                    </form>
                </div>

                <div class="divider"></div>

                <p class="signup">Developmental Partners</p>

                <!-- Logos Row -->
                <div class="logo-row">
                    <img src="{{ asset('assets/images/au.png') }}" alt="African Union"
                        data-popup="🌍 The African Union’s Agenda 2063 envisions a prosperous and well-governed Africa driven by its people.
            ERMS PLUS directly supports this vision by strengthening internal revenue systems, advancing Goals 1, 2, 11, 12 and 18, and providing the sustainable resources needed to build the Africa we want.">

                    <img src="{{ asset('assets/images/un.svg') }}" alt="United Nations"
                        data-popup="  The United Nations Sustainable Development Goals call for an end to poverty, decent work and growth, strong institutions, and innovation.
            Through resource mobilization, job creation, digital infrastructure, and transparent governance, ERMS PLUS contributes to achieving SDGs 1, 8, 9, 16, and 17.">

                    <img src="{{ asset('assets/images/mel.png') }}" alt="Melchia"
                        data-popup="🤝 Melchia serves as the operational partner of ERMS PLUS, ensuring that deployments across Africa are effectively managed and implemented.
            They guarantee the system’s smooth day-to-day operations, bringing the vision of sustainable revenue management to life.">

                    <img src="{{ asset('assets/images/acng.svg') }}" alt="Assets Circle"
                        data-popup="💡 Assets Circle Network is the technology and innovation partner powering ERMS PLUS.
            By driving Africa’s digital economy with cutting-edge solutions, they ensure the system remains future-ready, creating opportunities for stronger economies and new jobs across the continent.">
                </div>

            </div>
        </div>
    </div>

    <footer>
        <a href="https://ermsplus.africa" target="_blank" style="text-decoration: none; color: inherit;">
            © {{ date('Y') }} <strong>ERMS PLUS</strong> — Built by Africans, for Africans.
        </a>
    </footer>

    <!-- JS Typing Effect + Reset Flow -->
    <script>
        const messages = [
            "Streamlining Internal Generated Funds",
            "Building the Africa We Want",
            "Towards Agenda 2063 of the African Union"
        ];

        let index = 0,
            charIndex = 0;
        const typingSpeed = 80,
            eraseSpeed = 50,
            delayBetween = 1500;
        const inspireEl = document.getElementById("inspire");

        function typeEffect() {
            if (charIndex < messages[index].length) {
                inspireEl.textContent += messages[index].charAt(charIndex);
                charIndex++;
                setTimeout(typeEffect, typingSpeed);
            } else {
                setTimeout(eraseEffect, delayBetween);
            }
        }

        function eraseEffect() {
            if (charIndex > 0) {
                inspireEl.textContent = messages[index].substring(0, charIndex - 1);
                charIndex--;
                setTimeout(eraseEffect, eraseSpeed);
            } else {
                index = (index + 1) % messages.length;
                setTimeout(typeEffect, typingSpeed);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (inspireEl) typeEffect();
        });

        // Logo Popups
        const logos = document.querySelectorAll(".logo-row img");
        const popup = document.createElement("div");
        popup.classList.add("popup");
        document.body.appendChild(popup);

        logos.forEach(logo => {
            logo.addEventListener("mouseenter", () => {
                popup.textContent = logo.getAttribute("data-popup");
                popup.style.display = "block";
            });
            logo.addEventListener("mousemove", e => {
                popup.style.top = e.pageY - popup.offsetHeight - 15 + "px";
                popup.style.left = e.pageX - popup.offsetWidth / 2 + "px";
            });
            logo.addEventListener("mouseleave", () => {
                popup.style.display = "none";
            });
        });
    </script>
</body>

</html>
