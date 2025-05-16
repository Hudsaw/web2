    </main>
    <footer>
        <div class="secao">
            <div class="icon-container">
                <?php if (!empty($usuario['linkedin'])): ?>
                    <a href="<?= htmlspecialchars($usuario['linkedin']) ?>" target="_blank" class="icon">
                        <img src="https://img.icons8.com/ios-filled/100/linkedin.png" alt="LinkedIn">
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($usuario['github'])): ?>
                    <a href="<?= htmlspecialchars($usuario['github']) ?>" target="_blank" class="icon">
                        <img src="https://img.icons8.com/ios-filled/100/github.png" alt="GitHub">
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</body>
</html>