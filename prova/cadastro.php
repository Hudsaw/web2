<?php

require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$erros = $_SESSION['erros_cadastro'] ?? [];
$dados = $_SESSION['dados_cadastro'] ?? [];
unset($_SESSION['erros_cadastro']);
unset($_SESSION['dados_cadastro']);


require_once __DIR__ . '/header.php';
?>

<div class="apresentacao">
    <h1 class="auth-title">Crie sua conta</h1>
    <form class="auth-form" id="form-cadastro" method="POST" action="<?php echo BASE_URL; ?>AuthController.php?action=register">
        <!-- Dados Pessoais -->
        <div class="form-section">
            <h3>Dados Pessoais</h3>
            <div class="form-group">
                <input type="text" id="nome" name="nome" required
                    placeholder="Nome completo"
                    value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <input type="text" id="telefone" name="telefone" required
                    placeholder="Telefone (com DDD)"
                    pattern="[0-9]{10,11}" title="10 ou 11 dígitos"
                    value="<?php echo htmlspecialchars($dados['telefone'] ?? ''); ?>">
                <small class="input-hint">Exemplo: 11987654321</small>
            </div>

            <div class="form-group">
                <input type="text" id="cpf" name="cpf" required
                    placeholder="CPF (somente números)"
                    pattern="\d{11}" title="11 dígitos sem pontuação"
                    value="<?php echo htmlspecialchars($dados['cpf'] ?? ''); ?>">
                <small class="input-hint">Digite apenas números</small>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" required
                    placeholder="E-mail"
                    value="<?php echo htmlspecialchars($dados['email'] ?? ''); ?>">
                <small class="input-hint">Exemplo: seu@email.com</small>
            </div>
        </div>

        <!-- Endereço -->
        <div class="form-section">
            <h3>Endereço</h3>
            <div class="form-group">
                <input type="text" id="cep" name="cep" required
                    placeholder="CEP"
                    pattern="\d{8}" title="8 dígitos"
                    value="<?php echo htmlspecialchars($dados['cep'] ?? ''); ?>">
                <small class="cep-info" style="color: var(--erro);"></small>
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
        </div>
        <div class="form-group">
            <input type="text" id="cidade" name="cidade" required readonly
                placeholder="Cidade"
                value="<?php echo htmlspecialchars($dados['cidade'] ?? ''); ?>">
        </div>
        <!-- Dados Profissionais -->
        <div class="form-group" id="escolaridade-container">
            <select id="escolaridade" name="escolaridade" required>
                <option value="" disabled selected>Selecione sua escolaridade</option>
                <option value="fundamental" <?php echo (isset($dados['escolaridade']) && $dados['escolaridade']) === 'fundamental' ? 'selected' : ''; ?>>Fundamental Completo</option>
                <option value="medio" <?php echo (isset($dados['escolaridade']) && $dados['escolaridade']) === 'medio' ? 'selected' : ''; ?>>Médio Completo</option>
                <option value="superior" <?php echo (isset($dados['escolaridade']) && $dados['escolaridade']) === 'superior' ? 'selected' : ''; ?>>Superior Completo</option>
            </select>
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
        <div class="form-group" id="resumo-container">
            <input type="text" id="resumo" name="resumo"
                placeholder="Resumo da carreira"
                value="<?php echo htmlspecialchars($dados['resumo'] ?? ''); ?>">
        </div>
        <div class="form-group" id="experiencias-container">
            <input type="text" id="experiencias" name="experiencias"
                placeholder="Experiencias Profissionais"
                value="<?php echo htmlspecialchars($dados['experiencias'] ?? ''); ?>">
        </div>

        <!-- Segurança -->
        <div class="form-section">
            <h3>Segurança</h3>
            <div class="form-group">
                <input type="password" id="senha" name="senha" required
                    placeholder="Senha (mínimo 8 caracteres)">
                <div class="senha-feedback"></div>
            </div>

            <div class="form-group">
                <input type="password" id="confirmar_senha" name="confirmar_senha" required
                    placeholder="Confirmar Senha">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Criar Conta
        </button>
    </form>

    <div class="auth-links">
        <a href="<?php echo BASE_URL; ?>login.php">Já tem uma conta? Faça login</a>
    </div>
</div>

