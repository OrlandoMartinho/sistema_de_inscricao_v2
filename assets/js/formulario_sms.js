

//validar o formulário de sms
//---------------------------

function validarFormulario()
{

    let nome =document.forms["form"]["nome"].value;
    let email =document.forms["form"]["email"].value;
    let assunto =document.forms["form"]["assunto"].value;
    let sms =document.forms["form"]["mensagem"].value;
    let erros=[];

    if(nome=="")
    {
        erros.push("O campo nome deve ser  preenchido");
    }else 
    if(email=="")
        {
        erros.push("O campo email deve ser  preenchido");
    }
    else
    {
        //validação simples do email
        let validarEmail="/^[^\s@]+@[^\s@]+\.[^\s@]+$";
        if(!validarEmail.test(email))
            {
            erros.push("O email deve ser um email valido");
        }
    } 
        if(assunto==""){
            erros.push("O campo assunto deve ser  preenchido");
    }

    if(sms==""){
        erros.push("O campo mensagem deve ser  preenchido");
    }else if(nome=="" && email=="" && assunto=="" && sms==""){
        erros.push("preencha todos os campos");
    }

    if(erros.length>0){
        alert(erros.join("\n"));
        //vai impedir o envio do formulario
        return false;
    }
    return true;


}