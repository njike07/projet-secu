<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$db = new Database();

// Vérification de l'authentification
if (!$auth->isLoggedIn()) {
    header("Location: pageLogin.php?msg=Veuillez vous connecter&type=error");
    exit();
}

$message = '';
$messageType = 'info';

// Configuration sécurisée des uploads
$uploadDir = 'uploads/documents/';
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

// Créer le dossier d'upload s'il n'existe pas
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Traitement de l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    // Validation CSRF
    if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de sécurité invalide';
        $messageType = 'error';
    } else {
        $typeDocument = Database::validateInput($_POST['type_document'] ?? '');
        $description = Database::validateInput($_POST['description'] ?? '');
        
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['document'];
            
            // Validations de sécurité
            $fileName = $file['name'];
            $fileSize = $file['size'];
            $fileTmpName = $file['tmp_name'];
            $fileType = $file['type'];
            
            // Vérifier la taille
            if ($fileSize > $maxFileSize) {
                $message = 'Le fichier est trop volumineux (max 5MB)';
                $messageType = 'error';
            }
            // Vérifier l'extension
            else {
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $message = 'Type de fichier non autorisé';
                    $messageType = 'error';
                }
                // Vérifier le type MIME
                else if (!in_array($fileType, $allowedTypes)) {
                    $message = 'Type MIME non autorisé';
                    $messageType = 'error';
                }
                // Vérifier que c'est vraiment un fichier uploadé
                else if (!is_uploaded_file($fileTmpName)) {
                    $message = 'Erreur de sécurité lors de l\'upload';
                    $messageType = 'error';
                }
                else {
                    // Générer un nom de fichier sécurisé
                    $safeFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                    $filePath = $uploadDir . $safeFileName;
                    
                    // Déplacer le fichier
                    if (move_uploaded_file($fileTmpName, $filePath)) {
                        // Enregistrer en base de données
                        try {
                            $stmt = $db->prepare("
                                INSERT INTO documents (user_id, type_document, nom_fichier, chemin_fichier, taille_fichier, type_mime, statut, date_upload, commentaire_admin) 
                                VALUES (?, ?, ?, ?, ?, ?, 'en_attente', NOW(), ?)
                            ");
                            $stmt->execute([
                                $_SESSION['user_id'],
                                $typeDocument,
                                $fileName,
                                $filePath,
                                $fileSize,
                                $fileType,
                                $description
                            ]);
                            
                            // Journalisation
                            $stmt = $db->prepare("INSERT INTO modifications (user_id, action, details, ip_address, user_agent, modification_time) VALUES (?, ?, ?, ?, ?, NOW())");
                            $stmt->execute([
                                $_SESSION['user_id'],
                                'upload_document',
                                "Upload du document: {$fileName} (type: {$typeDocument})",
                                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                            ]);
                            
                            $message = 'Document uploadé avec succès';
                            $messageType = 'success';
                        } catch (PDOException $e) {
                            // Supprimer le fichier en cas d'erreur de base
                            unlink($filePath);
                            error_log("Document upload DB error: " . $e->getMessage());
                            $message = 'Erreur lors de l\'enregistrement';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Erreur lors du déplacement du fichier';
                        $messageType = 'error';
                    }
                }
            }
        } else {
            $message = 'Aucun fichier sélectionné ou erreur d\'upload';
            $messageType = 'error';
        }
    }
}

