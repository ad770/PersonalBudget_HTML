<?php

session_start();

if (!isset($_SESSION['logged'])) {
    header('Location: index.php');
    exit();
} else {
    $user_id = $_SESSION['id'];

    require_once "database.php";
    $payment_query = $db->prepare("SELECT * FROM payment_methods_assigned_to_users WHERE user_id='$user_id'");
    $payment_query->execute();
    $cat_query = $db->prepare("SELECT * FROM expenses_category_assigned_to_users WHERE user_id='$user_id'");
    $cat_query->execute();

    if (isset($_POST['expense'])) {
        $is_good = true;
        $expense_value = $_POST['expense'];
        if ($expense_value < 0) {
            $is_good = false;
            $_SESSION['e_expense_value'] = "Wartość musi być większa od 0!";
        }

        if (filter_var($expense_value, FILTER_VALIDATE_FLOAT) == false) {
            $is_good = false;
            $_SESSION['e_expense_value'] = "Wprowadź liczbę!";
        }
        $expense_category = $_POST['expense_cat'];
        $expense_category_query = $db->prepare("SELECT id FROM expenses_category_assigned_to_users WHERE user_id='$user_id' AND name='$expense_category'");
        $expense_category_query->execute();
        $expense_category_result = $expense_category_query->fetch(PDO::FETCH_ASSOC);
        $expense_id_cat = $expense_category_result['id'];

        $payment_method = $_POST['expense_payment'];
        $payment_methods_query = $db->prepare("SELECT id FROM payment_methods_assigned_to_users WHERE user_id='$user_id' AND name='$payment_method'");
        $payment_methods_query->execute();
        $payment_methods_result = $payment_methods_query->fetch(PDO::FETCH_ASSOC);
        $payment_id_cat = $payment_methods_result['id'];

        $amount = $_POST['expense'];
        $date_of_expense = $_POST['input_date'];
        $expense_comment = $_POST['expense_comment'];

        $statement = $db->prepare("INSERT INTO expenses VALUES (NULL, '$user_id', '$expense_id_cat', '$payment_id_cat', '$amount', '$date_of_expense', '$expense_comment')");
        if ($statement->execute()) {
            $_SESSION['successful_submit'] = true;
        } else {
            echo " Nie udało się!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="my_styles.css">
</head>

<body>

    <nav class="navbar navbar-dark bg-dark justify-content-start p-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="main.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-bank2" viewBox="0 0 15 15">
                    <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916l-7.5-5zM12.375 6v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1H.5z" />
                </svg> System zarządzania budżetem osobisym</a>
            <div class="navbar-nav">
                <a class="nav-link" href="#settings.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-gear" viewBox="0 0 15 15">
                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                    </svg> Ustawienia</a>
                <a class="nav-link" href="logout.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-power" viewBox="0 0 15 15">
                        <path d="M7.5 1v7h1V1h-1z" />
                        <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z" />
                    </svg> Wyloguj się</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="nav-item">
            <div class="row">
                <div class="col-10 col-sm-10 col-md-8 col-lg-7 mx-auto">
                    <ul class="nav nav-tabs nav-justified text-center">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="main.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-house" viewBox="0 0 15 15">
                                    <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                                    <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
                                </svg> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="income.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 15 15">
                                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z" />
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg> Dodaj przychód</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link disabled" href="expense.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-cart-dash" viewBox="0 0 15 15">
                                    <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z" />
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg> Dodaj wydatek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="balance.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-shuffle" viewBox="0 0 15 15">
                                    <path fill-rule="evenodd" d="M0 3.5A.5.5 0 0 1 .5 3H1c2.202 0 3.827 1.24 4.874 2.418.49.552.865 1.102 1.126 1.532.26-.43.636-.98 1.126-1.532C9.173 4.24 10.798 3 13 3v1c-1.798 0-3.173 1.01-4.126 2.082A9.624 9.624 0 0 0 7.556 8a9.624 9.624 0 0 0 1.317 1.918C9.828 10.99 11.204 12 13 12v1c-2.202 0-3.827-1.24-4.874-2.418A10.595 10.595 0 0 1 7 9.05c-.26.43-.636.98-1.126 1.532C4.827 11.76 3.202 13 1 13H.5a.5.5 0 0 1 0-1H1c1.798 0 3.173-1.01 4.126-2.082A9.624 9.624 0 0 0 6.444 8a9.624 9.624 0 0 0-1.317-1.918C4.172 5.01 2.796 4 1 4H.5a.5.5 0 0 1-.5-.5z" />
                                    <path d="M13 5.466V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192zm0 9v-3.932a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192z" />
                                </svg> Pokaż bilans</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="budget_panel p-2 d-flex justify-content-center">
            <div class=" row col-10 col-sm-10 col-md-8 col-lg-6 col-xlg=6 m-0 p-0 border rounded-3 text-dark">
                <form method="post">
                    <?php
                    if (isset($_SESSION['successful_submit'])) {
                        echo "<div id='hide_message' class='row col-9 p-2 mx-auto my-2 rounded-pill alert alert-success text-center col-3 justify-content-center center' role='alert'> Wydatek został poprawnie dodany!</div>";
                        unset($_SESSION['successful_submit']);
                    }
                    ?>
                    <div class="row w-75 mx-auto my-2 mt-3">
                        <label for="expense"></label>
                        <input type="number" step="0.01" min="0" class="rounded-pill" name="expense" id="expense" placeholder="100,00 zł" required>
                    </div>
                    <?php
                    if (isset($_SESSION['e_expense_value'])) {
                        echo '<div class="error">' . $_SESSION['e_expense_value'] . '</div>';
                        unset($_SESSION['e_expense_value']);
                    }
                    ?>
                    <div class="row w-75 mx-auto my-2">
                        <label for="input_date"></label>
                        <input type="date" class="rounded-pill" name="input_date" id="input_date" required>
                    </div>
                    <div class="row w-75 mx-auto my-2">
                        <label for="expense_payment"></label>
                        <select name="expense_payment" id="expense_payment" class="rounded-pill">
                            <option disabled selected>Wybierz metodę płatności</option>
                            <?php
                            while ($payment_result = $payment_query->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $payment_result['name'] . "''>" . $payment_result['name'] . "</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="row w-75 mx-auto my-2">
                        <label for="expense_cat"></label>
                        <select name="expense_cat" id="expense_cat" class="rounded-pill">
                            <option disabled selected>Wybierz kategorię płatności</option>
                            <?php
                            while ($cat_results = $cat_query->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $cat_results['name'] . "''>" . $cat_results['name'] . "</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="row w-75 mx-auto my-2">
                        <label for="expense_comment"></label>
                        <textarea name="expense_comment" cols="20" rows="5" class="rounded" id="expense_comment" placeholder="Uwagi (opcjonalnie)"></textarea>
                    </div>
                    <div class="row w-75 mx-auto my-2 mb-3">
                        <div class="buttons text-center">
                            <button type="submit" class="rounded-pill w-50">Dodaj</button>
                        </div>
                    </div>
                </form>
            </div>
            </main>
            <script>
                let date = new Date();
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();

                if (month < 10) month = "0" + month;
                if (day < 10) day = "0" + day;

                let today = year + "-" + month + "-" + day;

                document.getElementById('input_date').value = today;
            </script>
            <script>
                function hide() {
                    document.getElementById("hide_message").style.display = "none";
                }
                setInterval("hide();", 3000);
            </script>
        </div>

        <footer class="page-footer fixed-bottom text-center bg-dark text-white">2022 &#169; Adrian Żuchowski</footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>

</html>