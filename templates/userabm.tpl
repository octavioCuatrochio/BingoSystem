{include file="head.tpl"}
{include file="header.tpl"}
{include file="asideMenu.tpl"}

{include file="asideAdmin.tpl"}


<div class="category_container">
    <h1> Editar usuarios </h1>
</div>


<article class="artworks_container">

    <table class="abm_table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Permisos de edición</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$usuarios item=usuario}
    
                <tr>
                    <td>
                        <h2>{$usuario->id}</h2>
                    </td>
                    <td>
                        <h3>{$usuario->nombre}</h3>
                    </td>
    
                    {* pregunto que permisos tiene, con 1 (admin) o 0 (registrado) *}
                    {if $usuario->admin_auth eq "1"}
                        <td class="checked">
                            <h4> Habilitados </h4>
                        </td>
                    {else}
                        <td>
                            <h4> Deshabilitados </h4>
                        </td>
                    {/if}

                    
    
                    {* Pregunto si el usuario que trae es igual al que esta en la sesion actualmente, y no le dejo editar *}
                    {if $usuario->nombre eq $sessionName}
                        <td class="abm_button register_btn" colspan="2">
                            <h4 class="error"> Usuario actual </h4>
                        </td>
        
                    {* es esencial para el sistema ya que este usuario se usa para linkear
                    los comentarios de usuarios eliminados *}
                    {elseif $usuario->id eq 0}
                        <td class="abm_button register_btn" colspan="2">
                            <h4 class="error"> Esencial para el sistema </h4>
                        </td>

                    {else}
                        <td class="abm_button register_btn">
                            <a href="useredit/{$usuario->id}">Editar</a>
                        </td>
                        <td class="abm_button register_btn">
                            <a href="userdelete/{$usuario->id}">Eliminar</a>
                        </td>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>

    {include file="footer.tpl"}