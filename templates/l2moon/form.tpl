{include file="header.tpl"}

			<form name="login" method="POST" action="./{$session_id}">
				<h4 class="mb-3"><i class="bi bi-person-circle me-2" style="color: var(--moon-accent);"></i>{$vm.exist_account}</h4>
{if isset($error)}
				<div class="error">{$error}</div>
{/if}
{if isset($valid)}
				<div class="valid">{$valid}</div>
{/if}
				<div class="mb-3">
					<label for="Luser" class="form-label">{$vm.account}</label>
					<input type="text" class="form-control" id="Luser" name="Luser" autocomplete="off"
						   maxlength="{$vm.account_length}" placeholder="{$vm.account}">
				</div>
				<div class="mb-3">
					<label for="Lpwd" class="form-label">{$vm.password}</label>
					<input type="password" class="form-control" id="Lpwd" name="Lpwd" autocomplete="off"
						   maxlength="{$vm.password_length}" placeholder="{$vm.password}">
				</div>
				<div class="mb-2">
					<a href="?{$session_id}&action=show_forget" class="small">
						<i class="bi bi-question-circle me-1"></i>{$vm.forgot_password}
					</a>
				</div>
{if isset($image)}
				<div class="mb-3 captcha">
					<label for="Limage" class="form-label">Captcha</label>
					<div class="d-flex align-items-center gap-2">
						<img src="./img.php{$session_id}" id="L_image" onclick="reloadImage(this);" class="rounded">
						<input class="form-control" type="text" id="Limage" name="Limage" autocomplete="off"
							   placeholder="Code" style="max-width: 140px;">
					</div>
				</div>
{/if}
				<hr>
				<input type="hidden" name="action" value="login">
				<div class="text-center">
					<button type="submit" class="btn btn-moon w-100">
						<i class="bi bi-box-arrow-in-right me-1"></i>{$vm.login_button}
					</button>
				</div>
			</form>

			<hr>
			<h4 class="mb-3"><i class="bi bi-person-plus me-2" style="color: var(--moon-gold);"></i>{$vm.new_account}</h4>
			<p class="text-muted small">{$vm.new_account_text}</p>
			<div class="text-center">
				<input type="button" onClick="document.location='./?{$session_id}&action=show_create'"
					   class="btn btn-moon-gold w-100" value="{$vm.create_button}">
			</div>

{include file="footer.tpl"}
