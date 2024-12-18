<?php
session_start();

$host = "localhost";
$username = "root";
$password = ""; 
$database = "GenerasiCipta";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Koneksi ke database gagal. Silakan coba lagi nanti.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact'])) {
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid!');</script>";
        return;
    }    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    if ($stmt->execute()) {
        echo "<script>alert('Pesan berhasil dikirim!');</script>";
    } else {
        echo "<script>alert('Gagal mengirim pesan.');</script>";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if ($_POST['auth_type'] == 'Sign Up') {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $stmt->close();
} elseif ($_POST['auth_type'] == 'Sign In') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $email;
        }
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenerasiCipta</title>
    <link rel="website icon" href="logo (2).png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <a href="#home" class="logo"><strong>Generasi</strong><span class="word-two">Cipta<span></a>
        <div class="bx bx-menu" id="menu"><box-icon name='menu'></box-icon>
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="#about-us">about us</a></li>
                <li><a href="#author">author</a></li>
                <li><a href="#contact">contact</a></li>
                <li><button id="darkMode-icon" onclick="toggleDarkMode()"><div class="bx bxs-buildings" id="darkMode-icon"><box-icon name='buildings' type='solid'></box-icon></div></button></li>
            </ul>
        </nav>
        <button class="sign-up" id="sign-up" role="button">Sign-up</button>  
    
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup" id="authPopup">
                <h2 id="popupTitle">Sign In</h2>
                <div class="input-box">
                    <input type="email" placeholder="Email">
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password">
                </div>
                <button class="btn" id="actionBtn">Sign In</button>
                <p class="switch-btn" id="switchMode">Switch to Sign Up</p>
                <p class="close-btn" id="closePopup">Close</p>
            </div> 
        </div>
        <script>
            const signInBtn = document.getElementById('sign-up');
            const popupOverlay = document.getElementById('popupOverlay');
            const closePopup = document.getElementById('closePopup');
            const popupTitle = document.getElementById('popupTitle');
            const actionBtn = document.getElementById('actionBtn');
            const switchMode = document.getElementById('switchMode');
    
            let isSignIn = true;
    
            signInBtn.addEventListener('click', () => {
                popupOverlay.style.display = 'flex';
            });
    
            closePopup.addEventListener('click', () => {
                popupOverlay.style.display = 'none';
            });
    
            switchMode.addEventListener('click', () => {
                isSignIn = !isSignIn;
                popupTitle.textContent = isSignIn ? 'Sign In' : 'Sign Up';
                actionBtn.textContent = isSignIn ? 'Sign In' : 'Sign Up';
                switchMode.textContent = isSignIn ? 'Switch to Sign Up' : 'Switch to Sign In';
            });
        </script>    
        <script>
            function toggleDarkMode() {

                document.body.classList.toggle('dark-mode');

                const icon = document.getElementById('darkMode-icon');
                if (document.body.classList.contains('dark-mode')) {

                    icon.innerHTML = '<div class="bx bx-buildings" id="darkMode-icon"><box-icon name="buildings"></box-icon></div>';
            
                    const icons = document.querySelectorAll('.navbar-job .icon img');
                    icons.forEach(icon => {
                        icon.style.filter = 'invert(1)';
                    });
                } else {

                    icon.innerHTML = '<div class="bx bxs-buildings" id="darkMode-icon"><box-icon name="buildings" type="solid"></box-icon></div>';
                    const icons = document.querySelectorAll('.navbar-job .icon img');
                    icons.forEach(icon => {
                        icon.style.filter = 'invert(0)';
                    });
                }
            }
            
        </script>
    </header>
    <script>
        menu = document.querySelector("#menu");
        menu.onclick = function() {
            navBar = document.querySelector(".navbar");
            navBar.classList.toggle("active");
        }
    </script>
    <section class="home" id="home">
        <div class="home-content">
            <div class="box-one">
                <img src="abstract-background-textured-wal.png" alt="">
                <div class="overlay">
                    <h1>Temukan Peluangmu</h1>
                    <p>Jembatani bakatmu dengan dunia. Ayo mulai berkarya bersama GenerasiCipta!</p>
                    <div class="search-bar">
                        <input type="text" placeholder="Cari layanan...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="navbar-job">
                <div class="icon">
                    <img src="ai-services-thin.104f389.png" alt="AI Services">
                    <p>AI Services</p>
                </div>
                <div class="icon">
                    <img src="business-thin.885e68e.png" alt="Business">
                    <p>Business</p>
                </div>
                <div class="icon">
                    <img src="consulting-thin.d5547ff.png" alt="Consulting">
                    <p>Consulting</p>
                </div>
                <div class="icon">
                    <img src="digital-marketing-thin.68edb44.png" alt="Digital Marketing">
                    <p>Digital Marketing</p>
                </div>
                <div class="icon">
                    <img src="graphics-design-thin.ff38893.png" alt="Graphics Design">
                    <p>Graphics Design</p>
                </div>
                <div class="icon">
                    <img src="music-audio-thin.43a9801.png" alt="Music & Audio">
                    <p>Music & Audio</p>
                </div>
                <div class="icon">
                    <img src="programming-tech-thin.56382a2.png" alt="Programming & Tech">
                    <p>Programming & Tech</p>
                </div>
                <div class="icon">
                    <img src="video-animation-thin.9d3f24d.png" alt="Video & Animation">
                    <p>Video & Animation</p>
                </div>
                <div class="icon">
                    <img src="writing-translation-thin.fd3699b.png" alt="Writing & Translation">
                    <p>Writing & Translation</p>
                </div>
            </div>
            
        </div>
    </section>
    <section class="about-us" id="about-us">
        <div class="about-us-content">
            <div class="about-us-text">
                <h2 class="section-title">Tentang Kami</h2>
                <p>Selamat datang di platform kami, tempat inovasi bertemu dengan peluang. Kami hadir untuk menjembatani mahasiswa berbakat dari Fakultas Ilmu Komputer Universitas Brawijaya dengan masyarakat yang membutuhkan layanan IT berkualitas, terpercaya, dan terjangkau.</p>
                <p>Misi kami adalah memberdayakan mahasiswa dengan menyediakan platform profesional untuk memamerkan keahlian mereka, mendapatkan pengalaman nyata, dan berkontribusi dalam menyelesaikan tantangan teknologi yang dihadapi oleh individu maupun bisnis. Di sisi lain, kami juga mempermudah masyarakat dalam menemukan solusi IT yang tepat, mulai dari pengembangan website, desain UI/UX, hingga konsultasi teknologi.</p>
                <p>Dengan fitur-fitur seperti pencarian jasa, alat manajemen proyek, dan sistem pembayaran terintegrasi, platform kami menjamin pengalaman yang mudah dan efisien bagi penyedia jasa maupun pengguna. Bersama, kita menciptakan ekosistem kolaboratif di mana semua pihak dapat berkembang.</p>
                <p>Mari terhubung, berkarya, dan berinovasi – satu proyek dalam satu waktu.</p>
            </div>
            <div class="about-us-image">
                <img src="mobile_banner_b19573f215.png" alt="Tentang Kami" />
            </div>
            
            <div class="social-media">
                <a href="#" id="facebook"> <i class='bx bxl-facebook'></i> </a>
                <a href="#" id="twitter"> <i class='bx bxl-twitter'></i> </a>
                <a href="#" id="instagram"> <i class='bx bxl-instagram-alt'></i> </a>
                <a href="#" id="linkedin"> <i class='bx bxl-linkedin'></i> </a>
            </div>
        </div>
    </section>
    <section class="author" id="author">
        <div class="author-content">
            <p class="author-title">Author</p>
            <div class="navbar-author">
                <div class="icon">
                    <p class="name">Malvinshah Haris Athala</p>
                    <img src="/Project/Jasa Mahasiswa/FinalBasisData/New folder/1705935663267.png">
                    <div class="social-media">
                        <a href="#" id="facebook"> <i class='bx bxl-facebook' ></i> </a>
                        <a href="#" id="twitter"> <i class='bx bxl-twitter' ></i> </a>
                        <a href="#" id="instagram"> <i class='bx bxl-instagram-alt' ></i> </a>
                        <a href="#" id="linkedin"> <i class='bx bxl-linkedin' ></i> </a>
                    </div>
                </div>
                <div class="icon">
                    <p class="name">Joshua Washington Hutasoit</p>
                    <img src="/Project/Jasa Mahasiswa/FinalBasisData/New folder/WhatsApp Image 2024-12-06 at 21.51.06_20aa3683.jpg" >
                    <div class="social-media">
                        <a href="#" id="facebook"> <i class='bx bxl-facebook' ></i> </a>
                        <a href="#" id="twitter"> <i class='bx bxl-twitter' ></i> </a>
                        <a href="#" id="instagram"> <i class='bx bxl-instagram-alt' ></i> </a>
                        <a href="#" id="linkedin"> <i class='bx bxl-linkedin' ></i> </a>
                    </div>
                </div>
                <div class="icon">
                    <p class="name">Michael Andro Nathaniel</p>
                    <img src="/Project/Jasa Mahasiswa/FinalBasisData/New folder/1726654102019.png">
                    <div class="social-media">
                        <a href="#" id="facebook"> <i class='bx bxl-facebook' ></i> </a>
                        <a href="#" id="twitter"> <i class='bx bxl-twitter' ></i> </a>
                        <a href="#" id="instagram"> <i class='bx bxl-instagram-alt' ></i> </a>
                        <a href="#" id="linkedin"> <i class='bx bxl-linkedin' ></i> </a>
                    </div>
                </div>
                <div class="icon">
                    <p class="name">Nauval Rusdi Aslam</p>
                    <img src="/Project/Jasa Mahasiswa/FinalBasisData/New folder/Screenshot 2024-12-06 215202.png">
                    <div class="social-media">
                        <a href="#" id="facebook"> <i class='bx bxl-facebook' ></i> </a>
                        <a href="#" id="twitter"> <i class='bx bxl-twitter' ></i> </a>
                        <a href="#" id="instagram"> <i class='bx bxl-instagram-alt' ></i> </a>
                        <a href="#" id="linkedin"> <i class='bx bxl-linkedin' ></i> </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <section class="contact">
        <div class="contact-content">
            <h2>Contact Us</h2>
            <p>Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan atau masukan!</p>
            <form>
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nama Anda" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Anda" required>
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Pesan Anda" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn">Kirim Pesan</button>
            </form>
        </div>
    </section>
</body>
</html>