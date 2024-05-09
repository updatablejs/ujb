

<h1>Chronometer</h1>

<h2>Examples</h2>

<div class="example">
<h3>Example #1</h3>
<pre>
use ujb\common\Chronometer;

$c = new Chronometer();
sleep(1);
echo $c->stop();

Result: 1.0000019073486;
</pre>
</div>


<div class="example">
<h3>Example #2</h3>
<pre>
use ujb\common\Chronometer;

function firstFunction() {
	sleep(2);
}
function secondFunction() {
	sleep(3);
}
function thirdFunction() {
	sleep(1);
}

$c = new Chronometer();

firstFunction();
echo $c->mark('firstFunction', 'Some additional optional values.');

secondFunction();
echo $c->mark('secondFunction');

thirdFunction();
echo $c->mark('thirdFunction');

print_r($c->getMarks());

Result:

2.0000028610229
5.0000069141388
6.0000088214874

Array
(
    [firstFunction] => Array
        (
            [time] => 1715066632.0854
            [difference] => 2.0000028610229
            [extra] => Some additional optional values.
        )

    [secondFunction] => Array
        (
            [time] => 1715066635.0854
            [difference] => 5.0000069141388
            [extra] => 
        )

    [thirdFunction] => Array
        (
            [time] => 1715066636.0854
            [difference] => 6.0000088214874
            [extra] => 
        )

)
</pre>
</div>