<script>
    // Inicializa o estado ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {});

    document.getElementById('email').addEventListener('blur', function() {
        if (!validarEmail(this.value)) {
            this.setCustomValidity('Por favor, insira um e-mail válido');
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Consulta CEP (ViaCEP)
    document.getElementById('cep').addEventListener('blur', async function() {
        const cepInput = this;
        const cep = cepInput.value.replace(/\D/g, '');
        const cepInfo = cepInput.nextElementSibling;
        const logradouroInput = document.getElementById('logradouro');
        const cidadeInput = document.getElementById('cidade');

        // Limpa estados anteriores
        cepInput.classList.remove('campo-invalido');
        cepInfo.textContent = '';

        if (cep.length !== 8) {
            cepInfo.textContent = 'CEP deve ter 8 dígitos';
            cepInfo.style.color = 'var(--erro)';
            cepInput.classList.add('campo-invalido');
            return;
        }

        try {
            cepInfo.textContent = 'Buscando CEP...';
            cepInfo.style.color = 'var(--info)';

            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                throw new Error('CEP não encontrado');
            }

            // Preenche automaticamente
            logradouroInput.value = data.logradouro || '';
            cidadeInput.value = data.localidade || '';

            cepInfo.textContent = 'CEP válido';
            cepInfo.style.color = 'var(--sucesso)';

        } catch (error) {
            logradouroInput.value = '';
            cidadeInput.value = '';

            cepInfo.textContent = error.message || 'Erro ao buscar CEP';
            cepInfo.style.color = 'var(--erro)';
            cepInput.classList.add('campo-invalido');
        }
    });

    // Função para validar todo o formulário
    function validarFormulario() {
        const erros = [];
        const camposInvalidos = [];

        // Validação do nome
        const nomeInput = document.getElementById('nome');
        const nome = nomeInput.value.trim();
        if (nome.length < 3) {
            erros.push('Nome deve ter pelo menos 3 caracteres');
            camposInvalidos.push(nomeInput);
        }

        // Validação do telefone
        const telefoneInput = document.getElementById('telefone');
        const telefone = telefoneInput.value.replace(/\D/g, '');
        if (telefone.length < 10 || telefone.length > 11) {
            erros.push('Telefone deve conter 10 ou 11 dígitos');
            camposInvalidos.push(telefoneInput);
        }

        // Validação do CPF
        const cpfInput = document.getElementById('cpf');
        const cpf = cpfInput.value.replace(/\D/g, '');
        if (cpf.length !== 11) {
            erros.push('CPF deve conter 11 dígitos');
            camposInvalidos.push(cpfInput);
        }

        // Validação do email
        const emailInput = document.getElementById('email');
        const email = emailInput.value.trim();
        if (!validarEmail(email)) {
            erros.push('Por favor, insira um e-mail válido');
            camposInvalidos.push(emailInput);
        }

        // Validação do CEP
        const cepInput = document.getElementById('cep');
        const cep = cepInput.value.replace(/\D/g, '');
        const logradouro = document.getElementById('logradouro').value.trim();

        if (cep.length !== 8) {
            erros.push('CEP deve conter 8 dígitos');
            camposInvalidos.push(cepInput);
        }

        // Validação da senha
        const senhaInput = document.getElementById('senha');
        const senha = senhaInput.value;
        if (senha.length < 8) {
            erros.push('A senha deve ter pelo menos 8 caracteres');
            camposInvalidos.push(senhaInput);
        }

        // Validação da confirmação de senha
        const confirmarSenhaInput = document.getElementById('confirmar_senha');
        const confirmarSenha = confirmarSenhaInput.value;
        if (senha !== confirmarSenha) {
            erros.push('As senhas não coincidem');
            camposInvalidos.push(confirmarSenhaInput);
        }

        // Remove classes de erro de validações anteriores
        document.querySelectorAll('.campo-invalido').forEach(el => {
            el.classList.remove('campo-invalido');
        });

        // Adiciona classe de erro nos campos inválidos
        camposInvalidos.forEach(input => {
            input.classList.add('campo-invalido');
        });

        return erros;
    }

    // Função para validar email
    function validarEmail(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
    }

    function validarForcaSenha(senha) {
        const erros = [];

        if (senha.length < 8) {
            erros.push("A senha deve ter pelo menos 8 caracteres");
        }

        if (!/[A-Z]/.test(senha)) {
            erros.push("A senha deve conter pelo menos uma letra maiúscula");
        }

        if (!/[a-z]/.test(senha)) {
            erros.push("A senha deve conter pelo menos uma letra minúscula");
        }

        if (!/[0-9]/.test(senha)) {
            erros.push("A senha deve conter pelo menos um número");
        }

        return erros;
    }

    document.getElementById('senha').addEventListener('input', function() {
        const senha = this.value;
        const feedback = document.querySelector('.senha-feedback');

        let mensagem = '';
        let cor = 'red';

        if (senha.length === 0) {
            feedback.innerHTML = '';
            return;
        }

        // Verificar requisitos
        const temMaiuscula = /[A-Z]/.test(senha);
        const temMinuscula = /[a-z]/.test(senha);
        const temNumero = /[0-9]/.test(senha);
        const temTamanho = senha.length >= 8;

        if (temMaiuscula && temMinuscula && temNumero && temTamanho) {
            mensagem = 'Senha forte ✓';
            cor = 'green';
        } else {
            mensagem = 'A senha precisa de:';
            if (!temMaiuscula) mensagem += '<br>- 1 letra maiúscula';
            if (!temMinuscula) mensagem += '<br>- 1 letra minúscula';
            if (!temNumero) mensagem += '<br>- 1 número';
            if (!temTamanho) mensagem += '<br>- Mínimo 8 caracteres';
        }

        feedback.innerHTML = mensagem;
        feedback.style.color = cor;
    });

    // Evento submit do formulário
    document.getElementById('form-cadastro').addEventListener('submit', function(e) {
        const erros = validarFormulario();
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;

        if (senha !== confirmarSenha) {
            e.preventDefault();
            alert('As senhas não coincidem');
            return;
        }

        const errosSenha = validarForcaSenha(senha);
        if (errosSenha.length > 0) {
            e.preventDefault();
            alert('Problemas com a senha:\n\n- ' + errosSenha.join('\n- '));
            return;
        }

        if (erros.length > 0) {
            e.preventDefault();

            // Rolagem até o primeiro erro
            const primeiroCampoInvalido = document.querySelector('.campo-invalido');
            if (primeiroCampoInvalido) {
                primeiroCampoInvalido.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            alert('Por favor, corrija os seguintes erros:\n\n- ' + erros.join('\n- '));
        }
    });
</script>

<?php
require_once __DIR__ . '/rodape.php';
?>