// Récupération des documents de l'utilisateur
$stmt = $db->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY date_upload DESC");
$stmt->execute([$_SESSION['user_id']]);
$userDocuments = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $messageType = isset($_GET['type']) ? $_GET['type'] : 'info';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Documents - Cosendai</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 25px; margin: 20px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .upload-area { border: 2px dashed #ccc; border-radius: 10px; padding: 40px; text-align: center; margin: 20px 0; transition: all 0.3s; }
        .upload-area:hover { border-color: #667eea; background-color: #f8f9ff; }
        .upload-area.dragover { border-color: #667eea; background-color: #e3f2fd; }
        .file-input { display: none; }
        .upload-btn { background: #667eea; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .upload-btn:hover { background: #5a67d8; }
        .document-item { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-en-attente { background: #fff3cd; color: #856404; }
        .status-valide { background: #d4edda; color: #155724; }
        .status-refuse { background: #f8d7da; color: #721c24; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .progress-bar { width: 100%; height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; background: #28a745; transition: width 0.3s; }
        .file-info { display: none; margin: 10px 0; padding: 10px; background: #e3f2fd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-file-upload"></i> Gestion des Documents</h1>
            <p>Uploadez vos documents requis pour votre inscription</p>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Formulaire d'upload -->
        <div class="card">
            <h2><i class="fas fa-cloud-upload-alt"></i> Nouveau Document</h2>
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label class="w3-text-blue"><b>Type de document *</b></label>
                        <select name="type_document" class="w3-select w3-border" required>
                            <option value="">Sélectionnez le type</option>
                            <option value="piece_identite">Pièce d'identité</option>
                            <option value="diplome">Diplôme</option>
                            <option value="photo_identite">Photo d'identité</option>
                            <option value="justificatif_domicile">Justificatif de domicile</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="w3-half">
                        <label class="w3-text-blue"><b>Description (optionnelle)</b></label>
                        <input type="text" name="description" class="w3-input w3-border" placeholder="Description du document">
                    </div>
                </div>

                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                    <p><strong>Glissez-déposez votre fichier ici</strong></p>
                    <p>ou</p>
                    <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-folder-open"></i> Choisir un fichier
                    </button>
                    <input type="file" name="document" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" required>
                    <p style="margin-top: 15px; font-size: 0.9em; color: #666;">
                        Formats acceptés: JPG, PNG, GIF, PDF, DOC, DOCX (max 5MB)
                    </p>
                </div>

                <div class="file-info" id="fileInfo">
                    <strong>Fichier sélectionné:</strong> <span id="fileName"></span><br>
                    <strong>Taille:</strong> <span id="fileSize"></span><br>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" name="upload" class="w3-button w3-blue w3-large">
                        <i class="fas fa-upload"></i> Uploader le document
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des documents -->
        <div class="card">
            <h2><i class="fas fa-file-alt"></i> Mes Documents</h2>
            <?php if (empty($userDocuments)): ?>
                <p class="w3-text-grey">Aucun document uploadé pour le moment.</p>
            <?php else: ?>
                <?php foreach ($userDocuments as $doc): ?>
                <div class="document-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong><?php echo htmlspecialchars($doc['nom_fichier']); ?></strong>
                            <span class="status-badge status-<?php echo $doc['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $doc['statut'])); ?>
                            </span>
                            <br>
                            <small>
                                Type: <?php echo htmlspecialchars(str_replace('_', ' ', $doc['type_document'])); ?> | 
                                Taille: <?php echo number_format($doc['taille_fichier'] / 1024, 1); ?> KB | 
                                Uploadé le <?php echo date('d/m/Y H:i', strtotime($doc['date_upload'])); ?>
                            </small>
                            <?php if ($doc['commentaire_admin']): ?>
                                <br><em class="w3-text-blue">Commentaire admin: <?php echo htmlspecialchars($doc['commentaire_admin']); ?></em>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($auth->isAdmin()): ?>
                            <a href="<?php echo htmlspecialchars($doc['chemin_fichier']); ?>" target="_blank" class="w3-button w3-small w3-blue">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <?php endif; ?>
                            <button onclick="deleteDocument(<?php echo $doc['id']; ?>)" class="w3-button w3-small w3-red">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="index.php" class="w3-button w3-grey">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>
    </div>

    <script>
        // Gestion du drag & drop
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFileInfo(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showFileInfo(e.target.files[0]);
            }
        });

        function showFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            fileInfo.style.display = 'block';
            
            // Simulation de progression
            const progressFill = document.getElementById('progressFill');
            progressFill.style.width = '100%';
        }

        function deleteDocument(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
                fetch('delete_document.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id + '&csrf_token=<?php echo $auth->generateCSRFToken(); ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        }

        // Validation côté client
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            if (file) {
                // Vérifier la taille
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('Le fichier est trop volumineux (max 5MB)');
                    return;
                }
                
                // Vérifier l'extension
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(fileExtension)) {
                    e.preventDefault();
                    alert('Type de fichier non autorisé');
                    return;
                }
            }
        });
    </script>
</body>
</html>
