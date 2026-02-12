{include file="header.tpl"}

			<h4><i class="bi bi-envelope-fill me-2" style="color: var(--moon-accent);"></i>{$vm.chg_pwd}</h4>
			<p class="text-muted small">{$vm.chg_pwd_text}</p>

			<form name="create" method="POST" action="./{$session_id}">
{if isset($error)}
				<div class="error">{$error}</div>
{/if}
				<div class="mb-3">
					<label for="Lpwd" class="form-label">{$vm.password}</label>
					<input type="password" class="form-control" id="Lpwd" name="Lpwd" autocomplete="off"
						   maxlength="{$vm.password_length}">
				</div>
				<div class="mb-3">
					<label for="Lemail" class="form-label">{$vm.email}</label>
					<input type="email" class="form-control" id="Lemail" name="Lemail" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="Lemail2" class="form-label">{$vm.email2}</label>
					<input type="email" class="form-control" id="Lemail2" name="Lemail2" autocomplete="off">
				</div>
				<input type="hidden" name="action" value="chg_email_form">
				<hr>
				<div class="d-flex gap-2">
					<input type="button" onClick="document.location='./{$session_id}'"
						   class="btn btn-moon-outline" value="{$vm.return}">
					<button type="submit" class="btn btn-moon flex-grow-1">
						<i class="bi bi-check-lg me-1"></i>{$vm.chg_button}
					</button>
				</div>
			</form>

{include file="footer.tpl"}
