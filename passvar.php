<?php session_start(); 	

if ($_POST['aside']) { $_SESSION['aside'] = $_POST['aside']; } 
if ($_POST['retract']) { $_SESSION['retract'] = $_POST['retract']; } 
if ($_POST['expand']) { $_SESSION['expand'] = $_POST['expand']; } 
if ($_POST['settings']) { $_SESSION['settings'] = $_POST['settings']; }  
if ($_POST['closesettingstitle']) { $_SESSION['closesettingstitle'] = $_POST['closesettingstitle']; } 
if ($_POST['settingstitle']) { $_SESSION['settingstitle'] = $_POST['settingstitle']; } 
if ($_POST['width']) { $_SESSION['width'] = $_POST['width']; }   	        	     
?>