<?php
if(!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}
?>
<header class="main-header">
    <div class="logo">
        <h1>Maluti Primary School</h1>
    </div>
    <nav>
        <div class="profile-menu">
            <button class="avatar-btn" onclick="toggleMenu()" aria-label="Toggle profile menu">
                <i class="fas fa-user-circle fa-2x" style="color: white;"></i>
            </button>
            <div class="sub-menu-wrap" id="subMenu" style="display:none; position:absolute; right:0; background:#fff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                <div class="sub-menu" style="display:flex; flex-direction: column; padding: 10px 0;">
                    <a href="profile.php" class="sub-menu-link" style="display:flex; align-items:center; padding: 10px 20px; color:#333; text-decoration:none;">
                        <i class="fas fa-user fa-lg" style="margin-right: 10px;"></i>
                        <p style="margin:0;">View Profile</p>
                    </a>
                    <a href="logout.php" class="sub-menu-link" style="display:flex; align-items:center; padding: 10px 20px; color:#333; text-decoration:none;">
                        <i class="fas fa-sign-out-alt fa-lg" style="margin-right: 10px;"></i>
                        <p style="margin:0;">Logout</p>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
<script>
    function toggleMenu() {
        const menu = document.getElementById('subMenu');
        if(menu.style.display === 'block') {
            menu.style.display = 'none';
        } else {
            menu.style.display = 'block';
        }
    }
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('subMenu');
        const button = document.querySelector('.avatar-btn');
        if (!menu.contains(event.target) && !button.contains(event.target)) {
            menu.style.display = 'none';
        }
    });
</script>
