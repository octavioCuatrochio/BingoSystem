{include file="head.tpl"}
{include file="header.tpl"}
{include file="asideMenu.tpl"}

{if $sesion neq null}
    {if $sesion eq 1}
        {include file="asideAdmin.tpl"}
    {else}
        {include file="asideUsuario.tpl"}
    {/if}
{else}
    {include file="asideRegistro.tpl"}
{/if}

<h1> Editar usuario </h1>

<div class="abm_edit_container">

    <form action="addediteduser/{$usuario->id}" method="post">
        <div class="abm_edit_row">

            <div class="input_block">
                <label>Nombre:</label>
                <input name="nombre" type="text" value="{$usuario->nombre}">
            </div>
            <div class="input_block">
                <label>Permisos:</label>
                <select name="admin_auth">
                    {if $usuario->admin_auth eq "1"}
                        <option selected="selected" value="1">Actual: Activado </option>
                        <option value="0">Desactivar</option>
                    {else}
                        <option selected="selected" value="0"> Actual: Desactivado </option>
                        <option value="1">Activar</option>
                    {/if}
                </select>
            </div>
        </div>

        <button class="register_btn_nomargin" type="submit">Terminar edición</button>
    </form>
</div>

{include file="footer.tpl"}