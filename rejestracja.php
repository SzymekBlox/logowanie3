<?php

$db_host = "";
$db_user = "";
$db_password = "";
$db_database = "";

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "login") {
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    //obiektowo
    $db = mysqli_connect($db_host, $db_user, $db_password, $db_database);
    //var_dump($db);

    //strukturalnie 
    //$d = mysqli_connect("localhost", "root", "", "auth");
    //mysqli_query($d, "SELECT * FROM user");


    //ręcznie:
    //$q = "SELECT * FROM user WHERE email = '$email'";
    //echo $q;
    //$db->query($q);

    //prepared statements
    $q = $db->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
    //podstaw wartości
    $q->bind_param("s", $email);
    //wykonaj
    $q->execute();
    $result = $q->get_result();

    $userRow = $result->fetch_assoc();
    if ($userRow == null) {
        //konto nie istnieje
        echo "Błędny login lub hasło <br>";
    } else {
        //konto istnieje
        if (password_verify($password, $userRow['passwordHash'])) {
            //hasło poprawne
            echo "Zalogowano poprawnie <br>";
        } else {
            //hasło niepoprawne
            echo "Błędny login lub hasło <br>";
        }
    }
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "register") {

    if(empty($_REQUEST['email']) || empty($_REQUEST['password']) || empty($_REQUEST['passwordRepeat']))
        die("Brak danych");

    //rejestracja nowego użytkownika
    $db = mysqli_connect($db_host, $db_user, $db_password, $db_database);
    //var_dump($db);
    $email = $_REQUEST['email'];
    //wyczyść email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $password = $_REQUEST['password'];
    $passwordRepeat = $_REQUEST['passwordRepeat'];
    $firstName = $_REQUEST['firstName'];
    $lastName = $_REQUEST['lastName'];

    if($password == $passwordRepeat) {
        //hasła są identyczne - kontynuuj
        $q = $db->prepare("INSERT INTO user VALUES (NULL, ?, ?, ?, ?)");
        $passwordHash = password_hash($password, PASSWORD_ARGON2I);
        $q->bind_param("ssss", $email, $passwordHash, $firstName, $lastName);
        $result = $q->execute();
        if($result) {
            echo "Konto utworzono poprawnie"; 
        } else {
            echo "Coś poszło nie tak!";
        }
    } else {

        echo "Hasła nie są zgodne - spróbuj ponownie!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>MtBank - Rejestracja</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="logowanie.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <div class="login-title">
                Rejestracja
            </div>
            <form>
                <div class="login-input">
                    <label class="login-input-info3" for="fisrtName">Imię:</label>
                    <input type="text" name="firstName" id="firstName" required>
                </div>
                <div class="login-input">
                    <label class="login-input-info4" for="lastName">Nazwisko:</label>
                    <input type="text" name="lastName" id="lastName" required>
                </div>
                <div class="login-input">
                    <label class="login-input-info" for="emailInput">Email:</label>
                    <input type="email" name="email" id="emailInput" required>
                </div>    
                <div class="login-input">
                    <label class="login-input-info" for="passwordInput">Hasło:</label>
                    <input type="password" name="password" id="passwordInput" required>
                </div>
                <div class="login-input">
                    <label class="login-input-info2" for="passwordInput">Powtórz hasło:</label>
                    <input type="password" name="passwordRepeat" id="passwordInput" required>
                </div>
                <input type="hidden" name="action" value="register">
                <input class="btn" type="submit" value="Zarejestruj">
                <p>Masz konto? <a href="logowanie.php">Zaloguj się</a></p>
            </form>
        </div>
    </div>
</body>
</html>