{include file="header.tpl"}

			<div class="mb-3 small" style="max-height: 400px; overflow-y: auto; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 10px; border: 1px solid var(--moon-border);">
				{$vm.terms_and_condition}
			</div>

			<form name="create" method="POST" action="./{$session_id}">
				<input type="hidden" name="action" value="show_create">
				<input type="hidden" name="ack" value="ack">
				<hr>
				<div class="d-flex gap-2">
					<input type="button" onClick="document.location='./{$session_id}'"
						   class="btn btn-moon-outline" value="{$vm.return}">
					<button type="submit" class="btn btn-moon flex-grow-1">
						<i class="bi bi-check-circle me-1"></i>{$vm.accept_button}
					</button>
				</div>
			</form>

{include file="footer.tpl"}
