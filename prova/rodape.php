<?php
// Carrega os dados do usuário se estiver logado
if (isset($_SESSION['id'])) {
    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM curriculo WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao carregar usuário: " . $e->getMessage());
        $usuario = [];
    }
} else {
    $usuario = [];
}
?>
<footer>
    <div class="secao">
        <div class="icon-container">
            <?php 
            // Verifica se $usuario existe e é um array
            $usuario = $usuario ?? [];
            
            if (!empty($usuario['linkedin'])): ?>
                <a href="<?php echo htmlspecialchars($usuario['linkedin']); ?>" target="_blank" class="icon">
                    <img src="https://img.icons8.com/ios-filled/100/linkedin.png" alt="LinkedIn">
                </a>
            <?php endif; ?>
            
            <?php if (!empty($usuario['github'])): ?>
                <a href="<?php echo htmlspecialchars($usuario['github']); ?>" target="_blank" class="icon">
                    <img src="https://img.icons8.com/ios-filled/100/github.png" alt="GitHub">
                </a>
            <?php endif; ?>
            
            <?php if (!empty($usuario['telefone'])): ?>
                <a href="https://wa.me/55<?php echo preg_replace('/[^0-9]/', '', $usuario['telefone']); ?>" target="_blank" class="icon">
                    <img src="https://img.icons8.com/ios-filled/100/whatsapp.png" alt="WhatsApp">
                </a>
            <?php endif; ?>
            
            <?php if (!empty($usuario['email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($usuario['email']); ?>" class="icon">
                    <img src="https://img.icons8.com/ios-filled/100/email.png" alt="Email">
                </a>
            <?php endif; ?>
        </div>
    </div>
</footer>