{include file="header.tpl"}

			<h4><i class="bi bi-shield-lock me-2" style="color: var(--moon-warning);"></i>{$vm.forgot_pwd}</h4>
			<p class="text-muted small">{$vm.forgot_pwd_text}</p>

			<form name="forgot" method="POST" action="./{$session_id}">
{if isset($error)}
				<div class="error">{$error}</div>
{/if}
				<div class="mb-3">
					<label for="Luser" class="form-label">{$vm.account}</label>
					<input type="text" class="form-control" id="Luser" name="Luser" value="{$vm.post_id}" autocomplete="off"
						   maxlength="{$vm.account_length}">
				</div>
				<div class="mb-3">
					<label for="Lemail" class="form-label">{$vm.email}</label>
					<input type="email" class="form-control" id="Lemail" name="Lemail" value="{$vm.post_email}" autocomplete="off">
				</div>
{if isset($image)}
				<div class="mb-3 captcha">
					<label class="form-label">Captcha</label>
					<div class="d-flex align-items-center gap-2">
						<img src="./img.php{$session_id}" id="L_image" onclick="reloadImage(this);" class="rounded">
						<input type="text" class="form-control" id="Limage" name="Limage" autocomplete="off"
							   placeholder="Code" style="max-width: 140px;">
					</div>
				</div>
{/if}
				<input type="hidden" name="action" value="forgot_pwd">
				<hr>
				<div class="d-flex gap-2">
					<input type="button" onClick="document.location='./{$session_id}'"
						   class="btn btn-moon-outline" value="{$vm.return}">
					<button type="submit" class="btn btn-moon flex-grow-1">
						<i class="bi bi-send me-1"></i>{$vm.forgot_button}
					</button>
				</div>
			</form>

{include file="footer.tpl"}
