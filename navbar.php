<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">Sixteen Prod</a>
        <ul class="nav-links">
            <li><a href="reserver.php">Réserver</a></li>
            <li><a href="galerie.php">Galerie</a></li>
            <li><a href="tarifs.php">Tarifs</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="avis.php">Avis Clients</a></li>
            <li style="float:right;">
                <a href="login.php" style="background: #6a0dad; color: #fff; padding: 6px 18px; border-radius: 5px;">Connexion</a>
            </li>
        </ul>
    </div>
</nav>
<style>
.navbar {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 35px;
}
.nav-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 15px;
}
.nav-logo {
    font-weight: bold;
    color: #6a0dad;
    font-size: 1.5em;
    letter-spacing: 2px;
    text-decoration: none;
}
.nav-links {
    list-style: none;
    margin: 0;
    display: flex;
    gap: 18px;
    padding: 0;
}
.nav-links li { margin: 0; }
.nav-links a {
    color: #333;
    text-decoration: none;
    font-size: 1em;
    transition: color 0.2s;
}
.nav-links a:hover {
    color: #6a0dad;
    text-decoration: underline;
}
@media screen and (max-width: 700px) {
    .nav-container { flex-direction: column; gap: 16px; }
    .nav-links { flex-direction: column; gap: 12px; }
}
</style>