<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Status Monitor - Professional Dashboard</title>
    
    <!-- Bulma CSS Framework -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Login Page -->
    <section id="loginPage" class="hero login-hero is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-narrow">
                        <div class="card login-card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    <span class="icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <span>Site Monitor Login</span>
                                </p>
                            </header>
                            <div class="card-content">
                                <div id="loginAlert"></div>
                                <form id="loginForm">
                                    <div class="field">
                                        <label class="label">Username</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" id="username" name="username" 
                                                   placeholder="Enter your username" required>
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="field">
                                        <label class="label">Password</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="password" id="password" name="password" 
                                                   placeholder="Enter your password" required>
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="field">
                                        <div class="control">
                                            <button type="submit" class="button is-primary is-fullwidth">
                                                <span class="icon">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </span>
                                                <span>Login</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard -->
    <div id="dashboard" class="is-hidden">
        <!-- Navigation Header -->
        <nav class="navbar dashboard-header" role="navigation">
            <div class="navbar-brand">
                <a class="navbar-item brand-title">
                    <span class="icon">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <span>Site Status Monitor</span>
                </a>
            </div>
            
            <div class="navbar-menu">
                <div class="navbar-end">
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            <span class="icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <span id="userDisplay">Admin</span>
                        </a>
                        <div class="navbar-dropdown is-right">
                            <a class="navbar-item" onclick="app.logout()">
                                <span class="icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container is-fluid">
            <div class="section">
                <!-- Statistics Cards -->
                <div class="columns stats-grid">
                    <div class="column">
                        <div class="card stat-card">
                            <div class="card-content has-text-centered">
                                <div class="stat-number is-primary" id="totalSites">0</div>
                                <div class="stat-label">Total Sites</div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card stat-card">
                            <div class="card-content has-text-centered">
                                <div class="stat-number is-success" id="onlineSites">0</div>
                                <div class="stat-label">Online</div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card stat-card">
                            <div class="card-content has-text-centered">
                                <div class="stat-number is-danger" id="offlineSites">0</div>
                                <div class="stat-label">Offline</div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card stat-card">
                            <div class="card-content has-text-centered">
                                <div class="stat-number is-warning" id="warningSites">0</div>
                                <div class="stat-label">Warning</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Site -->
                <div class="card site-management-card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-plus-circle"></i>
                            </span>
                            <span>Add New Site</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div id="addSiteAlert"></div>
                        <form id="addSiteForm">
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Site Name</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" id="siteName" name="siteName" 
                                                   placeholder="e.g., Company Website" required>
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Site URL</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="url" id="siteUrl" name="siteUrl" 
                                                   placeholder="https://example.com" required>
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-globe"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-narrow">
                                    <div class="field">
                                        <label class="label">&nbsp;</label>
                                        <div class="control">
                                            <button type="submit" class="button is-success">
                                                <span class="icon">
                                                    <i class="fas fa-plus"></i>
                                                </span>
                                                <span>Add Site</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sites List -->
                <div class="card sites-container">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-list"></i>
                            </span>
                            <span>Monitored Sites</span>
                        </p>
                        <div class="card-header-icon">
                            <button class="button is-primary" id="checkAllBtn" onclick="app.checkAllSites()">
                                <span class="icon">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <span>Check All Sites</span>
                            </button>
                        </div>
                    </header>
                    <div class="card-content" style="padding: 0;">
                        <div id="sitesList">
                            <!-- Sites will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/app.js"></script>
</body>
</html>