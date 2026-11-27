<?php
$isLoggedIn = isset($_SESSION['user_id']);
$userName = (string) ($_SESSION['user_name'] ?? '');
?>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">StartConnect</a>
        <nav class="main-nav" aria-label="Hauptnavigation">
        
            <a href="index.php#startups">Startups</a>
            <a href="investoren.php">Investoren</a>
            <a href="ueberuns.php">Über uns</a>
            <a href="kontakt.php">Kontakt</a>

            <?php if ($isLoggedIn): ?>
                <span class="header-user">
                    Willkommen, <?= htmlspecialchars($userName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>!
                </span>

                <a class="button button-dark" href="../actions/logout.php">
                    Abmelden
                </a>
            <?php else: ?>
                <a class="button button-dark" href="login.php">
                    Anmelden
                </a>

                <button type="button"
                        class="button button-dark"
                        id="open-signup-modal"
                        aria-haspopup="dialog">
                    Registrieren
                </button>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if (!$isLoggedIn): ?>
    <dialog id="signup-choice-dialog" aria-labelledby="signup-choice-title">
        <div class="signup-modal-card">
            <button type="button"
                    class="signup-modal-close"
                    id="close-signup-modal"
                    aria-label="Schließen">
                ✕
            </button>

            <p class="eyebrow">Konto erstellen</p>

            <h2 id="signup-choice-title">
                Als Unternehmen oder Investor registrieren?
            </h2>

            <p>Wähle den passenden Bereich für deine Registrierung.</p>

            <div class="signup-modal-actions">
                <a class="button button-dark" href="signup-company.php">
                    Unternehmen / Gründer <span aria-hidden="true">↗</span>
                </a>

                <a class="button button-primary" href="signup-investor.php">
                    Investor <span aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </dialog>

    <script>
        (() => {
            const dialog = document.getElementById('signup-choice-dialog');
            const openButton = document.getElementById('open-signup-modal');
            const closeButton = document.getElementById('close-signup-modal');

            openButton.addEventListener('click', () => {
                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                } else {
                    dialog.setAttribute('open', '');
                }
            });

            function closeDialog() {
                if (typeof dialog.close === 'function') {
                    dialog.close();
                } else {
                    dialog.removeAttribute('open');
                }
            }

            closeButton.addEventListener('click', closeDialog);

            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    closeDialog();
                }
            });
        })();
    </script>
<?php endif; ?>


