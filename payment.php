<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/css/all.min.css" />
    <script src="main.js" defer></script>
    <link rel="stylesheet" href="css/pay.css" />
    <title>Payment Form</title>
    <script type="text/javascript">
        function preventBack() {
            window.history.forward(); 
        }
          
        setTimeout("preventBack()", 0);
          
        window.onunload = function () { null };
    </script>
</head>
<body>
<?php
require_once('connection.php');
session_start();
$email = $_SESSION['email'];

$sql = "SELECT * FROM booking WHERE EMAIL='$email' ORDER BY BOOK_ID DESC LIMIT 1";
$cname = mysqli_query($con, $sql);
$email = mysqli_fetch_assoc($cname);
$bid = $email['BOOK_ID'];
$_SESSION['bid'] = $bid;

if(isset($_POST['pay'])){
    $cardno = mysqli_real_escape_string($con, $_POST['cardno']);
    $exp = mysqli_real_escape_string($con, $_POST['exp']);
    $cvv = mysqli_real_escape_string($con, $_POST['cvv']);
    $price = $email['PRICE'];
    if(empty($cardno) || empty($exp) || empty($cvv) ){
        echo '<script>showDialog("Please fill all fields")</script>';
    }
    else{
        $sql2 = "INSERT INTO payment (BOOK_ID, CARD_NO, EXP_DATE, CVV, PRICE) VALUES ($bid, '$cardno', '$exp', $cvv, $price)";
        $result = mysqli_query($con, $sql2);
        if($result){
            header("Location: psucess.php");
            exit();
        }
    }
}
?>

    <h2 class="payment">TOTAL PAYMENT : <a>Rs<?php echo $email['PRICE']; ?>/-</a></h2>

    <div class="card">
        <form method="POST">
            <h1 class="card__title">Enter Payment Information</h1>
            <div class="card__row">
                <div class="card__col">
                    <label for="cardNumber" class="card__label">Card Number</label>
                    <input type="text" class="card__input card__input--large" id="cardNumber" placeholder="xxxx-xxxx-xxxx-xxxx" required name="cardno" maxlength="16" />
                </div>
                <div class="card__col card__chip">
                    <img src="images/chip.svg" alt="chip" />
                </div>
            </div>
            <div class="card__row">
                <div class="card__col">
                    <label for="cardExpiry" class="card__label">Expiry Date</label>
                    <input type="text" class="card__input" id="cardExpiry" placeholder="xx/xx" required name="exp" maxlength="5" />
                </div>
                <div class="card__col">
                    <label for="cardCcv" class="card__label">CCV</label>
                    <input type="password" class="card__input" id="cardCcv" placeholder="xxx" required name="cvv" maxlength="3" />
                </div>
                <div class="card__col card__brand"><i id="cardBrand"></i></div>
            </div>
            <input type="submit" value="PAY NOW" class="pay" name="pay">
            <button class="btn"><a href="cancelbooking.php">CANCEL</a></button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="main.js"></script>
</body>
</html>