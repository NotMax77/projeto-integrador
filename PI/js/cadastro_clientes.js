document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.getElementById("formCadastroCliente");

    const cpf = document.getElementById("cpf");
    const telefone = document.getElementById("telefone");
    const cep = document.getElementById("cep");

    const estado = document.getElementById("estado");
    const cidade = document.getElementById("cidade");
    const bairro = document.getElementById("bairro");
    const rua = document.getElementById("rua");

    const senha = document.getElementById("senha");
    const confirmarSenha = document.getElementById("confirmar_senha");

    const requisitosSenha =
        document.getElementById("requisitosSenha");


    /* ========================================
       FUNÇÕES DE ERRO
    ======================================== */

    function mostrarErro(campo, mensagem) {

        campo.classList.add("campo-erro");

        let mensagemErro =
            campo.parentElement.querySelector(".mensagem-campo");

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


    function removerErro(campo) {

        campo.classList.remove("campo-erro");

        const mensagemErro =
            campo.parentElement.querySelector(
                ".mensagem-campo"
            );

        if (mensagemErro) {
            mensagemErro.remove();
        }
    }


    /* ========================================
       MÁSCARA CPF
    ======================================== */

    cpf.addEventListener("input", function () {

        let valor =
            cpf.value.replace(/\D/g, "");

        valor = valor.substring(0, 11);

        if (valor.length > 9) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{3})(\d{3})(\d{1,2})$/,
                    "$1.$2.$3-$4"
                );

        } else if (valor.length > 6) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{3})(\d{1,3})$/,
                    "$1.$2.$3"
                );

        } else if (valor.length > 3) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{1,3})$/,
                    "$1.$2"
                );
        }

        cpf.value = valor;

        removerErro(cpf);
    });


    /* ========================================
       MÁSCARA TELEFONE
    ======================================== */

    telefone.addEventListener("input", function () {

        let valor =
            telefone.value.replace(/\D/g, "");

        valor = valor.substring(0, 11);

        if (valor.length > 10) {

            valor =
                valor.replace(
                    /^(\d{2})(\d{5})(\d{1,4})$/,
                    "($1) $2-$3"
                );

        } else if (valor.length > 6) {

            valor =
                valor.replace(
                    /^(\d{2})(\d{4,5})(\d{1,4})$/,
                    "($1) $2-$3"
                );

        } else if (valor.length > 2) {

            valor =
                valor.replace(
                    /^(\d{2})(\d{1,5})$/,
                    "($1) $2"
                );
        }

        telefone.value = valor;

        removerErro(telefone);
    });


    /* ========================================
       MÁSCARA CEP
    ======================================== */

    cep.addEventListener("input", function () {

        let valor =
            cep.value.replace(/\D/g, "");

        valor = valor.substring(0, 8);

        if (valor.length > 5) {

            valor =
                valor.replace(
                    /^(\d{5})(\d{1,3})$/,
                    "$1-$2"
                );
        }

        cep.value = valor;

        removerErro(cep);

    });


    /* ========================================
       BUSCAR CEP - VIACEP
    ======================================== */

    cep.addEventListener("blur", function () {

        const cepNumeros =
            cep.value.replace(/\D/g, "");

        if (cepNumeros.length !== 8) {

            mostrarErro(
                cep,
                "Digite um CEP válido."
            );

            return;
        }


        cidade.value = "";
        bairro.value = "";
        rua.value = "";
        estado.value = "";


        fetch(
            "https://viacep.com.br/ws/" +
            cepNumeros +
            "/json/"
        )

        .then(function (resposta) {

            if (!resposta.ok) {

                throw new Error(
                    "Erro ao consultar o CEP."
                );
            }

            return resposta.json();

        })

        .then(function (dados) {

            if (dados.erro) {

                mostrarErro(
                    cep,
                    "CEP não encontrado."
                );

                return;
            }


            rua.value =
                dados.logradouro || "";

            bairro.value =
                dados.bairro || "";

            cidade.value =
                dados.localidade || "";

            estado.value =
                dados.uf || "";


            removerErro(cep);

        })

        .catch(function () {

            mostrarErro(
                cep,
                "Não foi possível consultar o CEP."
            );

        });

    });


    /* ========================================
       VALIDAR CPF
    ======================================== */

    function validarCPF(valor) {

        const numero =
            valor.replace(/\D/g, "");


        if (numero.length !== 11) {
            return false;
        }


        // Impede números repetidos

        if (/^(\d)\1{10}$/.test(numero)) {
            return false;
        }


        // Primeiro dígito

        let soma = 0;

        for (let i = 0; i < 9; i++) {

            soma +=
                parseInt(numero.charAt(i)) *
                (10 - i);

        }

        let resto =
            soma % 11;

        let digito1 =
            resto < 2 ? 0 : 11 - resto;


        if (
            digito1 !==
            parseInt(numero.charAt(9))
        ) {

            return false;
        }


        // Segundo dígito

        soma = 0;

        for (let i = 0; i < 10; i++) {

            soma +=
                parseInt(numero.charAt(i)) *
                (11 - i);

        }

        resto =
            soma % 11;

        let digito2 =
            resto < 2 ? 0 : 11 - resto;


        if (
            digito2 !==
            parseInt(numero.charAt(10))
        ) {

            return false;
        }


        return true;
    }


    /* ========================================
       VALIDAR CPF AO SAIR DO CAMPO
    ======================================== */

    cpf.addEventListener("blur", function () {

        if (!validarCPF(cpf.value)) {

            mostrarErro(
                cpf,
                "CPF inválido."
            );

        } else {

            removerErro(cpf);

        }

    });


    /* ========================================
       SEGURANÇA DA SENHA
    ======================================== */

    senha.addEventListener("input", function () {

        const valor =
            senha.value;

        const temTamanho =
            valor.length >= 6;

        const temEspecial =
            /[^A-Za-z0-9]/.test(valor);


        if (
            temTamanho &&
            temEspecial
        ) {

            requisitosSenha.textContent =
                "Senha válida ✓";

            requisitosSenha.style.color =
                "green";

            removerErro(senha);

        } else {

            requisitosSenha.textContent =
                "Mínimo de 6 caracteres e pelo menos 1 caractere especial.";

            requisitosSenha.style.color =
                "#e53935";

        }

        validarConfirmacaoSenha();

    });


    /* ========================================
       CONFIRMAR SENHA
    ======================================== */

    function validarConfirmacaoSenha() {

        if (
            confirmarSenha.value === ""
        ) {
            return true;
        }


        if (
            senha.value !==
            confirmarSenha.value
        ) {

            mostrarErro(
                confirmarSenha,
                "As senhas não coincidem."
            );

            return false;

        } else {

            removerErro(confirmarSenha);

            return true;
        }
    }


    confirmarSenha.addEventListener(
        "input",
        validarConfirmacaoSenha
    );


    /* ========================================
       DATA DE NASCIMENTO
    ======================================== */

    const dataNascimento =
        document.getElementById(
            "data_nascimento"
        );


    dataNascimento.addEventListener(
        "change",
        function () {

            if (!dataNascimento.value) {
                return;
            }


            const nascimento =
                new Date(
                    dataNascimento.value +
                    "T00:00:00"
                );

            const hoje =
                new Date();


            let idade =
                hoje.getFullYear() -
                nascimento.getFullYear();


            const mes =
                hoje.getMonth() -
                nascimento.getMonth();


            if (
                mes < 0 ||
                (
                    mes === 0 &&
                    hoje.getDate() <
                    nascimento.getDate()
                )
            ) {

                idade--;
            }


            if (idade < 18) {

                mostrarErro(
                    dataNascimento,
                    "É necessário ter pelo menos 18 anos."
                );

            } else {

                removerErro(dataNascimento);

            }

        }
    );


    /* ========================================
       ENVIO DO FORMULÁRIO
    ======================================== */

    formulario.addEventListener(
        "submit",
        function (event) {

            let formularioValido = true;


            /* CPF */

            if (!validarCPF(cpf.value)) {

                mostrarErro(
                    cpf,
                    "CPF inválido."
                );

                formularioValido = false;

            }


            /* TELEFONE */

            const telefoneNumeros =
                telefone.value.replace(
                    /\D/g,
                    ""
                );


            if (
                telefoneNumeros.length !==
                10 &&
                telefoneNumeros.length !==
                11
            ) {

                mostrarErro(
                    telefone,
                    "Digite um telefone válido."
                );

                formularioValido = false;

            }


            /* SENHA */

            const temTamanho =
                senha.value.length >= 6;

            const temEspecial =
                /[^A-Za-z0-9]/.test(
                    senha.value
                );


            if (
                !temTamanho ||
                !temEspecial
            ) {

                mostrarErro(
                    senha,
                    "A senha deve ter no mínimo 6 caracteres e 1 caractere especial."
                );

                formularioValido = false;

            }


            /* CONFIRMAÇÃO */

            if (
                !validarConfirmacaoSenha()
            ) {

                formularioValido = false;

            }


            /* IDADE */

            if (!dataNascimento.value) {

                mostrarErro(
                    dataNascimento,
                    "Informe sua data de nascimento."
                );

                formularioValido = false;

            } else {

                const nascimento =
                    new Date(
                        dataNascimento.value +
                        "T00:00:00"
                    );

                const hoje =
                    new Date();


                let idade =
                    hoje.getFullYear() -
                    nascimento.getFullYear();


                const mes =
                    hoje.getMonth() -
                    nascimento.getMonth();


                if (
                    mes < 0 ||
                    (
                        mes === 0 &&
                        hoje.getDate() <
                        nascimento.getDate()
                    )
                ) {

                    idade--;

                }


                if (idade < 18) {

                    mostrarErro(
                        dataNascimento,
                        "É necessário ter pelo menos 18 anos."
                    );

                    formularioValido = false;

                }

            }


            /* FOTO */

            const foto =
                document.getElementById("foto");


            if (
                !foto.files ||
                foto.files.length === 0
            ) {

                mostrarErro(
                    foto,
                    "Selecione uma foto de perfil."
                );

                formularioValido = false;

            } else {

                const arquivo =
                    foto.files[0];

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
                        "Use uma imagem JPG, PNG ou WEBP."
                    );

                    formularioValido = false;

                }


                if (
                    arquivo.size >
                    5 * 1024 * 1024
                ) {

                    mostrarErro(
                        foto,
                        "A foto deve ter no máximo 5 MB."
                    );

                    formularioValido = false;

                }

            }


            /* CEP */

            const cepNumeros =
                cep.value.replace(
                    /\D/g,
                    ""
                );


            if (
                cepNumeros.length !== 8
            ) {

                mostrarErro(
                    cep,
                    "Digite um CEP válido."
                );

                formularioValido = false;

            }


            /* RESULTADO */

            if (!formularioValido) {

                event.preventDefault();

            }

        }
    );

});