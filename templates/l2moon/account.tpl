{include file="header.tpl"}

			<h4><i class="bi bi-gear-fill me-2" style="color: var(--moon-accent);"></i>{$vm.title_page}</h4>
			<p class="text-muted small">{$vm.account_text}</p>

{if isset($error)}
			<div class="error">{$error}</div>
{/if}
{if isset($valid)}
			<div class="valid">{$valid}</div>
{/if}

			<ul class="menu mt-3">
{dynamic}{section name=i loop=$modules}
				<li><a href="{$modules[i].link}">{$modules[i].name}</a></li>
{/section}{/dynamic}
			</ul>

{include file="footer.tpl"}
