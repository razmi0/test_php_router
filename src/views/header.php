<header>
    <nav class="container">
        <ul>
            <li>
                <h1 id="header-title">API Product Interface</h1>
            </li>
        </ul>
        <ul>
            <!-- Test the API Link -->
            <li>
                <a href="/" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/' ? 'active' : ''); ?>">Test the API</a>
            </li>

            <li>
                <ul dir="rtl">
                    <?php if (!isset($_SESSION['id'])) : ?>
                        <!-- User Not Logged In -->
                        <li>
                            <a href="/login" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/login' ? 'active' : ''); ?>">Log in</a>
                        </li>
                        <li>
                            <a href="/signup" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/signup' ? 'active' : ''); ?>">Sign up</a>
                        </li>
                    <?php else : ?>
                        <!-- User Logged In -->
                        <li>
                            <a href="/logout" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/logout' ? 'active' : ''); ?>">Log out</a>
                        </li>
                    <?php endif; ?>

                </ul>
            </li>
        </ul>
    </nav>
</header>