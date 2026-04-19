<?php
$length = 10;
$width = 5;
$area = $length * $width;
echo "The area of the rectangle is: " . $area;
$perimeter = 2 * ($length + $width);
echo "The perimeter of the rectangle is: " . $perimeter;
?>


<?php
$amount = 1000;
$vat_rate = 0.15;
$vat_amount = $amount * $vat_rate;  
echo "The VAT amount is: " . $vat_amount;
?>


<?php$number = 5;
if ($number % 2 == 0) {
    echo "The number is even.";
} else {
    echo "The number is odd.";
}  
?>

<?php
$a=10;
$b=20;
$c=30;
if ($a >= $b && $a >= $c) {
    echo "The largest number is: $a" ;
} elseif ($b > $a && $b > $c) {
    echo "The largest number is: $b" ;
} else {
    echo "The largest number is: $c" ;
}
?>


<?php
for($i=10; $i<=100; $i++){
    if($i%2 !=0){
        echo $i . " ";
    }       
}   
?>


