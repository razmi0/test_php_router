<header>
    <nav class="container">
        <ul>
            <li>
                <h1>API Product Interface</h1>
            </li>
        </ul>
        <ul>
            <li><a href="/" class="secondary"><?php echo ($_SERVER['REQUEST_URI'] == '/' ? '› ' : ''); ?>Test the API</a></li>
            <li>
                <ul dir="rtl">
                    <li><a href="/login"><?php echo ($_SERVER['REQUEST_URI'] == '/login' ? '› ' : ''); ?>Log in</a></li>
                    <li><a href="/signup"><?php echo ($_SERVER['REQUEST_URI'] == '/signup' ? '› ' : ''); ?>Sign up</a></li>
                    <li>
                        <button data-theme class="outline" data-tooltip="Toggle theme" data-placement="left">
                            <img src="/assets/theme-icon.svg" alt="theme">
                        </button>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>