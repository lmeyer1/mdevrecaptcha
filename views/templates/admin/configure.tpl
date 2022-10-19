{*
* 2007-2021 MaluDev
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@MaluDev.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade MaluDev to newer
* versions in the future. If you wish to customize MaluDev for your
* needs please refer to http://www.MaluDev.com for more information.
*
*  @author    MaluDev SA <contact@MaluDev.com>
*  @copyright 2007-2021 MaluDev SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of MaluDev SA
*}

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			{l s='Google Re Captcha Configuration' mod='mdevrecaptcha'}
		</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-info" role="alert">

						<h4>
							{l s='First steps:' mod='mdevrecaptcha'}
						</h4>

						<p>
							{l s='1- Register your Web Site in Google reCaptcha v3 Admin Console page.' mod='mdevrecaptcha'}
							(https://www.google.com/recaptcha/intro/v3.html)
						</p>

						<p>
							{l s='2- Copy both Site Key and Secret Key in the inputs bellow.' mod='mdevrecaptcha'}
						</p>

						<p>
							{l s='3- Enable reCaptcha in the available sections using the checkboxes bellow.' mod='mdevrecaptcha'}
						</p>

					</div>
				</div>
			</div>

			{if $is_saved}
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-success" role="alert">
						{l s='Changes saved' mod='mdevrecaptcha'}
					</div>
				</div>
			</div>
			{/if}

			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="webkey">{l s='Site Key' mod='mdevrecaptcha'}</label>
						<input type="text" id="webkey" name="webkey" class="form-control" value="{$webkey}" autocomplete="off">
					</div>

					<div class="form-group">
						<label for="secretkey">{l s='Secret Key' mod='mdevrecaptcha'}</label>
						<input type="text" id="secretkey" name="secretkey" class="form-control" value="{$secretkey}" autocomplete="off">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<h2>{l s='Use reCaptcha in:' mod='mdevrecaptcha'}</h2>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="use_in_login" value="login"{if $useLogin} checked{/if}> <strong>{l s='Login' mod='mdevrecaptcha'}</strong>
						</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="use_in_registration" value="registration"{if $useRegistration} checked{/if}> <strong>{l s='Registration' mod='mdevrecaptcha'}</strong>
						</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="use_in_contact" value="contact"{if $useContact} checked{/if}> <strong>{l s='Contact Form' mod='mdevrecaptcha'}</strong>
						</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="use_in_postcomment" value="postcomment"{if $usePostcomment} checked{/if}> <strong>{l s='Product Post comment' mod='mdevrecaptcha'}</strong>
						</label>
					</div>
				</div>
			</div>

		</div>
		<div class="panel-footer">
			<button type="submit" name="save" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mdevrecaptcha'}
			</button>
		</div>
	</div>
</form>
