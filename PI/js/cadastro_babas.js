document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formCadastroBaba");

    const cpf = document.getElementById("cpf");
    const telefone = document.getElementById("telefone");
    const cep = document.getElementById("cep");

    const estado = document.getElementById("estado");
    const cidade = document.getElementById("cidade");
    const bairro = document.getElementById("bairro");
    const rua = document.getElementById("rua");

    const dataNascimento =
        document.getElementById("data_nascimento");

    const senha =
        document.getElementById("senha");

    const confirmarSenha =
        document.getElementById("confirmar_senha");

    const foto =
        document.getElementById("foto");


    /* ========================================
       MÁSCARA CPF
    ======================================== */

    cpf.addEventListener("input", function () {

        let valor = cpf.value.replace(/\D/g, "");

        valor = valor.substring(0, 11);

        if (valor.length > 9) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{3})(\d{3})(\d{0,2})$/,
                    "$1.$2.$3-$4"
                );

        } else if (valor.length > 6) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{3})(\d{0,3})$/,
                    "$1.$2.$3"
                );

        } else if (valor.length > 3) {

            valor =
                valor.replace(
                    /^(\d{3})(\d{0,3})$/,
                    "$1.$2"
                );

        }

        cpf.value = valor;

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
                    /^(\d{2})(\d{5})(\d{0,4})$/,
                    "($1) $2-$3"
                );

        } else if (valor.length > 6) {

            valor =
                valor.replace(
                    /^(\d{2})(\d{4})(\d{0,4})$/,
                    "($1) $2-$3"
                );

        } else if (valor.length > 2) {

            valor =
                valor.replace(
                    /^(\d{2})(\d{0,5})$/,
                    "($1) $2"
                );

        } else if (valor.length > 0) {

            valor =
                valor.replace(
                    /^(\d{0,2})$/,
                    "($1"
                );

        }

        telefone.value = valor;

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
                    /^(\d{5})(\d{0,3})$/,
                    "$1-$2"
                );

        }

        cep.value = valor;


        // Quando completar o CEP,
        // consulta o ViaCEP.

        if (valor.replace(/\D/g, "").length === 8) {

            buscarCEP();

        }

    });


    /* ========================================
       BUSCAR CEP
    ======================================== */

    function buscarCEP() {

        const numeroCEP =
            cep.value.replace(/\D/g, "");


        if (numeroCEP.length !== 8) {

            return;

        }


        // Limpa os campos antes da consulta

        cidade.value = "";
        bairro.value = "";
        rua.value = "";


        fetch(
            "https://viacep.com.br/ws/" +
            numeroCEP +
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


                removerErro(cep);


                // Rua

                rua.value =
                    dados.logradouro || "";


                // Bairro

                bairro.value =
                    dados.bairro || "";


                // Cidade

                cidade.value =
                    dados.localidade || "";


                // Estado

                estado.value =
                    dados.uf || "";


                // Caso o estado retornado
                // não esteja no select

                if (
                    estado.value !== dados.uf
                ) {

                    estado.value = "";

                }

            })

            .catch(function () {

                mostrarErro(
                    cep,
                    "Não foi possível consultar o CEP."
                );

            });

    }


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


        let soma = 0;


        // Primeiro dígito

        for (let i = 0; i < 9; i++) {

            soma +=
                Number(numero[i]) *
                (10 - i);

        }


        let resto =
            soma % 11;


        let primeiroDigito =
            resto < 2
                ? 0
                : 11 - resto;


        if (
            primeiroDigito !==
            Number(numero[9])
        ) {

            return false;

        }


        // Segundo dígito

        soma = 0;


        for (let i = 0; i < 10; i++) {

            soma +=
                Number(numero[i]) *
                (11 - i);

        }


        resto =
            soma % 11;


        let segundoDigito =
            resto < 2
                ? 0
                : 11 - resto;


        if (
            segundoDigito !==
            Number(numero[10])
        ) {

            return false;

        }


        return true;

    }


    /* ========================================
       VALIDAR TELEFONE
    ======================================== */

    function validarTelefone(valor) {

        const numero =
            valor.replace(/\D/g, "");


        // Aceita telefone fixo
        // com 10 números

        if (numero.length === 10) {

            return true;

        }


        // Aceita celular
        // com 11 números

        if (numero.length === 11) {

            return true;

        }


        return false;

    }


    /* ========================================
       VALIDAR IDADE
    ======================================== */

    function validarIdade(valor) {

        if (!valor) {

            return false;

        }


        const nascimento =
            new Date(valor);


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


        return idade >= 18;

    }


    /* ========================================
       VALIDAR FOTO
    ======================================== */

    function validarFoto() {

        if (!foto.files.length) {

            return false;

        }


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

            return false;

        }


        // 5 MB

        if (
            arquivo.size >
            5 * 1024 * 1024
        ) {

            return false;

        }


        return true;

    }


    /* ========================================
       MOSTRAR ERRO
    ======================================== */

    function mostrarErro(
        elemento,
        mensagem
    ) {

        elemento.classList.add(
            "campo-erro"
        );


        let mensagemExistente =
            elemento.parentElement
                .querySelector(
                    ".mensagem-campo"
                );


        if (!mensagemExistente) {

            mensagemExistente =
                document.createElement(
                    "span"
                );

            mensagemExistente.className =
                "mensagem-campo";


            elemento.parentElement
                .appendChild(
                    mensagemExistente
                );

        }


        mensagemExistente.textContent =
            mensagem;

    }


    /* ========================================
       REMOVER ERRO
    ======================================== */

    function removerErro(elemento) {

        elemento.classList.remove(
            "campo-erro"
        );


        const mensagem =
            elemento.parentElement
                .querySelector(
                    ".mensagem-campo"
                );


        if (mensagem) {

            mensagem.remove();

        }

    }


    /* ========================================
       LIMPAR ERRO QUANDO DIGITAR
    ======================================== */

    cpf.addEventListener("input", function () {

        removerErro(cpf);

    });


    telefone.addEventListener("input", function () {

        removerErro(telefone);

    });


    dataNascimento.addEventListener(
        "change",
        function () {

            removerErro(dataNascimento);

        }
    );


    senha.addEventListener("input", function () {

        removerErro(senha);

    });


    confirmarSenha.addEventListener(
        "input",
        function () {

            removerErro(confirmarSenha);

        }
    );


    foto.addEventListener("change", function () {

        removerErro(foto);

    });


    cep.addEventListener("input", function () {

        removerErro(cep);

    });

    /* ========================================
   SEGURANÇA DA SENHA
======================================== */

    const requisitosSenha =
        document.getElementById("requisitosSenha");


    senha.addEventListener("input", function () {

        const valor = senha.value;

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

            let mensagem =
                "A senha precisa ter ";

            if (!temTamanho) {

                mensagem +=
                    "no mínimo 6 caracteres";

            }

            if (
                !temTamanho &&
                !temEspecial
            ) {

                mensagem +=
                    " e ";

            }

            if (!temEspecial) {

                mensagem +=
                    "1 caractere especial";

            }

            mensagem += ".";

            requisitosSenha.textContent =
                mensagem;

            requisitosSenha.style.color =
                "#e53935";

        }

    });


    /* ========================================
       VALIDAR FORMULÁRIO
    ======================================== */

    form.addEventListener(
        "submit",
        function (event) {

            let formularioValido = true;


            /* CPF */

            if (!validarCPF(cpf.value)) {

                mostrarErro(
                    cpf,
                    "Digite um CPF válido."
                );

                formularioValido = false;

            }


            /* TELEFONE */

            if (
                !validarTelefone(
                    telefone.value
                )
            ) {

                mostrarErro(
                    telefone,
                    "Digite um telefone válido."
                );

                formularioValido = false;

            }


            /* IDADE */

            if (
                !validarIdade(
                    dataNascimento.value
                )
            ) {

                mostrarErro(
                    dataNascimento,
                    "É necessário ter pelo menos 18 anos."
                );

                formularioValido = false;

            }


            /* SENHAS */

            if (
                senha.value !==
                confirmarSenha.value
            ) {

                mostrarErro(
                    confirmarSenha,
                    "As senhas não coincidem."
                );

                formularioValido = false;

            }

            const temTamanho =
                senha.value.length >= 6;

            const temEspecial =
                /[^A-Za-z0-9]/.test(senha.value);


            if (!temTamanho || !temEspecial) {

                mostrarErro(
                    senha,
                    "A senha deve ter no mínimo 6 caracteres e pelo menos 1 caractere especial."
                );

                formularioValido = false;

            }


            /* FOTO */

            if (!validarFoto()) {

                mostrarErro(
                    foto,
                    "Escolha uma imagem JPG, PNG ou WEBP de até 5 MB."
                );

                formularioValido = false;

            }


            /* CEP */

            const numeroCEP =
                cep.value.replace(/\D/g, "");


            if (numeroCEP.length !== 8) {

                mostrarErro(
                    cep,
                    "Digite um CEP válido."
                );

                formularioValido = false;

            }


            /* ESTADO */

            if (!estado.value) {

                mostrarErro(
                    estado,
                    "Selecione o estado."
                );

                formularioValido = false;

            }


            /* CIDADE */

            if (!cidade.value.trim()) {

                mostrarErro(
                    cidade,
                    "Digite a cidade."
                );

                formularioValido = false;

            }


            /* BAIRRO */

            if (!bairro.value.trim()) {

                mostrarErro(
                    bairro,
                    "Digite o bairro."
                );

                formularioValido = false;

            }


            /* RUA */

            if (!rua.value.trim()) {

                mostrarErro(
                    rua,
                    "Digite a rua."
                );

                formularioValido = false;

            }


            /* RESULTADO */

            if (!formularioValido) {

                event.preventDefault();


                // Vai para o primeiro erro

                const primeiroErro =
                    form.querySelector(
                        ".campo-erro"
                    );


                if (primeiroErro) {

                    primeiroErro.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });

                }

            }

        }
    );
});