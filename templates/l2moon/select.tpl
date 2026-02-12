{include file="header.tpl"}

			<h4><i class="bi bi-list-ul me-2" style="color: var(--moon-accent);"></i>{$vm.select_item}</h4>
			<p class="text-muted small">{$vm.select_desc}</p>

{if isset($error)}
			<div class="error">{$error}</div>
{/if}
{if isset($valid)}
			<div class="valid">{$valid}</div>
{/if}

			<ul class="menu mt-3">
{dynamic}{section name=i loop=$items}
				<li><a href="{$items[i].link}">{$items[i].name}</a></li>
{/section}{/dynamic}
			</ul>

			<hr>
			<div class="text-center">
				<input type="button" onClick="document.location='./{$session_id}'"
					   class="btn btn-moon-outline" value="{$vm.return}">
			</div>

{include file="footer.tpl"}
