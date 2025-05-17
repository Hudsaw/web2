<div class="apresentacao">
    <div class="formulario-container">
        <h1><?= isset($_SESSION['id']) ? 'Editar Currículo' : 'Cadastre seu Currículo' ?></h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $erro): ?>
                    <p><?= $erro ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>?action=<?= isset($_SESSION['id']) ? 'atualizar' : 'cadastro' ?>">
            <h3>Dados Pessoais</h3>

            <div class="form-group">
                <input type="text" name="nome" placeholder="Nome completo" required
                    placeholder="Nome completo"
                    value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <input type="text" id="telefone" name="telefone" required
                    placeholder="Telefone (com DDD)"
                    pattern="[0-9]{10,11}" title="10 ou 11 dígitos"
                    value="<?php echo htmlspecialchars($dados['telefone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <input type="text" id="cpf" name="cpf" required
                    placeholder="CPF (somente números)"
                    pattern="\d{11}" title="11 dígitos sem pontuação"
                    value="<?php echo htmlspecialchars($dados['cpf'] ?? ''); ?>">
            </div>

            <!-- Endereço -->
            <div class="form-section">
                <h3>Endereço</h3>
                <div class="form-group">
                    <input type="text" id="cep" name="cep" required
                        placeholder="CEP"
                        pattern="\d{8}" title="8 dígitos"
                        value="<?php echo htmlspecialchars($dados['cep'] ?? ''); ?>">
                    <small id="cep-info" style="display: block; margin-top: 5px;"></small>
                </div>
                <div class="form-group">
                    <input type="text" id="logradouro" name="logradouro" required readonly
                        placeholder="Logradouro"
                        value="<?php echo htmlspecialchars($dados['logradouro'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <input type="text" id="complemento" name="complemento" required
                        placeholder="Número, bloco, apartamento"
                        value="<?php echo htmlspecialchars($dados['complemento'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <input type="text" id="cidade" name="cidade" required readonly
                        placeholder="Cidade"
                        value="<?php echo htmlspecialchars($dados['cidade'] ?? ''); ?>">
                </div>
            </div>

            <!-- Dados Profissionais -->
            <div class="form-group">
                <h3>Dados Profissionais</h3>
                <select id="select" name="area_atuacao_id" required>
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
            <div class="form-group" id="resumo-container">
                <input type="text" id="resumo" name="resumo"
                    placeholder="Resumo da carreira"
                    value="<?php echo htmlspecialchars($dados['resumo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <select id="select" name="escolaridade" required>
                    <option value="" disabled selected>Selecione sua escolaridade</option>
                    <option value="fundamental" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'fundamental' ? 'selected' : ''; ?>>Fundamental Completo</option>
                    <option value="medio" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'medio' ? 'selected' : ''; ?>>Médio Completo</option>
                    <option value="superior" <?php echo (isset($dados['escolaridade'])) && $dados['escolaridade'] === 'superior' ? 'selected' : ''; ?>>Superior Completo</option>
                </select>
            </div>

            <div class="form-group" id="experiencias-container">
                <input type="text" id="experiencias" name="experiencias"
                    placeholder="Experiencias Profissionais"
                    value="<?php echo htmlspecialchars($dados['experiencias'] ?? ''); ?>">
            </div>
            <div class="form-group" id="linkedin-container">
                <input type="text" id="linkedin" name="linkedin"
                    placeholder="Link do linkedin"
                    value="<?php echo htmlspecialchars($dados['linkedin'] ?? ''); ?>">
            </div>
            <div class="form-group" id="github-container">
                <input type="text" id="github" name="github"
                    placeholder="Link do github"
                    value="<?php echo htmlspecialchars($dados['github'] ?? ''); ?>">
            </div>

            <!-- Segurança -->
            <div class="form-section">
                <h3>Segurança</h3>
                <div class="form-group">
                    <input type="email" id="email" name="email" required
                        placeholder="E-mail"
                        value="<?php echo htmlspecialchars($dados['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <input type="password" id="senha" name="senha" required
                        placeholder="Senha (mínimo 8 caracteres)">
                </div>

                <div class="form-group">
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required
                        placeholder="Confirmar Senha">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($_SESSION['id']) ? 'Atualizar' : 'Criar Conta' ?></button>
        </form>

        <div class="auth-links">
            <a href="<?php echo BASE_URL; ?>?action=login">Já tem uma conta? Faça login</a>
        </div>
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