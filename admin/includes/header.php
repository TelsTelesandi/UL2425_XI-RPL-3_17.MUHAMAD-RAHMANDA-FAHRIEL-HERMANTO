<header class="admin-header">
    <div class="header-left">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="header-right">
        <div class="dropdown">
            <button class="dropdown-toggle">
                <i class="fas fa-user"></i>
                <span><?php echo $_SESSION['username']; ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>
