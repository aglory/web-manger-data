<?php
	if(!defined('Execute')) exit();
	CurrentUserId(0);
	header("Location: ".ActionLink('home','index',true));
	exit();