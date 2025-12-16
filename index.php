<?php
// Simple access gate: if user already logged in, send to AHP summary,
// otherwise redirect to login page.
session_start();

// If user is logged in, forward to the appropriate landing page (AHP summary)
if (!empty($_SESSION['user_email'])) {
	// Logged-in users see the AHP summary by default
	header('Location: ahp/ahp_result_summary.php');
	exit();
}

// Not logged in -> send to login page
header('Location: ahp/login.php');
exit();