let a = 7;
let b = 12;

[a, b] = [b, a];

console.log("a =", a);
console.log("b =", b);







//maybeee ?

let aa = 8 ;
let bb = 12 ;

let t = a ;
a = b;
b = t ;

console.log( "a   -" ,  a )

console.log( "a   -" ,  b )

//



//did not work smh


let aaa = 8 ;
let bbb = 12 ;



const arr3 = [aaa,bbb] ;

for (let i = 2; i <= 1 ; i--)
{
    console.log( arr3[i]);
}





//









function square(n) {
    return n * n;
}

for (let i = 1; i <= 10; i++)
{
    console.log(`square(${i}) =`, square(i));
}







const arr = [14, 5, 7, 839, 12, 516];

let l = arr[0];

for (let i = 1; i < arr.length; i++)


{
    if (arr[i] > l)
    {
     l = arr[i];
    }
}

console.log("large "  , l);



