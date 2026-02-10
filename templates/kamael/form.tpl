{include file="header.tpl"}

			<form name="login" method="POST" action="./{$session_id}">
				<h2>{$vm.exist_account}</h2>
{if isset($error)}
				<div class="error"><div class="error_container">{$error}</div></div>
{/if}
{if isset($valid)}
				<div class="valid"><div class="valid_container">{$valid}</div></div>
{/if}
				<div class="mb-3">
				<label for="Luser" class="form-label">{$vm.account}</label>
				<input type="text" class="form-control" id="Luser" name="Luser" autocomplete="off" maxlength="{$vm.account_length}" />
				</div>
				<div class="mb-3"><label for="Lpwd" class="form-label">{$vm.password}</label>
				<input type="password" class="form-control" id="Lpwd" name="Lpwd" autocomplete="off" maxlength="{$vm.password_length}" /></div>
								<a href="?{$session_id}&action=show_forget">{$vm.forgot_password}</a>

{if isset($image)}
				<div class="mb-3 captcha">
				<label for="Limage" class="form-label">Captcha</label>
				<div class="d-flex align-items-center"><label class="form-label"><img src="./img.php{$session_id}" id="L_image" onclick="reloadImage(this);"></label>
				<input class="form-control" type="text" id="Limage" name="Limage" autocomplete="off"></div>
				</div>
{/if}
				<hr>
				<input type="hidden" name="action" value="login">
				<div class="text-center"><button type="submit" class="btn btn btn-success w-75">{$vm.login_button}</span></button></div>
			</form>

			<hr>
			<h2>{$vm.new_account}</h2>
			<p>{$vm.new_account_text}</p>
			<input type="button" onClick="document.location='./?{$session_id}&action=show_create'" class="btn btn btn-success w-75" value="{$vm.create_button}" />
            
{include file="footer.tpl"}