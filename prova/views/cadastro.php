<div class="container">
    <div class="card">
        <h1><?= isset($_SESSION['id']) ? 'Editar Currículo' : 'Cadastre seu Currículo' ?></h1>
        
        <?php if (!empty($erros)): ?>
            <div class="alert alert-error">
                <?php foreach ($erros as $erro): ?>
                    <p><?= $erro ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/<?= isset($_SESSION['id']) ? 'atualizar' : 'cadastro' ?>"class="form-cadastro">
            
            <!--Dados Pessoais-->
            <h3>Dados Pessoais</h3>
            <div class="form-group">
                <label for="nome">Nome completo</label>
                <input class="input" id="nome" type="text" name="nome" required
                    value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="telefone">Telefone (com DDD)</label>
                <input class="input" id="telefone" type="text" name="telefone" required
                    pattern="[0-9]{10,11}" title="10 ou 11 dígitos"
                    value="<?php echo htmlspecialchars($dados['telefone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="cpf">CPF (somente números)</label>
                <input class="input" type="text" id="cpf" name="cpf" required
                    pattern="\d{11}" title="11 dígitos sem pontuação"
                    value="<?php echo htmlspecialchars($dados['cpf'] ?? ''); ?>">
            </div>

            <!-- Endereço -->
            <div class="form-section">
                <h3>Endereço</h3>
                <div class="form-endereco">
                <small id="cep-info"></small>
                    <div id="cep-container">
                    
                        <div class="form-group">
                            <label for="cep">CEP (somente números)</label>
                            <input class="input" type="text" id="cep" name="cep" required  pattern="\d{8}" title="8 dígitos"
                        value="<?php echo htmlspecialchars($dados['cep'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="logradouro">Logradouro</label>
                        <input class="input" type="text" id="logradouro" name="logradouro" required readonly
                        value="<?php echo htmlspecialchars($dados['logradouro'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input class="input" type="text" id="complemento" name="complemento" required
                        value="<?php echo htmlspecialchars($dados['complemento'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input class="input" type="text" id="cidade" name="cidade" required readonly
                        value="<?php echo htmlspecialchars($dados['cidade'] ?? ''); ?>">
                    </div>
                </div>    
            </div>

            <!-- Dados Profissionais -->

            <h3>Dados Profissionais</h3>
            <div class="form-group">
                <label for="select1">Área de atuação</label>
                <select id="select1" name="area_atuacao_id" required>
                    <option value="">Selecione sua área de atuação</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?= $area['id'] ?>"
                            <?= (isset($dados['area_atuacao_id']) && $dados['area_atuacao_id'] == $area['id']) ||
                                (isset($usuario['area_atuacao_id']) && $usuario['area_atuacao_id'] == $area['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($area['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="select2">Escolaridade</label>
                <select id="select2" name="escolaridade" required>
                    <option value="" disabled selected>Selecione sua escolaridade</option>
                    <option value="fundamental" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'fundamental' ? 'selected' : ''; ?>>Fundamental Completo</option>
                    <option value="medio" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'medio' ? 'selected' : ''; ?>>Médio Completo</option>
                    <option value="superior" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'superior' ? 'selected' : ''; ?>>Superior Completo</option>
                </select>
            </div>
            <div class="form-group" id="resumo-container">
                <label for="resumo">Resumo da carreira</label>
                <textarea class="input" id="resumo" name="resumo"><?php echo htmlspecialchars($dados['resumo'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" id="experiencias-container">
                <label for="experiencias">Experiências Profissionais</label>
                <textarea class="input" id="experiencias" name="experiencias"><?php echo htmlspecialchars($dados['experiencias'] ?? ''); ?></textarea>
            </div>

            <!-- Links -->
            <h3>Links</h3>
            <div class="form-group" id="linkedin-container">
                <label for="linkedin">Link do LinkedIn</label>
                <input class="input" type="text" id="linkedin" name="linkedin"
                    placeholder="Link do LinkedIn"
                    value="<?php echo htmlspecialchars($dados['linkedin'] ?? ''); ?>">
            </div>
            <div class="form-group" id="github-container">
                <label for="github">Link do GitHub</label>
                <input class="input" type="text" id="github" name="github"
                    placeholder="Link do GitHub"
                    value="<?php echo htmlspecialchars($dados['github'] ?? ''); ?>">
            </div>

            <!-- Segurança -->
            <div class="form-section">
                <h3>Segurança</h3>
                <div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input class="input" type="email" id="email" name="email" required value="<?php echo htmlspecialchars($dados['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input class="input" type="password" id="senha" name="senha" required value="<?php echo htmlspecialchars($dados['senha'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <input class="input" type="password" id="confirmar_senha" name="confirmar_senha" required value="<?php echo htmlspecialchars($dados['confirmar_senha'] ?? ''); ?>">
                    </div>
                </div> 
            </div>
            <div id="actions">
                <button type="submit" class="btn btn-primary"><?= isset($_SESSION['id']) ? 'Atualizar' : 'Criar Conta' ?></button>
                <div class="auth-links">
                    <span>Já tem uma conta?</span>
                    <a href="<?= BASE_URL ?>/login" class="link-btn"> Faça login</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('cep').addEventListener('blur', async function() {
        const cepInput = this;
        const cep = cepInput.value.replace(/\D/g, '');
        const logradouroInput = document.getElementById('logradouro');
        const cidadeInput = document.getElementById('cidade');
        const cepInfo = document.getElementById('cep-info'); 

        // Verifica se o CEP tem 8 dígitos
        if (cep.length !== 8) {
            cepInfo.textContent = 'CEP deve ter 8 dígitos';
            cepInfo.style.color = 'red';
            return;
        }

        try {
            cepInfo.textContent = 'Buscando CEP...';
            cepInfo.style.color = 'blue';

            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

            if (!response.ok) {
                throw new Error('Erro na requisição');
            }

            const data = await response.json();

            if (data.erro) {
                throw new Error('CEP não encontrado');
            }

            // Preenche os campos
            logradouroInput.value = data.logradouro || '';
            cidadeInput.value = data.localidade || '';

            cepInfo.textContent = 'CEP encontrado!';
            cepInfo.style.color = 'green';

        } catch (error) {
            logradouroInput.value = '';
            cidadeInput.value = '';

            cepInfo.textContent = error.message || 'Erro ao buscar CEP';
            cepInfo.style.color = 'red';
        }
    });

    document.getElementById('senha').addEventListener('input', function() {
        document.getElementById('senha-feedback').textContent = this.value;
    });
</script>