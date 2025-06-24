<footer>
    <div class="container">
        <div class="icon-container">
            <?php 
            // Definir qual conjunto de dados usar
            $dadosFooter = isset($curriculo) ? $curriculo : (isset($usuario) ? $usuario : null);
            
            // Links padrão 
            $linkedinPadrao = "https://br.linkedin.com/in/hudson-borges-5858a2102";
            $githubPadrao = "https://github.com/hudsaw";
            ?>
            
            <!-- LinkedIn -->
            <a href="<?= !empty($dadosFooter['linkedin']) ? htmlspecialchars($dadosFooter['linkedin']) : $linkedinPadrao ?>" 
               target="_blank" class="icon">
                <img src="https://img.icons8.com/ios-filled/100/linkedin.png" alt="LinkedIn">
            </a>
            
            <!-- GitHub -->
            <a href="<?= !empty($dadosFooter['github']) ? htmlspecialchars($dadosFooter['github']) : $githubPadrao ?>" 
               target="_blank" class="icon">
                <img src="https://img.icons8.com/ios-filled/100/github.png" alt="GitHub">
            </a>
        </div>
        
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Todos os direitos reservados</p>
        </div>
    </div>
</footer>
</body>
</html>