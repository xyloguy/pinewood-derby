<?php
include_once(dirname(__FILE__) . '/functions.php');
include_once(dirname(__FILE__) . '/session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinewood Derby Organizer</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="/css/bootstrap-icons/font/bootstrap-icons.min.css">
    <style>
        #head_h1 a{
            color: white !important;
            text-decoration: none !important;
        }

        table th {
            width: auto !important;
        }

        #results td.click-area[role="button"] *:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>
<header class="container-fluid bg-dark text-white py-3">
    <div class="container">
        <h1 id="head_h1"><a href="/">Pinewood Derby Manager</a></h1>
    </div>
</header>

<main class="container">

<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="groups.php">Groups</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="racers.php">Racers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="heats.php">Heats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="score.php">Scoring</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="results.php">Results</a>
            </li>
        </ul>
    </div>
</nav>


