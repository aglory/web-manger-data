<?php
	if(!defined('Execute')) exit();
	CurrentUserId(0);
	header("Location: ".ActionLink('account','login',true));
	exit();