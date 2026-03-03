<?php
session_start();
$ADMIN_PASSWORD = "Ndiougahmad16";

// Connexion à la base
$conn = new mysqli("localhost","root","","photographe_db");
if($conn->connect_error) die("Erreur de connexion: ".$conn->connect_error);

// Gestion mot de passe admin
if(isset($_POST['password'])){
    if($_POST['password'] === $ADMIN_PASSWORD){
        $_SESSION['admin_logged'] = true;
    } else {
        $error = "Mot de passe incorrect";
    }
}

// Vérifie si admin connecté
$isAdmin = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'];

// Ajouter un tarif
if($isAdmin && isset($_POST['pack'], $_POST['description'], $_POST['prix'])){
    $stmt = $conn->prepare("INSERT INTO tarifs (pack, description, prix) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $_POST['pack'], $_POST['description'], $_POST['prix']);
    $stmt->execute();
    $stmt->close();
}

// Modifier un tarif
if($isAdmin && isset($_POST['edit_id'], $_POST['edit_pack'], $_POST['edit_description'], $_POST['edit_prix'])){
    $stmt = $conn->prepare("UPDATE tarifs SET pack=?, description=?, prix=? WHERE id=?");
    $stmt->bind_param("ssdi", $_POST['edit_pack'], $_POST['edit_description'], $_POST['edit_prix'], $_POST['edit_id']);
    $stmt->execute();
    $stmt->close();
}

// Supprimer un tarif
if($isAdmin && isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tarifs WHERE id=$id");
}

// Récupérer tous les tarifs
$result = $conn->query("SELECT * FROM tarifs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tarifs - Sixteen Prod</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body{ font-family:Arial; margin:0; padding:0; background:url('images/fond.jpg') no-repeat center center fixed; background-size:cover; color:#fff; }
        h1{text-align:center; margin-top:30px; text-shadow:2px 2px 4px rgba(0,0,0,0.7);}
        .tarif-table-container{width:90%; max-width:900px; margin:40px auto; overflow-x:auto; background:rgba(0,0,0,0.5); border-radius:10px; padding:20px;}
        table{width:100%; border-collapse:collapse; color:#fff;}
        th, td{padding:12px 15px; border-bottom:1px solid rgba(255,255,255,0.3);}
        th{background:rgba(106,13,173,0.8);}
        tr:hover{background:rgba(255,255,255,0.1);}
        td{font-size:15px;}
        @media screen and (max-width:768px){th, td{padding:10px; font-size:14px;}}
        .admin-btn{text-align:right; margin:20px 5%;}
        .admin-btn button{background:#ccc; color:#333; border:none; padding:5px 10px; font-size:12px; cursor:pointer;}
        .action-btn{padding:5px 8px; border:none; border-radius:3px; cursor:pointer;}
        .delete{background:red;color:#fff;}
        .edit{background:green;color:#fff;}
        #adminForm{background:rgba(0,0,0,0.7); padding:15px; margin:20px 5%; border-radius:8px;}
        #adminForm input{padding:5px; margin:5px;}
    </style>
</head>
<body>

<h1>Nos Tarifs</h1>

<div class="tarif-table-container">
    <table>
        <thead>
            <tr>
                <th>Pack</th>
                <th>Détails</th>
                <th>Prix</th>
                <?php if($isAdmin) echo "<th>Actions</th>"; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if($result && $result->num_rows>0){
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['pack'])."</td>";
                    echo "<td>".htmlspecialchars($row['description'])."</td>";
                    echo "<td>".htmlspecialchars($row['prix'])."</td>";
                    if($isAdmin){
                        echo "<td>
                            <a href='?delete=".$row['id']."' onclick=\"return confirm('Supprimer ce tarif ?')\">
                                <button class='action-btn delete'>Supprimer</button>
                            </a>
                            <button class='action-btn edit' onclick=\"ouvrirModifier(".$row['id'].",'".addslashes($row['pack'])."','".addslashes($row['description'])."',".$row['prix'].")\">Modifier</button>
                        </td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='".($isAdmin?4:3)."' style='text-align:center;'>Aucun tarif disponible.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="admin-btn">
    <button onclick="ouvrirAdmin()">Admin Tarifs</button>
</div>

<?php if($isAdmin){ ?>
<div id="adminForm">
    <h3>Ajouter un Tarif</h3>
    <form method="post">
        <input type="text" name="pack" placeholder="Nom du pack" required>
        <input type="text" name="description" placeholder="Description" required>
        <input type="number" step="0.01" name="prix" placeholder="Prix" required>
        <button type="submit">Ajouter</button>
    </form>

    <div id="modifierForm" style="display:none;">
        <h3>Modifier un Tarif</h3>
        <form method="post">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="edit_pack" id="edit_pack" placeholder="Nom du pack" required>
            <input type="text" name="edit_description" id="edit_description" placeholder="Description" required>
            <input type="number" step="0.01" name="edit_prix" id="edit_prix" placeholder="Prix" required>
            <button type="submit">Modifier</button>
            <button type="button" onclick="document.getElementById('modifierForm').style.display='none'">Annuler</button>
        </form>
    </div>
</div>
<?php } ?>

<script>
function ouvrirAdmin(){
    var mdp = prompt("Entrez le mot de passe pour accéder à l'administration :");
    if(mdp==="Ndiougahmad16"){
        window.location.href=window.location.href+"?admin=1";
    } else { alert("Mot de passe incorrect"); }
}

function ouvrirModifier(id, pack, description, prix){
    document.getElementById('modifierForm').style.display='block';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_pack').value = pack;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_prix').value = prix;
    window.scrollTo(0, document.body.scrollHeight);
}
</script>

</body>
</html>