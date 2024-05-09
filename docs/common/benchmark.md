

<h1>Benchmark</h1>

<h2>Examples</h2>

<div class="example">
<h3>Example #1</h3>
<pre>
use ujb\common\Benchmark;

$b = new Benchmark();
sleep(1);
echo $b->stop();
echo $b->getMemoryAtStart();
echo $b->getMemoryAtStop();
echo $b->getDuration();

print_r($b->getDetails());

Result:
1.0000009536743
423792
430832
1.0000009536743

Array
(
    [startTime] => 1715163424.1389
    [stopTime] => 1715163425.1389
    [duration] => 1.0000009536743
    [memoryAtStart] => 423792
    [memoryAtStop] => 430832
)
</pre>
</div>


<div class="example">
<h3>Example #2</h3>
<pre>
use ujb\common\Benchmark;

function firstFunction() {
	sleep(2);
}
function secondFunction() {
	sleep(3);
}
function thirdFunction() {
	sleep(1);
}

$b = new Benchmark();

firstFunction();
echo $b->mark('firstFunction', 'Some additional optional values.');

secondFunction();
echo $b->mark('secondFunction');

thirdFunction();
echo $b->mark('thirdFunction');

$b->stop();

print_r($b->getMarks());

print_r($b->getDetails());

Result:
2.0000030994415
5.0000069141388
6.000009059906

Array
(
    [firstFunction] => Array
        (
            [time] => 1715164079.9495
            [duration] => 2.0000030994415
            [extra] => Some additional optional values.
            [memory] => 430928
        )

    [secondFunction] => Array
        (
            [time] => 1715164082.9495
            [duration] => 5.0000069141388
            [extra] => 
            [memory] => 432056
        )

    [thirdFunction] => Array
        (
            [time] => 1715164083.9495
            [duration] => 6.000009059906
            [extra] => 
            [memory] => 432808
        )

)

Array
(
    [startTime] => 1715164077.9495
    [stopTime] => 1715164083.9495
    [duration] => 6.000009059906
    [memoryAtStart] => 423888
    [memoryAtStop] => 433560
)
</pre>
</div>


<div class="example">
<h3>Example #3</h3>
<pre>
use ujb\common\Benchmark;

$b = new Benchmark();
$callback = function($i) {
	return $i;
};

$result = [];
for ($i = 0; $i < 1000000; $i++) {
	$result[] = $callback($i);
}
	
$b->stop();
</pre>


<p>The same result can be obtained using the Benchmark::do function. If the callback function sent to the "do" method returns a value, it will be stored in an array, so the total memory used for all iterations can be found.
</p>
<pre>
use ujb\common\Benchmark;

// $i iteration number
$b = Benchmark::do(function($i) {
	return $i;
}, 1000000);

print_r($b->getDetails());

Result:
Array
(
    [startTime] => 1715164754.2892
    [stopTime] => 1715164754.3892
    [duration] => 0.10000014305115
    [memoryAtStart] => 422400
    [memoryAtStop] => 36081104
)
</pre>


<p>In this case, the callback function sent to the "do" method does not return any value.</p>
<pre>
use ujb\common\Benchmark;

$b = Benchmark::do(function($i) {
	// ...
}, 1000000);

print_r($b->getDetails());

Result:
Array
(
    [startTime] => 1715164886.8728
    [stopTime] => 1715164886.9478
    [duration] => 0.075001001358032
    [memoryAtStart] => 422368
    [memoryAtStop] => 429408
)
</pre>
</div>
