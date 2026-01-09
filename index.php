<?php
require_once('connection.php');
session_start(); 

$sql = "SELECT * FROM vehicles WHERE AVAILABLE='Y'";
$vehicles = mysqli_query($con, $sql);
$showLimit = 24;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VeloRent - Premium Vehicle Rental Service</title>

<style>
/* ---------- Base ---------- */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}
body{
    background:#f8f9fa;
    color:#2c3e50;
    line-height:1.6;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

main{
    flex:1;
}

/* ---------- Navbar ---------- */
.navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
        }

        .navbar img {
            height: 50px;
            transition: transform 0.3s;
        }

        .navbar img:hover {
            transform: scale(1.05);
        }

.menu ul{
    display:flex;
    list-style:none;
}
.menu li{ margin-left:40px; }
.menu a{
    color:#2c3e50;
    text-decoration:none;
    font-weight:500;
    font-size:1rem;
    transition:color 0.3s, border-bottom 0.3s;
    padding-bottom:5px;
    position:relative;
}

.menu a::after{
    content:'';
    position:absolute;
    width:0;
    height:2px;
    bottom:0;
    left:0;
    background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition:width 0.3s;
}

.menu a:hover{
    color:#667eea;
}

.menu a:hover::after{
    width:100%;
}

/* Hamburger Menu */
.hamburger{
    display:none;
    flex-direction:column;
    cursor:pointer;
    z-index:1001;
}

.hamburger span{
    width:30px;
    height:3px;
    background:#2c3e50;
    margin:4px 0;
    transition:0.3s;
    border-radius:2px;
}

.hamburger.active span:nth-child(1){
    transform:rotate(45deg) translate(5px, 5px);
}

.hamburger.active span:nth-child(2){
    opacity:0;
}

.hamburger.active span:nth-child(3){
    transform:rotate(-45deg) translate(7px, -6px);
}

@media (max-width: 768px){
    .hamburger{
        display:flex;
    }

    .menu{
        position:absolute;
        top:70px;
        right:20px;
        background:rgba(255, 255, 255, 0.98);
        backdrop-filter:blur(10px);
        padding:20px;
        border-radius:10px;
        box-shadow:0 4px 15px rgba(0, 0, 0, 0.1);
        display:none;
        z-index:1000;
    }

    .menu.active{
        display:block;
    }

    .menu ul{
        flex-direction:column;
        gap:15px;
    }

    .menu li{
        margin-left:0;
    }
}

/* ---------- Hero Section ---------- */
.hai{
    position:relative;
    min-height:80vh;
    background:url(images/car.png) no-repeat right center;
    background-size:65%;
}

/* Overlay */
.hai::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(
        to right,
        rgba(255,255,255,0.95) 45%,
        rgba(255,255,255,0.4) 70%,
        rgba(255,255,255,0) 100%
    );
}

.content{
    position:relative;
    z-index:1;
    min-height:80vh;
    display:flex;
    align-items:center;
    padding:160px 4% 80px;
}

.hero-content{
    max-width:480px;
    margin-left:-30px;
}

.hero-content h1{
    font-size:3.8rem;
    line-height:1.1;
    color:#2c3e50;
}
.hero-content h1 span{
    color:#ffd700;
}
.par{
    font-size:1.1rem;
    color:#555;
    margin:25px 0 35px;
}

.cta-button{
    background:#ffd700;
    padding:15px 35px;
    border-radius:30px;
    border:none;
    cursor:pointer;
}
.cta-button a{
    text-decoration:none;
    font-weight:600;
    color:#2c3e50;
}

/* ---------- Vehicle Section ---------- */
.vehicle-showcase{
    padding:100px 8%;
    text-align:center;
}
.vehicle-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:30px;
}
.vehicle-card{
    background:#fff;
    border-radius:20px;
    padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    transition:.3s;
}
.vehicle-card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 40px rgba(102,126,234,.25);
}
.vehicle-card img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:12px;
}
.vehicle-card h3{ margin-top:15px; }
.price{
    font-weight:700;
    color:#667eea;
}
.book-btn{
    display:block;
    margin-top:20px;
    padding:14px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff;
    text-decoration:none;
    border-radius:12px;
}

/* ---------- Footer ---------- */
footer{
    background:rgba(0,0,0,0.05);
    padding:30px 5%;
    text-align:center;
    border-top:1px solid #e0e6ed;
    margin-top:auto;
}

footer p{
    margin-bottom:15px;
    color:#524f4f;
    font-weight:500;
}

.socials{
    display:flex;
    justify-content:center;
    gap:20px;
}
.socials a{
    font-size:1.5rem;
    color:#333;
    transition:color 0.3s, transform 0.3s;
}
.socials a:hover{ 
    color:#667eea;
    transform:scale(1.2);
}

/* ---------- Responsive ---------- */
@media(max-width:768px){
    .hai{
        background-position:center bottom;
        background-size:90%;
    }
    .content{
        padding:120px 6% 60px;
        text-align:center;
    }
    .hero-content{
        margin-left:0;
        max-width:100%;
    }
}
</style>
</head>

<body>

<nav class="navbar">
    <a href="index.php">
        <img src="images/icon.png" alt="VeloRent Logo">
    </a>
    
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <div class="menu" id="menu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="aboutus.html">About Us</a></li>
            <li><a href="services.html">Services</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>
</nav>

<div class="hai">
    <div class="content">
        <div class="hero-content">
            <h1>Rent Your <br><span>Dream Vehicle</span></h1>
            <p class="par">
                Luxury meets affordability. Explore our collection of premium vehicles and start your journey today.
            </p>
            <button class="cta-button">
                <a href="register.php">Join Us Now</a>
            </button>
        </div>
    </div>
</div>

<section class="vehicle-showcase">
<h2 style="font-size:2.5rem;margin-bottom:50px;">Featured Fleet</h2>
<div class="vehicle-grid">
<?php
$count=0;
while($row=mysqli_fetch_assoc($vehicles)){
    if($count++ >= $showLimit) break;
?>
<div class="vehicle-card">
    <img src="images/<?php echo $row['VEHICLE_IMG']; ?>">
    <h3><?php echo $row['VEHICLE_NAME']; ?></h3>
    <p><?php echo $row['FUEL_TYPE']; ?> • <?php echo $row['CAPACITY']; ?> Seater</p>
    <p class="price">Rs. <?php echo $row['PRICE']; ?> / day</p>
    <a href="login.php?id=<?php echo $row['VEHICLE_ID']; ?>" class="book-btn">Book Now</a>
</div>
<?php } ?>
</div>
</section>

<footer>
    <p>&copy; 2025 VeloRent. All Rights Reserved.</p>
    <div class="socials">
        <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
        <a href="https://x.com/" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
        <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
    </div>
</footer>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('menu');

    hamburger.addEventListener('click', function() {
        hamburger.classList.toggle('active');
        menu.classList.toggle('active');
        if(menu.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    });

    const menuLinks = document.querySelectorAll('.menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            menu.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });

    document.addEventListener('click', function(event) {
        if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
            hamburger.classList.remove('active');
            menu.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
</script>
</body>
</html>
