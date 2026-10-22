<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$currentUser = $_SESSION['username'] ?? 'employee@gmail.com'; //Issue here - 
$currentInitial = strtoupper(substr($currentUser, 0, 1));
?>

<?php include __DIR__ . '/modals.php'; ?>

<link rel="stylesheet" href="/topnavbar.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="mia-navbar-wrap">
    <nav class="mia-navbar">
        <div class="mia-grid">

            <div class="mia-left">
                <a class="mia-brand" href="/subsystems.php/textchat">Make-It-All</a>
            </div>

            <div class="mia-center">
                <a href="/subsystems.php/textchat"
                   class="mia-nav-link <?php echo str_contains($currentUri, 'textchat') ? 'active' : ''; ?>">
                    Text Chat
                </a>
                <?php
                $analyticsRole = $_SESSION['role'] ?? 'employee';
                $analyticsLink = ($analyticsRole === 'project_manager' || $analyticsRole === 'team_leader') ? '/managerAnalytics.php' : '/employeeAnalytics.php';
                ?>
                <a href="<?php echo $analyticsLink; ?>"
                   class="mia-nav-link <?php echo str_contains($currentUri, 'Analytics') ? 'active' : ''; ?>">
                    Analytics
                </a>
            </div>

            <div class="mia-right">
                <a href="#" class="mia-settings-link"
                data-bs-toggle="modal" data-bs-target="#modalSettings">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>

                <div class="dropdown mia-profile-dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="mia-profile-btn"><?php echo htmlspecialchars($currentInitial); ?></div>
                        <span class="mia-profile-name"><?php echo htmlspecialchars($currentUser); ?></span>
                        <i class="bi bi-chevron-down mia-chevron"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end mia-dropdown-menu">
                        <li class="mia-dropdown-header">
                            <strong><?php echo htmlspecialchars($currentUser); ?></strong>
                            <span>Signed in</span>
                        </li>
                        <li>
                            <a class="dropdown-item mia-dropdown-item" href="#"
                            data-bs-toggle="modal" data-bs-target="#modalProfile">
                                <i class="bi bi-person-circle"></i>
                                Profile
                            </a>
                        </li>
                        <!-- <li>
                            <a class="dropdown-item mia-dropdown-item" href="#"
                            data-bs-toggle="modal" data-bs-target="#modalPreferences">
                                <i class="bi bi-sliders"></i>
                                Preferences
                            </a>
                        </li> -->
                        <li>
                            <a class="dropdown-item mia-dropdown-item" href="/logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </nav>
</div>


