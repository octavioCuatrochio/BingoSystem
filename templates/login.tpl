{include file="head.tpl"}
{* {include file="header.tpl"} *}


<section class="login_welcome">
    <h2>Por favor, inicie sesión</h2>

    {if $message neq null}
        <h3> {$message} </h3>
    {/if}
</section>

<form action="login" method="post" class="register_aside_form">
    <div class="login_container">
        <div class="register_field">
            <span>D.N.I:</span>
            <input type="text" name="dni" required>
        </div>
        <div class="register_field">
            <span>Teléfono:</span>
            <input type="text" name="phone_number" required>
        </div>
        <div class="register_field">
            <button id="js-login" type="submit" class="register_btn">Entrar</button>
        </div>
    </div>
</form>

{include file="footer.tpl"}