document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");

    if (!form) {
        return;
    }


    // ==============================
    // CAMPOS
    // ==============================

    const cpf = form.querySelector('input[name="cpf"]');

    const telefone = form.querySelector('input[name="telefone"]');

    const senha = form.querySelector('input[name="senha"]');

    const confirmarSenha =
        form.querySelector('input[name="confirmar_senha"]');

    const dataNascimento =
        form.querySelector('input[name="data_nascimento"]');

    const foto = form.querySelector('input[name="foto"]');


    // ==============================
    // CPF
    // ==============================

    if (cpf) {

        cpf.addEventListener("input", function () {

            let valor = cpf.value.replace(/\D/g, "");

            valor = valor.substring(0, 11);

            if (valor.length > 9) {

                valor =
                    valor.replace(
                        /(\d{3})(\d{3})(\d{3})(\d{1,2})/,
                        "$1.$2.$3-$4"
                    );

            } else if (valor.length > 6) {

                valor =
                    valor.replace(
                        /(\d{3})(\d{3})(\d{1,3})/,
                        "$1.$2.$3"
                    );

            } else if (valor.length > 3) {

                valor =
                    valor.replace(
                        /(\d{3})(\d{1,3})/,
                        "$1.$2"
                    );
            }

            cpf.value = valor;

            limparErro(cpf);

        });

    }


    // ==============================
    // TELEFONE
    // ==============================

    if (telefone) {

        telefone.addEventListener("input", function () {

            let valor =
                telefone.value.replace(/\D/g, "");

            valor = valor.substring(0, 11);

            if (valor.length > 10) {

                valor =
                    valor.replace(
                        /(\d{2})(\d{5})(\d{1,4})/,
                        "($1) $2-$3"
                    );

            } else if (valor.length > 6) {

                valor =
                    valor.replace(
                        /(\d{2})(\d{4})(\d{1,4})/,
                        "($1) $2-$3"
                    );

            } else if (valor.length > 2) {

                valor =
                    valor.replace(
                        /(\d{2})(\d{1,5})/,
                        "($1) $2"
                    );

            }

            telefone.value = valor;

            limparErro(telefone);

        });

    }


    // ==============================
    // SENHAS
    // ==============================

    if (senha) {

        senha.addEventListener("input", function () {

            limparErro(senha);

            if (confirmarSenha && confirmarSenha.value !== "") {

                verificarSenhas();

            }

        });

    }


    if (confirmarSenha) {

        confirmarSenha.addEventListener("input", function () {

            verificarSenhas();

        });

    }


    function verificarSenhas() {

        if (!senha || !confirmarSenha) {
            return true;
        }

        if (confirmarSenha.value === "") {
            return true;
        }

        if (senha.value !== confirmarSenha.value) {

            mostrarErro(
                confirmarSenha,
                "As senhas não são iguais."
            );

            return false;

        }

        limparErro(confirmarSenha);

        return true;
    }


    // ==============================
    // DATA DE NASCIMENTO
    // ==============================

    if (dataNascimento) {

        dataNascimento.addEventListener("change", function () {

            validarIdade(dataNascimento);

        });

    }


    function validarIdade(campo) {

        if (!campo.value) {
            return false;
        }

        const nascimento =
            new Date(campo.value + "T00:00:00");

        const hoje = new Date();

        let idade =
            hoje.getFullYear() -
            nascimento.getFullYear();

        const mes =
            hoje.getMonth() -
            nascimento.getMonth();

        if (
            mes < 0 ||
            (mes === 0 &&
                hoje.getDate() < nascimento.getDate())
        ) {

            idade--;

        }

        // Idade mínima
        const idadeMinima = 18;

        if (idade < idadeMinima) {

            mostrarErro(
                campo,
                "Você precisa ter pelo menos " +
                idadeMinima +
                " anos."
            );

            return false;

        }

        limparErro(campo);

        return true;
    }


    // ==============================
    // FOTO
    // ==============================

    if (foto) {

        foto.addEventListener("change", function () {

            validarFoto();

        });

    }


    function validarFoto() {

        if (!foto || foto.files.length === 0) {

            mostrarErro(
                foto,
                "Selecione uma foto de perfil."
            );

            return false;

        }

        const arquivo = foto.files[0];

        const extensoesPermitidas = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        if (
            !extensoesPermitidas.includes(
                arquivo.type
            )
        ) {

            mostrarErro(
                foto,
                "A foto deve estar em JPG, JPEG, PNG ou WEBP."
            );

            return false;

        }

        // Limite de 5 MB
        const tamanhoMaximo =
            5 * 1024 * 1024;

        if (arquivo.size > tamanhoMaximo) {

            mostrarErro(
                foto,
                "A foto deve ter no máximo 5 MB."
            );

            return false;

        }

        limparErro(foto);

        return true;
    }


    // ==============================
    // CPF VÁLIDO
    // ==============================

    function validarCPF(valor) {

        const cpfNumeros = valor.replace(/\D/g, "");

        if (cpfNumeros.length !== 11) {
            return false;
        }

        // Não aceita CPF com todos os números iguais
        if (/^(\d)\1{10}$/.test(cpfNumeros)) {
            return false;
        }

        // Primeiro dígito
        let soma = 0;

        for (let i = 0; i < 9; i++) {
            soma += Number(cpfNumeros[i]) * (10 - i);
        }

        let resto = soma % 11;

        let primeiroDigito;

        if (resto < 2) {
            primeiroDigito = 0;
        } else {
            primeiroDigito = 11 - resto;
        }

        if (primeiroDigito !== Number(cpfNumeros[9])) {
            return false;
        }


        // Segundo dígito
        soma = 0;

        for (let i = 0; i < 10; i++) {
            soma += Number(cpfNumeros[i]) * (11 - i);
        }

        resto = soma % 11;

        let segundoDigito;

        if (resto < 2) {
            segundoDigito = 0;
        } else {
            segundoDigito = 11 - resto;
        }

        if (segundoDigito !== Number(cpfNumeros[10])) {
            return false;
        }


        return true;
    }


    // ==============================
    // TELEFONE VÁLIDO
    // ==============================

    function validarTelefone(valor) {

        const numeros =
            valor.replace(/\D/g, "");

        // Telefone celular com DDD
        if (numeros.length !== 11) {
            return false;
        }

        // Celular começa com 9
        if (numeros.charAt(2) !== "9") {
            return false;
        }

        return true;
    }


    // ==============================
    // CAMPOS OBRIGATÓRIOS
    // ==============================

    function validarObrigatorios() {

        let valido = true;

        const obrigatorios =
            form.querySelectorAll("[required]");

        obrigatorios.forEach(function (campo) {

            // Checkbox
            if (campo.type === "checkbox") {

                if (!campo.checked) {

                    mostrarErro(
                        campo,
                        "Este campo é obrigatório."
                    );

                    valido = false;

                }

                return;
            }


            // Arquivo
            if (campo.type === "file") {

                if (campo.files.length === 0) {

                    mostrarErro(
                        campo,
                        "Este campo é obrigatório."
                    );

                    valido = false;

                }

                return;
            }


            // Campos normais
            if (campo.value.trim() === "") {

                mostrarErro(
                    campo,
                    "Este campo é obrigatório."
                );

                valido = false;

            }

        });

        return valido;
    }


    // ==============================
    // MOSTRAR ERRO
    // ==============================

    function mostrarErro(campo, mensagem) {

        if (!campo) {
            return;
        }

        campo.classList.add("campo-erro");


        let mensagemErro =
            campo.parentElement.querySelector(
                ".mensagem-campo"
            );


        if (!mensagemErro) {

            mensagemErro =
                document.createElement("small");

            mensagemErro.className =
                "mensagem-campo";

            campo.parentElement.appendChild(
                mensagemErro
            );

        }


        mensagemErro.textContent = mensagem;

    }


    // ==============================
    // LIMPAR ERRO
    // ==============================

    function limparErro(campo) {

        if (!campo) {
            return;
        }

        campo.classList.remove("campo-erro");

        const mensagemErro =
            campo.parentElement.querySelector(
                ".mensagem-campo"
            );

        if (mensagemErro) {

            mensagemErro.remove();

        }

    }


    // ==============================
    // ENVIO DO FORMULÁRIO
    // ==============================

    form.addEventListener("submit", function (evento) {

        let valido = true;


        // Obrigatórios
        if (!validarObrigatorios()) {

            valido = false;

        }


        // CPF
        if (cpf && !validarCPF(cpf.value)) {

            mostrarErro(
                cpf,
                "Digite um CPF válido."
            );

            valido = false;

        }


        // Telefone
        if (
            telefone &&
            !validarTelefone(telefone.value)
        ) {

            mostrarErro(
                telefone,
                "Digite um telefone válido."
            );

            valido = false;

        }


        // Senhas
        if (!verificarSenhas()) {

            valido = false;

        }


        // Idade
        if (
            dataNascimento &&
            !validarIdade(dataNascimento)
        ) {

            valido = false;

        }


        // Foto
        if (
            foto &&
            !validarFoto()
        ) {

            valido = false;

        }


        // Impede envio
        if (!valido) {

            evento.preventDefault();

            const primeiroErro =
                form.querySelector(".campo-erro");

            if (primeiroErro) {

                primeiroErro.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                primeiroErro.focus();

            }

        }

    });


});