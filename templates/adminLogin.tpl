{include file="head.tpl"}
{* {include file="header.tpl"} *}


<section class="login_welcome">
    <h2>Admin</h2>

    {if $message neq null}
        <h3 class="error"> {$message} </h3>
    {/if}
</section>

<form action="admin-login" method="post" class="register_aside_form">
    <div class="login_container">
        <div class="register_field">
            <span>Nombre:</span>
            <input type="text" name="name" required>
        </div>
        <div class="register_field">
            <span>Clave:</span>
            <input type="text" name="password" required>
        </div>
        <div class="register_field">
            <button id="js-login" type="submit" class="register_btn">Entrar</button>
        </div>
    </div>
</form>

{include file="footer.tpl"}